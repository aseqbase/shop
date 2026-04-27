<?php
$r = table("Request");
return compute("shop/content/merchandises", [
    "Condition" => "$r->Name.Group IS NOT NULL AND $r->Name.Group NOT IN ('',0,'0')",
    "RequestTable"=>$r
]);