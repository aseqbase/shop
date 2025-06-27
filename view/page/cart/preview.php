<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title")??"Preview";
$module->Description = grab($data, "Description");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image")??"check";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items")??compute("request/currents", \Req::Receive());
$module->ShowContact = 
$module->ShowAddress = true;
$module->NextButton = Html::Button("Payment", "/cart/payment", ["class" => "btn main col-sm"]);
$module->BackButton = Html::Button("Options", "/cart/options", ["class" => "col-sm-4"]);
swap($module, $data);
$module->Render();
?>