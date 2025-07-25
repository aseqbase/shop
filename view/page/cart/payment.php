<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Html;

module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = grab($data, "Title") ?? "Payment";
$module->Description = grab($data, "Description");
$module->Content = grab($data, "Content");
$module->Image = grab($data, "Image") ?? "credit-card";
$module->Render();
module("CartCollection");
$module = new MiMFa\Module\CartCollection();
$module->Items = grab($data, "Items") ?? compute("request/fix-currents", \Req::Receive());
$module->ShowItems = false;
$bill = $module->ComputeBill();
$transaction = Convert::ToJson([
    "Description" => "Buy from " . \_::$Info->FullOwner,
    "Relation" => "CU".\_::$Back->User->Id . "N" . count($module->Items) . "T" . first(preg_split("/\./", microtime(true))),
    "Source" => \_::$Back->User->Name,
    "Value" => $bill["Price"],
    "Unit" => \_::$Config->PriceUnit,
    "SuccessPath" => "/cart/success",
    "FailPath" => "/cart/fail"
]);
$id = "payment_method_" . getId(true);
$methods = compute("get/payment-methods//");
$mc = count($methods) - 1;
$module->Content = Html::Style("
            #$id .button{
                background-color: var(--back-color-0);
                color: var(--fore-color-0);
                padding: 0px;
                margin: calc(var(--size-0) * .25);
                border-radius: var(--radius-1);
                border: var(--border-1) var(--back-color-1);
                text-align: initial;
            }
            #$id .button .label{
                height: 100%;
                padding: var(--size-0);
                cursor: pointer;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: var(--size-0);
            }
            #$id .button:has(input:checked){
                background-color: var(--back-color-2);
                color: var(--fore-color-2);
                border-color: var(--fore-color-2);
            }
            #$id :is(.button, .button:has(input:checked)):hover{
                background-color: var(--back-color-5);
                color: var(--fore-color-5);
                border-color: var(--fore-color-5);
            }
            #$id .button .input{
                display: none;
            }
            #$id .button .media{
                font-size: 2em;
                min-height: 1em;
                min-width: 1em;
            }
            #$id .button .name{
                width: calc(90% - 2em);
            }
            #$id .button .name .big{
                display: block;
            }
            #$id .button:not(:hover, :active) .name .small{
                color: #888;
            }
        ") .
    Html::Frame(
        join(
            "",
            loop($methods, function ($v, $k) use ($mc, $transaction) {
                $trans = $transaction;
                if (isset($v["Transaction"])) set($trans, $v["Transaction"]);
                $path = get($v, "Path") . "?" . urlencode(encrypt($trans));
                return ($k % 2 === 0 ? "<div class='row'>" : "") . Html::Button(
                    Html::Label(
                        Html::Media(get($v, "Image")) . Html::Division(Html::Big(get($v, "Title")) . Html::Small(get($v, "Description")), ["class" => "name"]),
                        Html::RadioInput("PayMethod", get($v, "IsDefault") ?? $k == 0, ["data-value" => $path])
                    ), $path
                    , ["class" => "col-md"]
                ) . ((($k + 1) % 2 === 0) || $k >= $mc ? "</div>" : "");
            })
        ),
        ["id" => $id]
    );
$module->BackButton = Html::Button("Options", "/cart/options", ["class" => "col-sm-4"]);
$module->NextButton = Html::Button("Pay", "
        method = document.querySelector('#$id .input[name=\\'PayMethod\\']:checked');
        load(method.getAttribute('data-value'));
    ", ["class" => "btn main col-sm"]);
$module->Render();
?>