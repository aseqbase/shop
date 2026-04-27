<?php

use MiMFa\Library\Contact;
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;

if ($collection = get($data, "Collection")) {
    $rows = table("Request")->As("R")->Join(table("Merchandise")->As("M"))
        ->Select(
            "*, R.Id AS Id, R.Count AS Count, R.Amount AS Amount,
            M.Id AS MerchandiseId, M.Count AS MerchandiseCount,
            M.Total AS MerchandiseTotal, M.Volume AS MerchandiseVolume",
            "R.Collection=:Collection",
            [":Collection" => $collection]
        );
    $hasres = 0;
    foreach ($rows as $row) {
        $subject = get($row, "Subject");
        $title = get($row, "Subject");
        $description = get($row, "Description");
        $id = get($row, "Id");
        $mId = get($row, "MerchandiseId");
        $count = get($row, "Count");
        $amount = get($row, "Amount");
        $mCount = get($row, "MerchandiseCount");
        $mTotal = get($row, "MerchandiseTotal");
        $mVol = get($row, "MerchandiseVolume");
        $address = get($row, "Address");
        $isDigital = get($row, "Digital") ?? \_::$Joint->Shop->DigitalStore;
        $initialStatus = $isDigital ? \_::$Joint->Shop->DigitalInitialStatus : \_::$Joint->Shop->PhysicalInitialStatus;

        // Checkout
        $req = table("Request")->SelectRow("*", "Id=:Id", [":Id" => $id]);
        if ($mCount) {
            if ($mCount >= $count) {
                if (
                    table("Merchandise")->Set($mId, [
                        ":Count" => $mCount - $count,
                        ":Total" => $mTotal + $count,
                        ":Volume" => $mVol + $amount,
                    ])
                )
                    $req["Status"] = $initialStatus;// Accepted
                else
                    $req["Status"] = \_::$Joint->Shop->DefectedStatus;// Defected
            } else {
                $res = $req;// Response Row
                $price = $req["Amount"] / $count;
                $req["Status"] = $initialStatus;// Accepted
                $req["Count"] = $mCount;
                $req["Amount"] = $price * $req["Count"];
                $res["Id"] = -1 * $req["Id"];
                $res["Status"] = \_::$Joint->Shop->UnavailableStatus;// Unavailable
                $res["Count"] = $count - $mCount;
                $res["Amount"] = $price * $res["Count"];
                $mer = [
                    ":Id" => $mId,
                    ":Count" => 0,
                    ":Total" => $mTotal + $req["Count"],
                    ":Volume" => $mVol + $req["Amount"]
                ];// Merchandise Row
                if (
                    \_::$Back->DataBase->Transaction(
                        [
                            table("Merchandise")->UpdateQuery("Id=:Id", $mer),
                            table("Request")->UpdateQuery("Id=:Id", $req),
                            table("Response")->InsertQuery($res)
                        ],
                        [$mer, $req, $res]
                    )
                )
                    $req["Status"] = $initialStatus;// Accepted
                else
                    $req["Status"] = \_::$Joint->Shop->DefectedStatus;// Defected
            }
        } elseif ($isDigital) {
            if (
                table("Merchandise")->Set($mId, [
                    ":Total" => $mTotal + $count,
                    ":Volume" => $mVol + $amount,
                ])
            )
                $req["Status"] = \_::$Joint->Shop->DigitalInitialStatus;// Digital Accepted
        } else
            $req["Status"] = \_::$Joint->Shop->UnavailableStatus;// Physical Unavailable

        // Send Digital Info
        // Create Private Content
        if (
            \_::$Joint->Shop->StatusToIInt($req["Status"]) > 0 &&
            $req["Private"] = compute("shop/response/privates", [
                "Merchandise" => $row,
                "Request" => $req,
            ])
        ) {
            if ($isDigital)
                $req["Status"] = \_::$Joint->Shop->PreparedStatus;// Digital Prepared
            if (
                Contact::SendHtmlEmail(
                    \_::$User->SenderEmail,
                    $isDigital ? $address : \_::$User->Email,
                    Convert::FromDynamicString(get($row, "PrivateSubject"))??$subject??get($row, "Title"),
                    $req["Private"]
                )
                && $isDigital
            )
                $req["Status"] = \_::$Joint->Shop->DigitalResponseStatus;// Digital Sent

            if ($title || $description) {
                response(
                    Struct::Page(
                        ($title ? Struct::Heading2($title) : "") .
                        ($description ? Struct::Paragraph($description) : "") . 
                        $req["Private"]
                    )
                );
                if ($isDigital)
                    $req["Status"] = \_::$Joint->Shop->ReceivedStatus;// Digital Received
            }
        }
        if (
            \_::$Back->DataBase->Transaction([
                table("Request")->DeleteQuery("Id=:Id"),
                table("Response")->InsertQuery($req)
            ], [[":Id" => $id], $req])
        )
            $hasres++;
    }
    return $hasres;
}
return false;