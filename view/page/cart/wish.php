<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")??"Wish List";
$module->Description = pop($data, "Description");
$module->Content = pop($data, "Content");
$module->Image = pop($data, "Image")??"heart";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = pop($data, "Items")??compute("request/wishes", receive());
$module->AllowBill = 
$module->AllowContact = 
$module->AllowAddress = false;
if(\_::$User->GetAccess(\_::$User->UserAccess)) $module->NextButton = Html::Button("Confirm", "/cart/options", ["class" => "btn main"]);
else $module->NextButton = Html::Button("Confirm", "/cart/sign-in", ["class" => "btn main"]);
pod($module, $data);
$module->Render();
?>