<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
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