<?php
use MiMFa\Library\Html;
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Payment";
$module->Render();
part("pay", $data);
?>