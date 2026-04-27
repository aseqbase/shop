<?php
use MiMFa\Library\Struct;
response(Struct::OpenTag("div", ["class"=>"page"]));
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")?:\_::$Joint->Shop->PreviewTitle;
$module->Description = pop($data, "Description")?:\_::$Joint->Shop->PreviewDescription;
$module->Content = pop($data, "Content")?:\_::$Joint->Shop->PreviewContent;
$module->Image = pop($data, "Image")?:\_::$Joint->Shop->PreviewImage;
$module->Render();
module("shop\CartCollection");
$module = new MiMFa\Module\Shop\CartCollection();
$module->CartButtonLabel = null; 
$module->AllowContact = 
$module->AllowAddress = false;
$module->DefaultImage = \_::$Joint->Shop->ItemDefaultImagePath;
$module->DefaultTitle = \_::$Joint->Shop->ItemDefaultTitle;
$module->DefaultDescription = \_::$Joint->Shop->ItemDefaultDescription;
$module->Root = \_::$Joint->Shop->ItemRootUrlPath;
$module->CollectionRoot = \_::$Joint->Shop->ItemsRootUrlPath;
$module->CartRoot = \_::$Joint->Shop->CartRootUrlPath;
$module->Items = pop($data, "Items")??compute("shop/request/currents", receive());
$module->NextButton = Struct::Button(\_::$Joint->Shop->PaymentTitle, \_::$Joint->Shop->PaymentUrlPath, ["class" => "btn main col-sm"]);
$module->BackButton = Struct::Button(\_::$Joint->Shop->OptionsTitle, \_::$Joint->Shop->OptionsUrlPath, ["class" => "col-sm-4"]);
if($metadata = \MiMFa\Library\Convert::FromJson(\_::$Joint->Shop->PreviewMetaData))
    pod($module, $metadata);
pod($module, $data);
$module->Render();
response(Struct::CloseTag("div"));