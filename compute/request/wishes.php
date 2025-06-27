<?php
$r = table("Request");
return compute("content/merchandises", [
    "Condition" => "$r->Name.Like IS TRUE AND $r->Name.Count=0",
    "RequestTable"=>$r
])
?>