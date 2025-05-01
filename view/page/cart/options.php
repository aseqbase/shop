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
$isDigital = false && find($module->Items, fn($k, $v) => get($v, "MerchandiseDigital")) ?? \_::$Config->DigitalStore;
$module->ShowAddress = !$isDigital;
$module->ShowItems = false;
$id = "form_" . getId(true);
$nextReference = $isDigital ? "/cart/payment" : "submitForm('#$id', (d,e)=>d?load('/cart/payment'):null)";
$module->Description = Html::Style("
            #$id {
                width:100%;
            }
            #$id .field .input{
                background-color: var(--back-color-1);
                color: var(--fore-color-1);
                width:100%;
            }
            #$id .field .description{
                font-size: var(--size-0);
                color: #8888;
            }
        ") .
    Html::Form(
        [
            Html::Field(type: "Email", key: "Email", value: \_::$Back->User->Email, description:$isDigital ? "Please put a correct email address to receive the digital items links" : "To inform your cart status changes..."),
            ...($isDigital ? [] : [
                Html::Field(type: "Tel", key: "Contact", value: \_::$Back->User->GetValue("Contact"), description:"An oncall phone or mobile numbers..."),
                Html::Field(type: "Texts", key: "Address", value: \_::$Back->User->GetValue("Address"), description:"A full address to send your cart there...")
            ]),
        ],
        null,
        ["Id" => $id, "method" => "PUT"]
    );
$module->NextButton = Html::Button("Pay", $nextReference, ["class" => "btn main col-sm"]);
$module->BackButton = Html::Button("Cart", "/cart", ["class" => "col-sm-4"]);
swap($module, $data);
$module->Render();
?>