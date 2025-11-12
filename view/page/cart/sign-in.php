<?php
use MiMFa\Library\Struct;
use MiMFa\Library\Style;

module("PrePage");
$modulePrePage = new MiMFa\Module\PrePage();
$modulePrePage->Description = pop($data, "Description");
$modulePrePage->Content = pop($data, "Content");
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = pop($data, "Items")??compute("request/currents", receive());
$module->AllowItems = false;
module("SignInForm");
$sign = new MiMFa\Module\SignInForm();
$modulePrePage->Title = $sign->Title??pop($data, "Title")??"Signing";
$modulePrePage->Image = $sign->Image??pop($data, "Image")??"user";
$modulePrePage->Render();
$sign->AllowHeader = false;
$sign->ContentClass = "col-lg";
$sign->SignUpPath = "/cart/sign-up";
$sign->RecoverPath = "/cart/recover";
$sign->Welcome = fn()=>load("/cart/options");
$module->Content = $sign;
$module->BackButton = Struct::Button("Cart", "/cart", ["class" => "col-sm-4"]);
if(\_::$User->GetAccess(\_::$User->UserAccess)) $module->NextButton = Struct::Button("Continue", "/cart/options", ["class" => "btn main col-sm"]);
$module->Render();
?>