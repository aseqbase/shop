<?php
$r = table("Request");
return logic("content/merchandises", [
    "Condition" => "$r->Name.`Count`>0",
    "RequestTable"=>$r
])
?>