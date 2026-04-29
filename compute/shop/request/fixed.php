<?php
$r = table("Shop_Request");
return compute("shop/content/merchandises", [
    "Condition" => "$r->Name.Collection='Collected' AND $r->Name.`Count`>0",
    "RequestTable"=>$r
]);