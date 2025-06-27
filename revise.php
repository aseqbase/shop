<?php
if(auth(\_::$Config->AdminAccess) && isset(\_::$Info->MainMenus["Admin-Main"])){
    \_::$Info->MainMenus["Admin-Content"]["Items"] = \_::$Info->SideMenus["Admin-Content"]["Items"] = [
        array("Name" => "MERCHANDISES", "Path" => "/admin/content/merchandises", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all the merchandises and items", "Image" => "box"),
        ...\_::$Info->MainMenus["Admin-Content"]["Items"]
    ];
    \_::$Info->MainMenus["Admin-User"]["Items"] = \_::$Info->SideMenus["Admin-Content"]["Items"] = [
        array("Name" => "REQUESTS", "Path" => "/admin/user/requests", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all the user requests", "Image" => "shopping-basket"),
        array("Name" => "RESPONSES", "Path" => "/admin/user/responses", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all the user responses", "Image" => "truck"),
        ...\_::$Info->MainMenus["Admin-User"]["Items"],
        array("Name" => "PAYMENTS", "Path" => "/admin/user/payments", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all the website's payments", "Image" => "credit-card")
    ];
}
?>