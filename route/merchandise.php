<?php
$ctable = table("Content");
$mtable = table("Merchandise");
$rtable = table("Request");
route("content", [
    "Logic" => [
        "LogicName" => "content/merchandise",
        "ContentTable" => $ctable,
        "MerchandiseTable" => $mtable,
        "RequestTable" => $rtable,
    ],
    "View" => [
        "Part" => "content/merchandise",
        "RootRoute" => "/item/",
        "CollectionRoute" => "/items/",
        "CheckAccess" => function ($item) {
            return \_::$Back->User->Access(\_::$Config->AdminAccess) || \_::$Back->User->Access(\MiMFa\Library\Convert::ToSequence(\MiMFa\Library\Convert::FromJson(getValid($item, 'Access', \_::$Config->VisitAccess))));
        }
    ],
    "ErrorHandler" => "Could not find related merchandise"
]);

?>