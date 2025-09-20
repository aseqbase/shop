<?php
$ctable = table("Content");
$mtable = table("Merchandise");
$rtable = table("Request");
route("contents", [
    "Compute" => [
        "ComputeName" => "content/merchandises",
        "ContentTable" => $ctable,
        "MerchandiseTable" => $mtable,
        "RequestTable" => $rtable,
        "Order" => "$ctable->Name.`UpdateTime` DESC"
    ],
    "View" => [
        "Part" => "content/merchandises",
        "RootRoute" => "/item/",
        "CollectionRoute" => "/items/",
        "DefaultTitle" => "All Merchandises",
        "Image" => "box",
        "Description" => "Browse between all merchandises",
        "CheckAccess" => function ($item) {
            return \_::$User->Access(\_::$Config->AdminAccess) || \_::$User->Access(\MiMFa\Library\Convert::ToSequence(\MiMFa\Library\Convert::FromJson(getValid($item, 'Access', \_::$Config->VisitAccess))));
        }
    ],
    "ErrorHandler" => "Could not find related merchandise"
]);

?>