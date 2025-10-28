<?php
if(!isValid($data, "MerchandiseId")) return part("content/content", $data);
module("Merchandise");
$module = new \MiMFa\Module\Merchandise();
$name = $module->Name;
$module->Item = $data;
$module->CommentForm->SubjectLabel = null;
pod($module, $data);
$module->Name = $name;// To do not change the name of module
$module->Render();
?>