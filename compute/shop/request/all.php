<?php
$r = table("Request");
return compute("shop/content/merchandises", [
    "Condition" => "$r->Name.`Count`>0",
    "RequestTable"=>$r
]);