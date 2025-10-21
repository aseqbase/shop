<?php
(new Router())
->if(\_::$User->GetAccess(\_::$User->AdminAccess))
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