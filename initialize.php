<?php
\_::$Back->DecimalPercision = 2;
\_::$Back->MerchandiseUnit = " merchandises";
\_::$Back->DigitalStore = true;
\_::$Back->CountUnit = " items";
\_::$Back->PriceUnit = "$";
\_::$Back->Task = "$";
\_::$Back->ComputePrice = function ($price = 0, $discount=null, $metadata = null, $id = null, &$effectiveDiscounts = []) {
    if ($discount) $effectiveDiscounts['Discount'] = ($effectiveDiscounts['Discount']??0)-$discount * $price /100;
    else $discount = 0;
    if (isset($metadata["Price"]))
        foreach ($metadata["Price"] as $key => $value) {
            if (is_string($value)) {
                $pp = floatval(preg_replace("/[\%\s]/", "", $value));
                $effectiveDiscounts[$key] = ($effectiveDiscounts[$key]??0) + $pp * $price / 100;
                $discount -= $pp;
            } else{
                $effectiveDiscounts[$key] = ($effectiveDiscounts[$key]??0) + $value;
                $discount -= $value*100/$price;
            }
        }
    return $discount ? $price - $discount * $price / 100 : $price;
};
\_::$Back->StandardPrice = function($price = 0, $unit = null){
    switch (trim(strtolower($unit??""))) {
        case 'usd':
        case 'usdt':
        case '$':
            return $price;
        default:
            return $price;
    }
};
$menus = array(
    array("Name" => "MERCHANDISES", "Path" => "/items", "Image" => "box"),
    array("Name" => "CART", "Path" => "/cart", "Image" => "shopping-cart"),
    array("Name" => "WISHES", "Path" => "/cart/wish", "Image" => "heart"),
    array("Name" => "REQUESTS", "Path" => "/cart/all", "Image" => "list"),
    array(
        "Name" => "CONTACTS",
        "Path" => "/contact",
        "Image" => "envelope",
        "Items" => array(
            array("Name" => "FORUMS", "Path" => "/forums", "Image" => "comments"),
            array("Name" => "CONTACTS", "Path" => "/contact", "Image" => "address-book"),
            array("Name" => "ABOUT", "Path" => "/about", "Image" => "info")
        )
    )
);

\_::$Front->Payment = \_::$Front->Payment??null;
\_::$Front->MainMenus = [...\_::$Front->MainMenus,...$menus];
\_::$Front->SideMenus = [...\_::$Front->SideMenus,...$menus];

if(!\_::$Front->Services) \_::$Front->Services = \_::$Front->MainMenus;

if(!\_::$Front->Shortcuts) \_::$Front->Shortcuts = [
    array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
    array("Name" => "CART", "Path" => "/cart", "Image" => "shopping-cart"),
    array("Name" => "HOME", "Path" => \_::$Front->HomePath, "Image" => "home"),
    array("Name" => "MERCHANDISES", "Path" => "/items", "Image" => "box"),
    array("Name" => "CONTACT", "Path" => "/contact", "Image" => "envelope")
];

// To unset the default router sat at the bottom layers
\_::$Router->On()->Reset();

/**
 * Use your routers by below formats
 * \_::$Router->On("A Part Of Path?")->Default("Route Name");
 */
\_::$Router->On("cart")->Default("cart");
\_::$Router->On("(item|merchandise)s")->Default("merchandises");
\_::$Router->On("item|merchandise")->Default("merchandise");