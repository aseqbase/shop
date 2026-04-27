<?php
if(!isValid($data, "MerchandiseId")) return part("content/content", $data);
module("shop\Merchandise");
$module = new \MiMFa\Module\Shop\Merchandise();
$name = $module->MainClass;
$module->Item = $data;
$module->AllowCommentsAccess = \_::$Joint->Shop->CommentsAccess;
$module->CommentForm->SubjectLabel = null;
if($metadata = \MiMFa\Library\Convert::FromJson(\_::$Joint->Shop->ItemMetaData))
    pod($module, $metadata);
pod($module, $data);
$module->MainClass = $name;// To do not change the name of module
$module->Render();