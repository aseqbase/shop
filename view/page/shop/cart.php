<?php
use MiMFa\Library\Struct;
response(Struct::OpenTag("div", ["class"=>"page"]));
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")?:\_::$Joint->Shop->CartTitle;
$module->Description = pop($data, "Description")?:\_::$Joint->Shop->CartDescription;
$module->Content = pop($data, "Content")?:\_::$Joint->Shop->CartContent;
$module->Image = pop($data, "Image")?:\_::$Joint->Shop->CartImage;
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
$module->NextButton = Struct::Button("Confirm", \_::$Joint->Shop->OptionsUrlPath, ["class" => "btn main col-sm"]);
$module->BackButton = Struct::Button("Add more...", \_::$Joint->Shop->ItemsRootUrlPath, ["class" => "col-sm"]);
if($metadata = \MiMFa\Library\Convert::FromJson(\_::$Joint->Shop->CartMetaData))
    pod($module, $metadata);
pod($module, $data);
$module->Render();
response(Struct::CloseTag("div"));