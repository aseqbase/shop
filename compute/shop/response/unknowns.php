<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
$successAction = "compute/shop/response/succeed";
$errorAction = "compute/shop/response/failed";
$trackAction = "compute/shop/response/track";

$piId = table("Invoice")->Insert([
    "UserId" => \_::$User->Id,
    "Name" => \_::$Joint->Finance->ShownUnknownPrice,
    "Title" => "'" . \_::$Joint->Finance->ShownUnknownPrice . "' Invoice",
    "Description" => \_::$User->GetMetaValue("CartDescription"),
    "Content" => __("Please wait to indicate the price of this invoice!"),
    "Relation" => \_::$Joint->Shop->RequestsUrlPath,
    "Source" => \_::$User->Id,
    "SourceData" => Convert::ToJson([
        ...(($v = \_::$User->GetValue("Contact")) ? ["Phone" => $v] : []),
        ...(($v = \_::$User->Email) ? ["Email" => $v] : [])
    ]),
    "Amount" => null,
    "Destination" => \_::$Joint->Finance->PlatformAccount,
    "DestinationData" => Convert::ToJson([
        "Success" => $successAction,
        "Error" => $errorAction
    ]),
    "MetaData" => [
        "Run" => [
            "Name" => $trackAction,
            "Data" => $data
        ]
    ]
]);
$rows = table("Shop_Request")->Get($data);
foreach ($rows as $key => $value) 
    $rows[$key]["Status"] = \_::$Joint->Shop->UncheckedStatus;
if(table("Shop_Response")->Insert($rows))
    table("Shop_Request")->Del($data);

$rel = \_::$Joint->Finance->InvoiceUrlPath . "?Id=" . $piId;
return [
    "Description"=>Struct::Center(Struct::Button("Track the related 'Invoice'", $rel, ["class"=>"main"])),
    "Relation"=>$rel
];