<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title")??"All Requests";
$module->Description = grab($data, "Description");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image")??"list";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items")??compute("request/all", receive());
$module->AllowContact = 
$module->AllowAddress = false;
if(auth(\_::$Config->UserAccess)) $module->NextButton = Html::Button("Confirm", "/cart/options", ["class" => "btn main"]);
else $module->NextButton = Html::Button("Confirm", "/cart/sign-in", ["class" => "btn main"]);
swap($module, $data);
$module->Render();
?>