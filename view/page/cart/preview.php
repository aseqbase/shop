<?php
use MiMFa\Library\Struct;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")??"Preview";
$module->Description = pop($data, "Description");
$module->Content = pop($data, "Content");
$module->Image = pop($data, "Image")??"check";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = pop($data, "Items")??compute("request/currents", receive());
$module->AllowContact = 
$module->AllowAddress = true;
$module->NextButton = Struct::Button("Payment", "/cart/payment", ["class" => "btn main col-sm"]);
$module->BackButton = Struct::Button("Options", "/cart/options", ["class" => "col-sm-4"]);
pod($module, $data);
$module->Render();
?>