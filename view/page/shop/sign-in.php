<?php
use MiMFa\Library\Struct;
$data = $data ?? [];
if (\_::$User->HasAccess(\_::$User->UserAccess))
    page(\_::$Joint->Shop->OptionsUrlPath, $data);
response(Struct::OpenTag("div", ["class" => "page"]));
module("PrePage");
$modulePrePage = new MiMFa\Module\PrePage();
$modulePrePage->Description = pop($data, "Description");
$modulePrePage->Content = pop($data, "Content");
module("shop\CartCollection");
$module = new MiMFa\Module\Shop\CartCollection();
$module->Items = pop($data, "Items") ?? compute("shop/request/currents", receive());
$module->AllowItems = false;
// module("SignInForm");
// $sign = new MiMFa\Module\SignInForm();
// $modulePrePage->Title = $sign->Title??pop($data, "Title")??"Signing";
// $modulePrePage->Image = $sign->Image??pop($data, "Image")??"user";
// $sign->AllowHeader = false;
// $sign->ContentClass = "col-lg";
// $sign->SignUpPath = \_::$Joint->Shop->SignUpUrlPath;
// $sign->RecoverPath = \_::$Joint->Shop->SignInUrlPath;
// $sign->Welcome = fn()=>load(\_::$Joint->Shop->OptionsUrlPath);
// $module->Content = $sign;
$modulePrePage->Title = pop($data, "Title") ?? "Signing";
$modulePrePage->Image = pop($data, "Image") ?? "user";
$modulePrePage->Render();
$module->Content = part(\_::$User->InHandlerPath, [
    "AllowHeader" => false,
    "SignUpPath" => \_::$Joint->Shop->SignUpUrlPath,
    "RecoverPath" => \_::$Joint->Shop->SignRecoverUrlPath
], print: false);
$module->BackButton = Struct::Button(\_::$Joint->Shop->CartTitle, \_::$Joint->Shop->CartUrlPath, ["class" => "col-sm-4"]);
if ($module->Items)
    $module->Render();
else
    response($module->Content);
response(Struct::CloseTag("div"));