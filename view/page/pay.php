<?php
use MiMFa\Library\Struct;
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Payment";
$module->Render();
part("pay", $data);
?>