<?php
use MiMFa\Library\Contact;
use MiMFa\Library\Convert;
use MiMFa\Library\MetaDataTable;
use MiMFa\Library\Struct;
auth(\_::$Joint->Shop->BuyingAccess);
$id = received("Id");
$state = received("State");
$desc = received("Description");

$res = table("Shop_Response")->Get($id);
$status = get($res, "Status");
$msg = null;
switch ($state) {
    case 1:
        $si = \_::$Joint->Shop->StatusToIInt($status);
        $row = table("Shop_Merchandise")->Get($res["MerchandiseId"]);
        $d = $row["Digital"] ?? \_::$Joint->Shop->DigitalStore;
        if (
            ($d && $si >= \_::$Joint->Shop->StatusToIInt(\_::$Joint->Shop->DigitalResponseStatus)) ||
            (!$d && $si >= \_::$Joint->Shop->StatusToIInt(\_::$Joint->Shop->PhysicalResponseStatus))
        ) {
            $m = "";
            if ($p = get($res, "Private"))
                $msg = fn() => deliverModal($p);
            else {
                $res["Private"] = compute("shop/response/privates", [
                    "Merchandise"=>$row,
                    "Request"=>$res,
                    ]);
                if ($d && $status !== \_::$Joint->Shop->DigitalFinalStatus) {
                    library("Contact");
                    if (
                        !Contact::SendHtmlEmail(
                            \_::$User->SenderEmail,
                            $email = $d ? $res["Address"] : \_::$User->Email,
                            Convert::FromDynamicString(get($row, "PrivateSubject"))??get($row, "Title"),
                            $res["Private"]
                        )
                    )
                        $m .= Struct::Warning("Could not email to the '$email'!");
                    $status = \_::$Joint->Shop->DigitalFinalStatus;
                }
                $msg = fn() => deliverModal($m.$res["Private"]);
            }
        } else
            return deliverError("Could not deliver the merchandise!");
        break;
    case -10:
        $status .= ":Returning";
        $msg = "The 'merchandise' will return in 72 hours later!";
        break;
    case 10:
        $status = preg_find("/^[A-Za-z]+\b/", $status);
        $msg = "Your 'merchandise' will send to you!";
        break;
    default:
        break;
}
if ($id) {
    $md = Convert::FromJson(get($res, "MetaData"));
    library("MetaDataTable");
    $MDT = new MetaDataTable(null, "Shop_Response");
    $MDT->AddProcedure($res, $md, $status, $desc);
    if (
        $MDT->Set($id, [
            "UpdateTime" => Convert::ToDateTimeString(),
            "Status" => $status,
            "Description" => $desc,
            "Private" => $res["Private"],
            "MetaData" => $md
        ])
    )
        if (is_callable($msg))
            return $msg();
        else
            return deliverRedirect(Struct::Success($msg));
    else
        return deliverError("Could not change the '$table' status!");
} else
    return deliverError("Something went wrong!");