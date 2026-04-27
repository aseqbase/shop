<?php
use MiMFa\Library\Struct;
response(Struct::OpenTag("div", ["class" => "page"]));
module("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = pop($data, "Title") ?? "Succeed";
$module->Description = pop($data, "Description") ?? MiMFa\Library\Struct::Success("Thanks, your payment is completed successfully.");
$module->Content = pop($data, "Content");
$module->Image = pop($data, "Image") ?? "check";
$module->Render();
$id = get($data, "Id");
$transaction = \_::$Joint->Finance->GetTransaction($id);
$collection = null;
if ($transaction) $collection = $transaction["Relation"] . "/" . $transaction["RelationId"];
response(Struct::CloseTag("div"));

\_::$Joint->Shop->UseDiscount();

//Add transaction as Collection of the requests
if (!$collection)
    if ($id)
        return warning("Your order will be shipped to you once your transaction is confirmed!" . Struct::$Break . "This may take up to 24 hours.");
    else
        return page(\_::$Joint->Shop->PaymentUrlPath, $data);
$items = compute("shop/request/fixed");
if ($items && table("Request")->SetValue(loop($items, fn($v) => $v["RequestId"]), "Collection", $collection))
    if (compute("shop/response/complete", ["Collection" => $collection]))
        return deliverRedirect(Struct::Success("Your transaction verified successfully!"),  "/finance/". $transaction["Relation"] . "?id=" . $transaction["RelationId"]);
    else
        return warning("A problem was occured! please " . Struct::Link("call to the provider", "contact") . ".");
else
    return warning("Your cart paid before! Track from the " . Struct::Link("requests page", \_::$Joint->Shop->RequestsUrlPath) . "!");