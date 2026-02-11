<?php
if(\_::$User->HasAccess(\_::$User->AdminAccess) && isset(\_::$Front->MainMenus["Admin-Main"])){
    \_::$Front->MainMenus["Admin-Content"]["Items"] = \_::$Front->SideMenus["Admin-Content"]["Items"] = [
        array("Name" => "MERCHANDISES", "Path" => "/admin/content/merchandises", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the merchandises and items", "Image" => "box"),
        ...\_::$Front->MainMenus["Admin-Content"]["Items"]
    ];
    \_::$Front->MainMenus["Admin-User"]["Items"] = \_::$Front->SideMenus["Admin-Content"]["Items"] = [
        array("Name" => "REQUESTS", "Path" => "/admin/user/requests", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the user requests", "Image" => "shopping-basket"),
        array("Name" => "RESPONSES", "Path" => "/admin/user/responses", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the user responses", "Image" => "truck"),
        ...\_::$Front->MainMenus["Admin-User"]["Items"],
        array("Name" => "PAYMENTS", "Path" => "/admin/user/payments", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the website's payments", "Image" => "credit-card")
    ];
}

// To route other requests to the DefaultRouteName
\_::$Router->On()->Default(\_::$Front->DefaultRouteName);