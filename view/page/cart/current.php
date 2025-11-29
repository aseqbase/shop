<?php
use MiMFa\Library\Struct;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")??"Cart";
$module->Description = pop($data, "Description");
$module->Content = pop($data, "Content");
$module->Image = pop($data, "Image")??"shopping-cart";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = pop($data, "Items")??compute("request/currents", receive());
$module->AllowContact = 
$module->AllowAddress = false;
if(\_::$User->HasAccess(\_::$User->UserAccess)) $module->NextButton = Struct::Button("Confirm", "/cart/options", ["class" => "btn main"]);
else $module->NextButton = Struct::Button("Confirm", "/cart/sign-in", ["class" => "btn main"]);
pod($module, $data);
$module->Render();
?>