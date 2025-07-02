<?php
\_::$Config->DecimalPercision = 2;
\_::$Config->MerchandiseUnit = " merchandises";
\_::$Config->DigitalStore = true;
\_::$Config->CountUnit = " items";
\_::$Config->PriceUnit = "$";
\_::$Config->Task = "$";
\_::$Config->ComputePrice = function ($price = 0, $discount=null, $metadata = null, $id = null, &$effectiveDiscounts = []) {
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
\_::$Config->StandardPrice = function($price = 0, $unit = null){
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

\_::$Info->Payment = \_::$Info->Payment??null;
\_::$Info->MainMenus = [...\_::$Info->MainMenus,...$menus];
\_::$Info->SideMenus = [...\_::$Info->SideMenus,...$menus];

if(!\_::$Info->Services) \_::$Info->Services = \_::$Info->MainMenus;

if(!\_::$Info->Shortcuts) \_::$Info->Shortcuts = [
    array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
    array("Name" => "CART", "Path" => "/cart", "Image" => "shopping-cart"),
    array("Name" => "HOME", "Path" => \_::$Info->HomePath, "Image" => "home"),
    array("Name" => "MERCHANDISES", "Path" => "/items", "Image" => "box"),
    array("Name" => "CONTACT", "Path" => "/contact", "Image" => "envelope")
];
?>