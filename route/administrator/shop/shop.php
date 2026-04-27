<?php
auth(\_::$User->SuperAccess);
$data = $data ?? [];
$routeHandler = function ($data) {
    return \MiMFa\Library\Revise::ToString(\_::$Joint->Shop);
};
(new Router())
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "shop",
            "Title" => "'Shop' Managements"
        ]);
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();