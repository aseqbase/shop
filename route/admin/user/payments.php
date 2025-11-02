<?php
use MiMFa\Library\Html;
(new Router())
->if(\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/payments",
            "Image" => "credit-card",
            "Title" => "Payments Management"
        ]);
    })
    ->Put(
        function () {
            $id = receivePut("Id");
            if (!$id)
                deliver(Html::Error("It is not a valid transaction!"));
            if (table("Payment")->Update("Id=:Id", [":Id" => $id, "Verify" => true]))
                if ($res = compute("request/complete", ["PaymentId" => $id]))
                    deliverBreaker(Html::Success("The transaction verified successfully!"));
                else {
                    table("Payment")->Update("Id=:Id", [":Id" => $id, "Verify" => 0]);
                    if ($res === false)
                        deliver(Html::Error("Something went wrong!"));
                    else
                        deliver(Html::Warning("There was no requests to verify!"));
                }
            else
                deliver(Html::Error("We could not verify your transaction!"));
        }
    )
    ->Default(function () {
        part("admin/table/payments");
    })
->Handle();
?>