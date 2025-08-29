<?php
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title") ?? "Delivery";
$module->Description = grab($data, "Description");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image") ?? "truck";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items")??compute("request/currents", receive());
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
            Html::Field(type: "Email", key: "Email", value: \_::$Back->User->GetMetaValue("Email")??\_::$Back->User->Email, description:$isDigital ? "Please put a correct email address to receive the digital items links" : "To inform your cart status changes...", attributes:["required"]),
            Html::Field(type: "Tel", key: "Contact", value: \_::$Back->User->GetMetaValue("Contact")??\_::$Back->User->GetValue("Contact"), description:$isDigital ? "A phone or mobile numbers (use your country code)..." :  "An oncall phone or mobile numbers (use your country code)...", attributes:$isDigital ? []:["required"]),
            ...($isDigital ? [] : [
                Html::Field(type: "Text", key: "Country", value: \_::$Back->User->GetMetaValue("Country"), description:"Your country to send your cart there...", attributes:["required"]),
                Html::Field(type: "Text", key: "Province", value: \_::$Back->User->GetMetaValue("Province"), description:"Your province to send your cart there...", attributes:["required"]),
                Html::Field(type: "Text", key: "City", value: \_::$Back->User->GetMetaValue("City"), description:"Your city to send your cart there...", attributes:["required"]),
                Html::Field(type: "Texts", key: "Address", value: \_::$Back->User->GetMetaValue("Address")??\_::$Back->User->GetValue("Address"), description:"A full address to send your cart there...", attributes:["required"]),
                Html::Field(type: "Text", key: "PostalCode", value: \_::$Back->User->GetMetaValue("PostalCode"), description:"Your exact postal code (zipcode)")
            ]),
        ],
        null,
        ["Id" => $id, "method" => "PUT"]
    );
$module->BackButton = Html::Button("Cart", "submitForm('#$id', (d,e)=>load('/cart'), (d,e)=>load('/cart'));", ["class" => "col-sm-4"]);
if($isDigital == \_::$Config->DigitalStore) $module->NextButton = Html::Button("Payment", "submitForm('#$id', (d,e)=>load('/cart/payment'));", ["class" => "btn main col-sm"]);
else $module->NextButton = Html::Button("Preview", "submitForm('#$id', (d,e)=>load('/cart/preview'));", ["class" => "btn main col-sm"]);
swap($module, $data);
$module->Render();
?>