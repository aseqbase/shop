<?php

use MiMFa\Library\Contact;
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
function RequestToResponse($row)
{
    unset($row["Like"]);
    unset($row["Request"]);
    return $row;
}
compute("request/base");
if ($pid = get($data, "PaymentId")) {
    $collection = table("Payment")->SelectValue("Relation", "Verify IS TRUE AND Id=:Id", [":Id" => $pid]);
    if (isEmpty($collection)) {
        renderWarning("Your order will be shipped to you once your transaction is confirmed!" . Html::$Break . "This may take up to 24 hours.");
        return false;
    }
    $rows = table("Request")->As("R")->Join(table("Merchandise")->As("M"))
        ->Select(
            "*, R.Id AS Id, R.Count AS Count, M.Id AS MerchandiseId, M.Count AS MerchandiseCount",
            "R.Collection=:Collection",
            [":Collection" => $collection]
        );
    $hasres = 0;
    foreach ($rows as $row) {
        $subject = get($row, "Subject");
        $title = get($row, "Subject");
        $description = get($row, "Description");
        $id = grab($row, "Id");
        $mId = get($row, "MerchandiseId");
        $count = get($row, "Count");
        $mCount = get($row, "MerchandiseCount");
        $address = get($row, "Address");
        $isDigital = get($row, "Digital");
        // Create Private Content
        $result = [];
        $c = min($count, $mCount);
        $res = null;
        do {
            if ($res = get($row, "PrivateTitle"))
                $result[] = Html::Heading($subject = Convert::FromDynamicString($res));
            if ($res = get($row, "PrivateMessage"))
                $result[] = Convert::FromDynamicString($res);
            if ($res = get($row, "PrivateAttach"))
                $result[] = Html::Items(Convert::FromJson($res));
            if ($res = get($row, "PrivateGenerator")) {
                $result[] = !isUrl($res) ? (isScript($res) ? eval ($res) : $res) : sendPost(getFullUrl($res), $row);
                $result[] = Html::$BreakLine;
            }
        } while ($res && --$c > 0);
        // Checkout
        $row = table("Request")->SelectRow("*", "Id=:Id", [":Id" => $id]);
        if ($mCount) {
            if ($mCount >= $count) {
                if (table("Merchandise")->Update("Id=:Id", [":Id" => $mid, ":Count" => $mCount - $count]))
                    $row["Status"] = 1;// Accepted
                else
                    $row["Status"] = -3;// Defected
            } else {
                $rrow = RequestToResponse($row);// Response Row
                $mrow = [":Id" => $mid, "Count" => 0];// Merchandise Row
                $price = $row["Price"] / $count;
                $row["Status"] = 1;// Accepted
                $row["Count"] = $mCount;
                $row["Price"] = $price * $row["Count"];
                $rrow["Status"] = -2;// Unavailable
                $rrow["Count"] = $count - $mCount;
                $rrow["Price"] = $price * $rrow["Count"];
                unset($rrow["Id"]);
                if (
                    \_::$Back->DataBase->Transaction(
                        [
                            table("Merchandise")->UpdateQuery("Id=:Id", $mrow),
                            table("Request")->UpdateQuery("Id=:Id", $row),
                            table("Response")->InsertQuery($rrow)
                        ],
                        [$mrow, $row, $rrow]
                    )
                )
                    $row["Status"] = 1;// Accepted
                else
                    $row["Status"] = -3;// Defected
            }
        } elseif ($isDigital)
            $row["Status"] = 1;// Digital Accepted
        else {
            $row["Status"] = -2;// Physical Unavailable
        }
        // Send Digital Information
        if ($row["Status"] > 0 && $result) {
            $content = Convert::ToString($result);
            if ($isDigital)
                $row["Status"] = 2;// Digital Prepared
            if (
                Contact::SendHtmlEmail(
                    \_::$Info->SenderEmail,
                    $isDigital ? $address : \_::$Back->User->Email,
                    $subject,
                    $content
                )
                && $isDigital
            )
                $row["Status"] = 3;// Digital Sent

            if ($title || $description || $content) {
                render(Html::Page(
                    ($title ? Html::SuperHeading($title) : "") .
                    ($description ? Html::Paragraph($description) : "") .
                    $content
                ));
                if ($isDigital)
                    $row["Status"] = 4;// Digital Received
            }
            if ($content)
                $row["Content"] = $content;
        }
        $row = RequestToResponse($row);
        if (
            \_::$Back->DataBase->Transaction([
                table("Request")->DeleteQuery("Id=:Id"),
                table("Response")->InsertQuery($row)
            ], [[":Id" => $id], $row])
        )
            $hasres++;
        //if(table("Response")->Insert($row) && table("Request")->Delete("Id=:Id",[":Id"=>$id])) $hasres++;
    }
    return $hasres;
}
return false;
?>