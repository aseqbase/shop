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
// module("SignRecoverForm");
// $sign = new MiMFa\Module\SignRecoverForm();
// $modulePrePage->Title = $sign->Title??pop($data, "Title")??"Signing";
// $modulePrePage->Image = $sign->Image??pop($data, "Image")??"user";
// $modulePrePage->Render();
// $sign->AllowHeader = false;
// $sign->ContentClass = "col-lg";
// $sign->SignUpPath = \_::$Joint->Shop->SignUpUrlPath;
// $sign->SignInPath = \_::$Joint->Shop->SignInUrlPath;
// $module->Content = $sign;
$modulePrePage->Title = pop($data, "Title") ?? "Signing";
$modulePrePage->Image = pop($data, "Image") ?? "recovery";
$modulePrePage->Render();
$module->Content = part(\_::$User->RecoverHandlerPath, [
    "AllowHeader" => false,
    "SignUpPath" => \_::$Joint->Shop->SignUpUrlPath,
    "SignInPath" => \_::$Joint->Shop->SignInUrlPath
], print: false);
$module->BackButton = Struct::Button(\_::$Joint->Shop->CartTitle, \_::$Joint->Shop->CartUrlPath, ["class" => "col-sm-4"]);
if (\_::$User->HasAccess(\_::$User->UserAccess))
    $module->NextButton = Struct::Button("Continue", \_::$Joint->Shop->OptionsUrlPath, ["class" => "btn main col-sm"]);
if ($module->Items)
    $module->Render();
else
    response($module->Content);
response(Struct::CloseTag("div"));