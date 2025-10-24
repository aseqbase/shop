<?php
use MiMFa\Library\Html;

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
$module->NextButton = Html::Button("Payment", "/cart/payment", ["class" => "btn main col-sm"]);
$module->BackButton = Html::Button("Options", "/cart/options", ["class" => "col-sm-4"]);
dip($module, $data);
$module->Render();
?>