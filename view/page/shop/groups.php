<?php
use MiMFa\Library\Struct;
response(Struct::OpenTag("div", ["class"=>"page"]));
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")?:\_::$Joint->Shop->GroupsTitle;
$module->Description = pop($data, "Description")?:\_::$Joint->Shop->GroupsDescription;
$module->Content = pop($data, "Content")?:\_::$Joint->Shop->GroupsContent;
$module->Image = pop($data, "Image")?:\_::$Joint->Shop->GroupsImage;
$module->Render();
module("shop\CartCollection");
$module = new MiMFa\Module\Shop\CartCollection();
$module->AllowInvoice = 
$module->AllowContact = 
$module->AllowAddress = false;
$module->DefaultImage = \_::$Joint->Shop->ItemDefaultImagePath;
$module->DefaultTitle = \_::$Joint->Shop->ItemDefaultTitle;
$module->DefaultDescription = \_::$Joint->Shop->ItemDefaultDescription;
$module->Root = \_::$Joint->Shop->ItemRootUrlPath;
$module->CollectionRoot = \_::$Joint->Shop->ItemsRootUrlPath;
$module->CartRoot = \_::$Joint->Shop->CartRootUrlPath;
$module->Items = pop($data, "Items")??compute("shop/request/groups", receive());
$module->MaximumColumns = 2;
if($metadata = \MiMFa\Library\Convert::FromJson(\_::$Joint->Shop->GroupsMetaData))
    pod($module, $metadata);
pod($module, $data);
$module->Render();
response(Struct::CloseTag("div"));