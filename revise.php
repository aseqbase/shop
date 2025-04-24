<?php
if(auth(\_::$Config->AdminAccess) && isset(\_::$Info->MainMenus["Admin-Main"])){
    \_::$Info->MainMenus["Admin-Content"]["Items"] = \_::$Info->SideMenus["Admin-Content"]["Items"] = [
        array("Name" => "MERCHANDISES", "Path" => "/admin/content/merchandises", "Access" => \_::$Config->AdminAccess, "Image" => "box"),
        ...\_::$Info->MainMenus["Admin-Content"]["Items"]
    ];
}
?>