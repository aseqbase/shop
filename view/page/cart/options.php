<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title") ?? "Delivery";
$module->Description = pop($data, "Description");
$module->Content = pop($data, "Content");
$module->Image = pop($data, "Image") ?? "truck";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = pop($data, "Items")??compute("request/currents", receive());
$isDigital = \_::$Config->DigitalStore;
foreach ($module->Items as $k => $v) 
    if(!(get($v, "MerchandiseDigital")??\_::$Config->DigitalStore)){
        $isDigital = false;
        break;
    }
$module->AllowItems = false;
$id = "form_" . getId(true);
$module->Description = Html::Style("
            #$id {
                width:100%;
            }
            #$id .field .input{
                background-color: var(--back-color-input);
                color: var(--fore-color-input);
                width:100%;
            }
            #$id .field .description{
                font-size: var(--size-0);
                color: #8888;
            }
        ") .
    Html::Form(
        [
            Html::Field(type: "Email", key: "Email", value: \_::$User->GetMetaValue("Email")??\_::$User->Email, description:$isDigital ? "Please put a correct email address to receive the digital items links" : "To inform your cart status changes...", attributes:["required"]),
            Html::Field(type: "Tel", key: "Contact", value: \_::$User->GetMetaValue("Contact")??\_::$User->GetValue("Contact"), description:$isDigital ? "A phone or mobile numbers (use your country code)..." :  "An oncall phone or mobile numbers (use your country code)...", attributes:$isDigital ? []:["required"]),
            ...($isDigital ? [] : [
                Html::Field(type: "Text", key: "Country", value: \_::$User->GetMetaValue("Country"), description:"Your country to send your cart there...", attributes:["required"]),
                Html::Field(type: "Text", key: "Province", value: \_::$User->GetMetaValue("Province"), description:"Your province to send your cart there...", attributes:["required"]),
                Html::Field(type: "Text", key: "City", value: \_::$User->GetMetaValue("City"), description:"Your city to send your cart there...", attributes:["required"]),
                Html::Field(type: "Texts", key: "Address", value: \_::$User->GetMetaValue("Address")??\_::$User->GetValue("Address"), description:"A full address to send your cart there...", attributes:["required"]),
                Html::Field(type: "Text", key: "PostalCode", value: \_::$User->GetMetaValue("PostalCode"), description:"Your exact postal code (zipcode)")
            ]),
        ],
        null,
        ["Id" => $id, "method" => "PUT"]
    );
$module->BackButton = Html::Button("Cart", "submitForm('#$id', (d,e)=>load('/cart'), (d,e)=>load('/cart'));", ["class" => "col-sm-4"]);
if($isDigital == \_::$Config->DigitalStore) $module->NextButton = Html::Button("Payment", "submitForm('#$id', (d,e)=>load('/cart/payment'));", ["class" => "btn main col-sm"]);
else $module->NextButton = Html::Button("Preview", "submitForm('#$id', (d,e)=>load('/cart/preview'));", ["class" => "btn main col-sm"]);
dip($module, $data);
$module->Render();
?>