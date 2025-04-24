<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title")??"Cart";
$module->Description = grab($data, "Description");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image")??"shopping-cart";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items");
$module->NextButton = Html::Button("Confirm", "/cart/options", ["class" => "btn-main"]);
swap($module, $data);
$module->Render();
?>