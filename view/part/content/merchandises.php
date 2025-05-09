<?php
$Items = grab($data, "Items");
$Name = grab($data, 'Name');
$Title = grab($data, 'Title');
module("Navigation");
$nav = new \MiMFa\Module\Navigation($Items);
module("MerchandiseCollection");
$module = new \MiMFa\Module\MerchandiseCollection();
$module->Title = !isEmpty($Title) && !isEmpty($Name) && abs(strlen($Name) - strlen($Title)) > 3 ? "$Title ".($Name?"($Name)":"") : between($Title, $Name);
$module->DefaultImage = \_::$Info->FullLogoPath;
$module->ShowRoute = false;
$module->Description = grab($data, "Description");
$module->Class .= " page";
$module->Items = $nav->GetItems();
swap($module, $data);
$module->Render();
$nav->Render();
?>