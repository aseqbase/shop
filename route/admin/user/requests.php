<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/requests",
            "Image" => "shopping-basket",
            "Title" => "Requests Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/requests");
    })
    ->Handle();
?>