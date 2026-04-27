<?php
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
if (!\_::$User->HasAccess(\_::$User->UserAccess))
    return page(\_::$Joint->Shop->SignInUrlPath, $data);
$successAction = "compute/shop/response/succeed";
$errorAction = "compute/shop/response/failed";
$trackAction = "compute/shop/response/track";
response(Struct::OpenTag("div", ["class" => "page"]));
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title") ?: \_::$Joint->Shop->PaymentTitle;
$module->Description = pop($data, "Description") ?: \_::$Joint->Shop->PaymentDescription;
$module->Content = pop($data, "Content") ?: \_::$Joint->Shop->PaymentContent;
$module->Image = pop($data, "Image") ?: \_::$Joint->Shop->PaymentImage;
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
$module->Items = [];
$PostItems = [];
foreach (pop($data, "Items") ?? compute("shop/request/fix-currents", receive()) as $key => $value)
    if (isEmpty(get($value, "MerchandisePrice")))
        $PostItems[] = $value;
    else
        $module->Items[] = $value;

$module->AllowItems = false;
$bill = $module->ComputeInvoice();
$a = get($bill, "Amount");
$u = get($bill, "Currency");
$d = get($bill, "Paid");

library("finance/Account");
$account_MDT = new \MiMFa\Library\Finance\Account();
$balance = $account_MDT->GetBalanceAmount(\_::$User->Id, $u ?: \_::$Joint->Finance->Currency);

$pat = join(" ", [__("Pay"), Struct::Number($a), __($u ?: \_::$Joint->Finance->ShownCurrency), __("via") . ":"]);
$pbt = join(" ", [__("Pay"), Struct::Number($a - min($a, $balance)), __($u ?: \_::$Joint->Finance->ShownCurrency), __("via") . ":"]);

$T = md5("U" . getClientCode(\_::$User->Id) . "T" . get($module->Items, count($module->Items) - 1, "RequestCreateTime"));

// $bill["Content"] = [
//     ["Code", "Image", "Title", "Price", "Count", "Discount", "Amount"],
//     ...loop(pop($bill, "Items"), fn($v) => [
//         $v["Merchandise"]["Id"],
//         $v["Content"]["Image"] ? Struct::Image("", $v["Content"]["Image"], ["style" => "max-width:var(--size-max);"]) : "",
//         Struct::Link($v["Content"]["Title"], \_::$Joint->Shop->ItemRootUrlPath . $v["Merchandise"]["Id"]),
//         $v["Merchandise"]["Price"] . __(\_::$Joint->Finance->ShownCurrency),
//         $v["Request"]["Count"],
//         $v["Request"]["Price"] - $v["Request"]["Amount"],
//         $v["Request"]["Amount"] . __(\_::$Joint->Finance->ShownCurrency)
//     ])
// ];
$bill["Title"] = "'Buying' invoice";
$bill["Description"] = \_::$User->GetMetaValue("CartDescription");
$bill["Success"] = $successAction;
$bill["Error"] = $errorAction;
$bill["Relation"] = \_::$Joint->Shop->RequestsUrlPath;
$bill["MetaData"] = [
    "PriceParams" => pop($bill, "Params"),
    "Run" => [
        "Name" => $trackAction,
        "Data" => loop($module->Items, fn($v) => $v["RequestId"])
    ]
];
if ($PostItems)
    $bill["Action"] = [
        "Name" => "compute/shop/response/unknowns",
        "Data" => loop($PostItems, fn($v) => $v["RequestId"])
    ];

setSecret($T, $bill, 10000000);

$methods = \_::$Joint->Finance->GetPlatforms($a, $u);
$p = receiveGet(\_::$Joint->Finance->PlatformRequestKey) ?? array_key_first($methods);
$module->Content = ($balance ? Struct::Field("switch", \_::$Joint->Finance->WalletRequestKey, false, title: Struct::Bold(join(" ", [__("Pay "), Struct::Number(min($a, $balance)) . __($u ?: \_::$Joint->Finance->ShownCurrency), __("by wallet")])), attributes: [
    "wrapper" => [
        "class" => "be middle",
        "style" => "gap: var(--size-0);"
    ],
    "class" => "fa-2x",
    "onchange" => "_('.price-payable').html(this.checked?" . Script::Convert($pbt) . ":" . Script::Convert($pat) . ");",
]) : "") . Struct::Bold($pat, null, ["class" => "price-payable"]) . Struct::Box([
                loop(
                    $methods,
                    function ($platform, $k) use ($p) {
                        return Struct::Button(
                            Struct::Label(
                                Struct::Media($platform->Image) .
                                Struct::Division(
                                    Struct::Span($platform->Title, null, ["class" => "name"]) .
                                    Struct::Small($platform->Description) .
                                    Struct::RadioInput(\_::$Joint->Finance->PlatformRequestKey, $platform->Name, $k === $p ? ["checked" => "checked"] : []),
                                    ["class" => "name"]
                                )
                            )
                        );
                    }
                )
            ], ["class" => "payment-platforms"]);
$module->BackButton = Struct::Button(\_::$Joint->Shop->OptionsTitle, \_::$Joint->Shop->OptionsUrlPath, ["class" => "col-sm-4"]);
$module->NextButton = Struct::SubmitButton(\_::$Joint->Finance->SubmitRequestKey, "Pay", ["class" => "btn main col-sm"]);

style("
    .payment-platforms{
        display: flex;
        flex-wrap: wrap;
        gap: var(--size-0);
    }
    .payment-platforms .button{
        background-color: var(--back-color);
        color: var(--fore-color);
        padding: 0px;
        width: fit-content;
        height: fit-content;
    }
    .payment-platforms .button .label{
        margin: 0px;
        padding: var(--size-0);
        border-radius: var(--radius-2);
        border: var(--border-1) var(--back-color-input);
        text-align: initial;
        display: flex;
        justify-content: center;
        align-items: center;
        align-content: center;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: var(--size-0);
    }
    .payment-platforms .button .media{
        width: var(--size-max);
        max-height: var(--size-max);
    }
    .payment-platforms .button .name{
        height: 100%;
        cursor: pointer;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }
    .payment-platforms .button:has(input:checked){
        background-color: var(--back-color-output);
        color: var(--fore-color-output);
        border-color: var(--back-color-special-output);
        box-shadow: var(--shadow-2);
    }
    .payment-platforms :is(.button, .button:has(input:checked)):hover{
        background-color: var(--back-color-special-output);
        color: var(--fore-color-special-output);
        border-color: var(--fore-color-special-output);
    }
    .payment-platforms .button .input{
        display: none;
    }
    .payment-platforms .button .media{
        font-size: 2em;
        min-height: 1em;
        min-width: 1em;
    }
    .payment-platforms .button .name{
        display: block;
    }
    .payment-platforms .button .name .small{
        display: block;
    }
    .payment-platforms .button:not(:hover, :active) .name .small{
        color: #888;
    }
");
response(
    Struct::Form(
        $module->ToString(),
        rtrim(\_::$Joint->Finance->PaymentUrlPath, "\/\\") . "?" . \_::$Joint->Finance->TokenRequestKey . "=" . urlencode($T),
        [
            "Method" => \_::$Joint->Finance->PeymentMethod,
            "Class" => "payment container",
            "Interaction" => true
        ]
    ) .
    Struct::CloseTag("div")
);