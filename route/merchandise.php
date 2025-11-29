<?php
$ctable = table("Content");
$mtable = table("Merchandise");
$rtable = table("Request");
route("content", [
    "Compute" => [
        "ComputeName" => "content/merchandise",
        "ContentTable" => $ctable,
        "MerchandiseTable" => $mtable,
        "RequestTable" => $rtable,
    ],
    "View" => [
        "Part" => "content/merchandise",
        "Root" => "/item/",
        "CollectionRoot" => "/items/",
        "CheckAccess" => function ($item) {
            return \_::$User->HasAccess(\_::$User->AdminAccess) || \_::$User->HasAccess(\MiMFa\Library\Convert::ToSequence(\MiMFa\Library\Convert::FromJson(getValid($item, 'Access', \_::$User->VisitAccess))));
        }
    ],
    "ErrorHandler" => "Could not find related merchandise"
]);

?>