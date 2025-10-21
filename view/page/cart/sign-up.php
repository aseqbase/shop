<?php
use MiMFa\Library\Html;
use MiMFa\Library\Style;

module("PrePage");
$modulePrePage = new MiMFa\Module\PrePage();
$modulePrePage->Description = grab($data, "Description");
$modulePrePage->Content = grab($data, "Content");
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items")??compute("request/currents", receive());
$module->AllowItems = false;
module("SignUpForm");
$sign = new MiMFa\Module\SignUpForm();
$modulePrePage->Title = $sign->Title??grab($data, "Title")??"Signing";
$modulePrePage->Image = $sign->Image??grab($data, "Image")??"user";
$modulePrePage->Render();
$sign->AllowHeader = false;
$sign->ContentClass = "col-lg";
$sign->SignInPath = "/cart/sign-in";
$sign->RecoverPath = "/cart/recover";
$sign->Welcome = fn()=>load("/cart/options");
$module->Content = $sign;
$module->BackButton = Html::Button("Cart", "/cart", ["class" => "col-sm-4"]);
if(\_::$User->GetAccess(\_::$User->UserAccess)) $module->NextButton = Html::Button("Continue", "/cart/options", ["class" => "btn main col-sm"]);
$module->Render();
?>