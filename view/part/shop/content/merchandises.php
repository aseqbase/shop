<?php
$Items = pop($data, "Items");
$Name = pop($data, 'Name');
$Title = pop($data, 'Title');
module("Navigation");
$nav = new \MiMFa\Module\Navigation($Items);
module("shop\MerchandiseCollection");
$module = new \MiMFa\Module\Shop\MerchandiseCollection();
$module->Title = !isEmpty($Title) && !isEmpty($Name) && abs(strlen($Name) - strlen($Title)) > 3 ? "$Title ".($Name?"($Name)":"") : between($Title, $Name);
$module->AllowRoot = false;
$module->Description = pop($data, "Description");
$module->Class .= " page";
$module->Items = $nav->GetItems();
if($metadata = \MiMFa\Library\Convert::FromJson(\_::$Joint->Shop->ItemsMetaData))
    pod($module, $metadata);
pod($module, $data);
$module->Render();
$nav->Render();