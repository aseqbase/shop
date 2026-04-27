<?php
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
if (!\_::$User->HasAccess(\_::$User->UserAccess))
    return page(\_::$Joint->Shop->SignInUrlPath, $data);
response(Struct::OpenTag("div", ["class"=>"page"]));
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title") ?: \_::$Joint->Shop->OptionsTitle;
$module->Description = pop($data, "Description") ?: \_::$Joint->Shop->OptionsDescription;
$module->Content = pop($data, "Content") ?: \_::$Joint->Shop->OptionsContent;
$module->Image = pop($data, "Image") ?: \_::$Joint->Shop->OptionsImage;
$module->Render();
module("shop\CartCollection");
$module = new MiMFa\Module\Shop\CartCollection();
$module->CartButtonLabel = null;
$module->DefaultImage = \_::$Joint->Shop->ItemDefaultImagePath;
$module->DefaultTitle = \_::$Joint->Shop->ItemDefaultTitle;
$module->DefaultDescription = \_::$Joint->Shop->ItemDefaultDescription;
$module->Root = \_::$Joint->Shop->ItemRootUrlPath;
$module->CollectionRoot = \_::$Joint->Shop->ItemsRootUrlPath;
$module->CartRoot = \_::$Joint->Shop->CartRootUrlPath;
$module->Items = pop($data, "Items") ?? compute("shop/request/currents", receive());
$isDigital = \_::$Joint->Shop->DigitalStore;
foreach ($module->Items as $k => $v)
    if (!(get($v, "MerchandiseDigital") ?? \_::$Joint->Shop->DigitalStore)) {
        $isDigital = false;
        break;
    }
