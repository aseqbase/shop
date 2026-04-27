<?php
plugin("Shop");
\_::$Joint->Shop = new MiMFa\Plugin\Shop();

if (\_::$User->HasAccess(\_::$User->AdminAccess) && isset(\_::$Front->AdminMenus["Administrator"])) {
    \_::$Front->AdminMenus["Administrator-Shop"] =
        array(
            "Name" => "SHOP",
            "Path" => "/administrator/shop/merchandises",
            "Access" => \_::$User->AdminAccess,
            "Image" => "shop",
            "Items" => [
                array("Name" => "MERCHANDISES", "Path" => "/administrator/shop/merchandises", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the merchandises and items", "Image" => "box"),
                array("Name" => "REQUESTS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/shop/requests", "Description" => "To manage all the user 'requests'", "Image" => "truck"),
                array("Name" => "WAITS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/shop/waits", "Description" => "To manage all the user 'prerequests'", "Image" => "shopping-basket"),
                array("Name" => "WISHES", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/shop/groups", "Description" => "To manage all the user specified groups", "Image" => "heart"),
                array("Name" => "DISCOUNTS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/shop/discounts", "Description" => "To manage all the discounts", "Image" => "percent"),
                array("Name" => "CONFIGURATIONS", "Path" => "/administrator/shop/shop", "Access" => \_::$User->SuperAccess, "Image" => "cog"),
            ]
        );
}

if (\_::$Joint->Shop->DefaultMenu) {
    $menus = array(
        array(
            "Name" => \_::$Joint->Shop->Title,
            "Description" => \_::$Joint->Shop->Description,
            "Image" => \_::$Joint->Shop->Image,
            "Path" => \_::$Joint->Shop->ItemsRootUrlPath,
            "Items" => array(
                array(
                    "Name" => \_::$Joint->Shop->ItemsTitle,
                    "Description" => \_::$Joint->Shop->ItemsDescription,
                    "Access" => \_::$Joint->Shop->BuyingAccess,
                    "Path" => \_::$Joint->Shop->ItemsRootUrlPath,
                    "Image" => \_::$Joint->Shop->ItemsImage
                ),
                array(
                    "Name" => \_::$Joint->Shop->CartTitle,
                    "Description" => \_::$Joint->Shop->CartDescription,
                    "Access" => \_::$Joint->Shop->BuyingAccess,
                    "Path" => \_::$Joint->Shop->CartUrlPath,
                    "Image" => \_::$Joint->Shop->CartImage
                ),
                array(
                    "Name" => \_::$Joint->Shop->GroupsTitle,
                    "Description" => \_::$Joint->Shop->GroupsDescription,
                    "Access" => \_::$Joint->Shop->BuyingAccess,
                    "Path" => \_::$Joint->Shop->GroupsUrlPath,
                    "Image" => \_::$Joint->Shop->GroupsImage
                ),
                array(
                    "Name" => \_::$Joint->Shop->RequestsTitle,
                    "Description" => \_::$Joint->Shop->RequestsDescription,
                    "Access" => \_::$Joint->Shop->BuyingAccess,
                    "Path" => \_::$Joint->Shop->RequestsUrlPath,
                    "Image" => \_::$Joint->Shop->RequestsImage
                ),
                array(
                    "Name" => \_::$Joint->Shop->AdminTitle,
                    "Description" => \_::$Joint->Shop->AdminDescription,
                    "Image" => \_::$Joint->Shop->AdminImage,
                    "Path" => "/administrator/shop/merchandises",
                    "Access" => \_::$Joint->Shop->AmbassadorsAccess,
                    "Items" => array(
                        array("Name" => "MERCHANDISES", "Access" => \_::$Joint->Shop->SellingAccess, "Path" => "/administrator/shop/merchandises", "Description" => "To manage all the merchandises and items", "Image" => "box"),
                        array("Name" => "REQUESTS", "Access" => \_::$Joint->Shop->AmbassadorsAccess, "Path" => "/administrator/shop/requests", "Description" => "To manage all the user 'requests'", "Image" => "truck"),
                        array("Name" => "WAITS", "Access" => \_::$Joint->Shop->SellingAccess, "Path" => "/administrator/shop/waits", "Description" => "To manage all the user 'prerequests'", "Image" => "shopping-basket"),
                        array("Name" => "WISHES", "Access" => \_::$Joint->Shop->SellingAccess, "Path" => "/administrator/shop/groups", "Description" => "To manage all the user specified groups", "Image" => "heart"),
                        array("Name" => "DISCOUNTS", "Access" => \_::$Joint->Shop->SellingAccess, "Path" => "/administrator/shop/discounts", "Description" => "To manage all the discounts", "Image" => "percent"),
                    )
                )
            )
        )
    );

    \_::$Front->MainMenus = [
        ...array_slice(\_::$Front->MainMenus, 0, count(\_::$Front->MainMenus) - 1),
        ...$menus,
        ...array_slice(\_::$Front->MainMenus, count(\_::$Front->MainMenus) - 1),
    ];
    \_::$Front->SideMenus = [
        ...array_slice(\_::$Front->SideMenus, 0, count(\_::$Front->SideMenus) - 1),
        ...$menus,
        ...array_slice(\_::$Front->SideMenus, count(\_::$Front->SideMenus) - 1),
    ];

    if (!\_::$Front->Services)
        \_::$Front->Services = \_::$Front->MainMenus;

    if (!\_::$Front->Shortcuts)
        \_::$Front->Shortcuts = [
            array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
            array("Name" => "CART", "Path" => \_::$Joint->Shop->CartUrlPath, "Access" => \_::$Joint->Shop->BuyingAccess, "Image" => "shopping-cart"),
            array("Name" => "HOME", "Path" => \_::$Front->HomePath, "Image" => "home"),
            array("Name" => "MERCHANDISES", "Path" => \_::$Joint->Shop->ItemsRootUrlPath, "Access" => \_::$Joint->Shop->BuyingAccess, "Image" => "box"),
            array("Name" => "CONTACT", "Path" => "/contact", "Image" => "envelope")
        ];
}
// To unset the default router sat at the bottom layers
\_::$Router->On()->Reset();

/**
 * Use your routers by below formats
 * \_::$Router->On("A Part Of Path?")->Default("Route Name");
 */
\_::$Router->On(\_::$Joint->Shop->RootUrlPath . ".*")->Default("shop");