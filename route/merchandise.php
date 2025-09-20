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
        "RootRoute" => "/item/",
        "CollectionRoute" => "/items/",
        "CheckAccess" => function ($item) {
            return \_::$User->Access(\_::$Config->AdminAccess) || \_::$User->Access(\MiMFa\Library\Convert::ToSequence(\MiMFa\Library\Convert::FromJson(getValid($item, 'Access', \_::$Config->VisitAccess))));
        }
    ],
    "ErrorHandler" => "Could not find related merchandise"
]);

?>