<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title")??"Succeed";
$module->Description = grab($data, "Description")??MiMFa\Library\Html::Success("Thanks, your payment is completed successfully.");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image")??"tick";
$module->Render();
$id = \Req::Receive("Id");
if (compute("request/complete", ["PaymentId" => $id]))
    \Res::Render(Html::Success("Your transaction verified successfully!"));
?>