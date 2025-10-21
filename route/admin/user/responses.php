<?php
(new Router())
->if(\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/responses",
            "Image" => "truck",
            "Title" => "Responses Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/responses");
    })
    ->Handle();
?>