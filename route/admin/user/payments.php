<?php
use MiMFa\Library\Html;
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/payments",
            "Image" => "credit-card",
            "Title" => "Payments Management"
        ]);
    })
    ->Put(
        function () {
            $id = \Req::ReceivePut("Id");
            if (!$id)
                \Res::End(Html::Error("It is not a valid transaction!"));
            if (table("Payment")->Update("Id=:Id", [":Id" => $id, "Verify" => true]))
                if ($res = compute("request/complete", ["PaymentId" => $id]))
                    \Res::Flip(Html::Success("The transaction verified successfully!"));
                else {
                    table("Payment")->Update("Id=:Id", [":Id" => $id, "Verify" => 0]);
                    if ($res === false)
                        \Res::End(Html::Error("There a problem is occured!"));
                    else
                        \Res::End(Html::Warning("There was no requests to verify!"));
                }
            else
                \Res::End(Html::Error("We could not verify your transaction!"));
        }
    )
    ->Default(function () {
        part("admin/table/payments");
    })
->Handle();
?>