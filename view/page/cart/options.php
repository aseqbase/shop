<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title") ?? "Options";
$module->Description = grab($data, "Description");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image") ?? "map-marker";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items");
$isDigital = find($module->Items, fn($k, $v) => get($v, "MerchandiseDigital"))??\_::$Config->DigitalStore;
$module->ShowAddress = !$isDigital;
$id = "form_" . getId(true);
$nextReference = $isDigital ? "/cart/payment" : "submitForm('#$id', (d,e)=>d?load('/cart/payment'):null)";
if (!$isDigital)
    $module->Description = Html::Style("
            #$id {
                width:100%;
            }
            #$id .field .input{
                background-color: var(--back-color-1);
                color: var(--fore-color-1);
                width:100%;
            }
        ") .
        Html::Form(
            [
                Html::Field(type: "Tel", key: "Contact", value: \_::$Back->User->GetValue("Contact")),
                Html::Field(type: "Texts", key: "Address", value: \_::$Back->User->GetValue("Address"))
            ],
            null,
            ["Id" => $id, "method"=>"PUT"]
        );
$module->NextButton = Html::Button("Pay", $nextReference, ["class" => "btn-main col-sm"]);
$module->BackButton = Html::Button("Cart", "/cart", ["class" => "col-sm-4"]);
swap($module, $data);
$module->Render();
?>