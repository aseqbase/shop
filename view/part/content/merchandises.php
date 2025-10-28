<?php
$Items = pop($data, "Items");
$Name = pop($data, 'Name');
$Title = pop($data, 'Title');
module("Navigation");
$nav = new \MiMFa\Module\Navigation($Items);
module("MerchandiseCollection");
$module = new \MiMFa\Module\MerchandiseCollection();
$module->Title = !isEmpty($Title) && !isEmpty($Name) && abs(strlen($Name) - strlen($Title)) > 3 ? "$Title ".($Name?"($Name)":"") : between($Title, $Name);
$module->DefaultImage = \_::$Info->FullLogoPath;
$module->AllowRoot = false;
$module->Description = pop($data, "Description");
$module->Class .= " page";
$module->Items = $nav->GetItems();
pod($module, $data);
$module->Render();
$nav->Render();
?>