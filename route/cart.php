<?php
use MiMFa\Library\Router;
(new Router())
->Route("cart/payment")
    ->Put(fn() => \Res::Set(logic("request/update", \Req::Receive())))
    ->Get(
    fn () =>
        view(\_::$Config->DefaultViewName, [
            "Name" => "cart/payment",
            "Title" => "Payment",
            "Items" => logic("request/all", \Req::Receive())
        ])
    )
->Route("cart/options")
    ->Put(fn() => \Res::Set(logic("request/add", \Req::Receive())))
    ->Patch(fn() => \Res::Set(logic("request/update", \Req::Receive())))
    ->Delete(fn() => \Res::Set(logic("request/remove", \Req::Receive())))
    ->Get(
    fn () =>
        view(\_::$Config->DefaultViewName, [
            "Name" => "cart/options",
            "Title" => "My Options",
            "Items" => logic("request/all", \Req::Receive())
        ])
    )
->Route("cart(/all)?")
    ->Put(fn() => \Res::Set(logic("request/add", \Req::Receive())))
    ->Patch(fn() => \Res::Set(logic("request/update", \Req::Receive())))
    ->Delete(fn() => \Res::Set(logic("request/remove", \Req::Receive())))
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, [
                "Name" => "cart/all",
                "Title" => "My Shopping Cart",
                "Image" => "shopping-cart",
                "Items" => logic("request/all", \Req::Receive())
            ])
    )->Handle();
?>