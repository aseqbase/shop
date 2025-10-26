<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title")??"Succeed";
$module->Description = pop($data, "Description")??MiMFa\Library\Html::Success("Thanks, your payment is completed successfully.");
$module->Content = pop($data, "Content");
$module->Image = pop($data, "Image")??"check";
$module->Render();
$id = getReceived("Id");
if (compute("request/complete", ["PaymentId" => $id]))
    response(Html::Success("Your transaction verified successfully!"));
?>