<?php
$r = table("Request");
return compute("content/merchandises", [
    "Condition" => "$r->Name.`Count`>0",
    "RequestTable"=>$r
])
?>