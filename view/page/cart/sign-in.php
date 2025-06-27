<?php
use MiMFa\Library\Html;
use MiMFa\Library\Style;

module("PrePage");
$modulePrePage = new MiMFa\Module\PrePage();
$modulePrePage->Description = grab($data, "Description");
$modulePrePage->Content = grab($data, "Content");
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items")??compute("request/currents", \Req::Receive());
$module->ShowItems = false;
module("SignInForm");
$sign = new MiMFa\Module\SignInForm();
$modulePrePage->Title = $sign->Title??grab($data, "Title")??"Signing";
$modulePrePage->Image = $sign->Image??grab($data, "Image")??"user";
$modulePrePage->Render();
$sign->AllowHeader = false;
$sign->ContentClass = "col-lg";
$sign->SignUpPath = "/cart/sign-up";
$sign->RecoverPath = "/cart/recover";
$sign->Welcome = fn()=>\Res::Load("/cart/options");
$module->Content = $sign;
$module->BackButton = Html::Button("Cart", "/cart", ["class" => "col-sm-4"]);
if(auth(\_::$Config->UserAccess)) $module->NextButton = Html::Button("Continue", "/cart/options", ["class" => "btn main col-sm"]);
$module->Render();
?>