$sd = $isDigital === \_::$Joint->Shop->DigitalStore;
$module->AllowItems = false;
$id = "form_" . getId(true);
$sites = \_::$Joint->Shop->GetSites($isDigital);
$module->Description .= Struct::Style("
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
    Struct::Form(
        [
            Struct::HiddenInput("Next", ($sd?\_::$Joint->Shop->PaymentUrlPath:\_::$Joint->Shop->PreviewUrlPath)),
            Struct::Field(type: "Email", key: "Email", value: \_::$User->GetMetaValue("Email") ?? \_::$User->Email, description: $isDigital ? "Please put a correct email address to receive the digital items links" : "To inform your cart status changes...", attributes: ["required"]),
            Struct::Field(type: "Tel", key: "Contact", value: \_::$User->GetMetaValue("Contact") ?? \_::$User->GetValue("Contact"), description: $isDigital ? "A phone or mobile numbers (use your country code)..." : "An oncall phone or mobile numbers (use your country code)...", attributes: $isDigital ? [] : ["required"]),
            //Struct::Field(type: "Map", key: "Location", value: \_::$User->GetMetaValue("Location"), description:"Your location to send your cart there...", attributes:["required"]),
            ...($sites ? [
                Struct::Field(
                    type: "select",
                    key: "Country",
                    value: \_::$User->GetMetaValue("Country"),
                    options: $sites,
                    description: "Your country to send your cart there...",
                    attributes: [
                        "required",
                        "onchange" => Script::Action(
                            "[" . Script::Convert($isDigital) . ", _(this).matches('optgroup:has(option[value=\"'+_(this).val()+'\"])').attr('label'), _(this).val(), getQuery(this.parentNode.nextElementSibling)]",
                            function ($isDigital, $continent, $country, $selector) {
                                $sites = \_::$Joint->Shop->GetSites($isDigital, $continent, $country);
                                return \MiMFa\Library\Struct::Script(
                                    "_('$selector').replace(" .
                                    \MiMFa\Library\Script::Convert(
                                        $sites ?
                                        \MiMFa\Library\Struct::Field(type: "Select", key: "Province", value: \_::$User->GetMetaValue("Province"), options: $sites, description: "Your province to send your cart there...", attributes: [
                                            "required",
                                            "onchange" => \MiMFa\Library\Script::Action(
                                                "[" . \MiMFa\Library\Script::Convert($isDigital) . ", " . \MiMFa\Library\Script::Convert($continent) . ", " . \MiMFa\Library\Script::Convert($country) . ", _(this).val(), getQuery(this.parentNode.nextElementSibling)]",
                                                function ($isDigital, $continent, $country, $province, $selector) {
                                                    $sites = \_::$Joint->Shop->GetSites($isDigital, $continent, $country, $province);
                                                    return \MiMFa\Library\Struct::Script(
                                                        "_('$selector').replace(" .
                                                        \MiMFa\Library\Script::Convert(
                                                            $sites ?
                                                            \MiMFa\Library\Struct::Field(type: "Select", key: "City", value: \_::$User->GetMetaValue("City"), options: $sites, description: "Your city to send your cart there...", attributes: ["required"]) :
                                                            \MiMFa\Library\Struct::Field(type: "text", key: "City", value: \_::$User->GetMetaValue("City"), options: $sites, description: "Your city to send your cart there...", attributes: ["required"])
                                                        ) .
                                                        ");"
                                                    );
                                                }
                                            )
                                        ]) :
                                        \MiMFa\Library\Struct::Field(type: "Text", key: "Province", value: \_::$User->GetMetaValue("Province"), options: $sites, description: "Your province to send your cart there...", attributes: ["required"])
                                    ) .
                                    ");"
                                );
                            }
                        )
                    ]
                ),
                Struct::Field(type: "Text", key: "Province", value: \_::$User->GetMetaValue("Province"), description: "Your province to send your cart there...", attributes: ["required"]),
                Struct::Field(type: "Text", key: "City", value: \_::$User->GetMetaValue("City"), description: "Your city to send your cart there...", attributes: ["required"]),
                Struct::Field(type: "Texts", key: "Address", value: \_::$User->GetMetaValue("Address") ?? \_::$User->GetValue("Address"), description: "A full address to send your cart there...", attributes: ["required"]),
                Struct::Field(type: "Text", key: "PostalCode", value: \_::$User->GetMetaValue("PostalCode"), description: "Your exact postal code (zipcode)")
            ] : [
                // Struct::Field(type: "Text", key: "Country", value: \_::$User->GetMetaValue("Country"), description: "Your country to send your cart there..."),
                // Struct::Field(type: "Text", key: "Province", value: \_::$User->GetMetaValue("Province"), description: "Your province to send your cart there..."),
                // Struct::Field(type: "Text", key: "City", value: \_::$User->GetMetaValue("City"), description: "Your city to send your cart there..."),
                Struct::Field(type: "Texts", key: "Address", value: \_::$User->GetMetaValue("Address") ?? \_::$User->GetValue("Address"), description: "Your optional full address..."),
                Struct::Field(type: "Text", key: "PostalCode", value: \_::$User->GetMetaValue("PostalCode"), description: "Your exact postal code (zipcode)")
            ]),
            Struct::Field(type: "Texts", key: "Description", value: \_::$User->GetMetaValue("CartDescription"), description: "Your description for vendor")
        ],
        null,
        ["Id" => $id, "method" => "POST"]
    );
$module->BackButton = Struct::Button(\_::$Joint->Shop->CartTitle, \_::$Joint->Shop->CartUrlPath, ["class" => "col-sm-4"]);
$module->NextButton = Struct::Button($sd?\_::$Joint->Shop->PaymentTitle:\_::$Joint->Shop->PreviewTitle, "if(_('#$id').validate()) submitForm('#$id');", ["class" => "btn main col-sm"]);
if ($metadata = \MiMFa\Library\Convert::FromJson(\_::$Joint->Shop->OptionsMetaData))
    pod($module, $metadata);
pod($module, $data);
$module->Render();
response(Struct::CloseTag("div"));