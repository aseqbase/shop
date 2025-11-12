<?php
$data["Description"] = pop($data, "Description")??MiMFa\Library\Struct::Error("
It seams your payment is failed or canceled.
Please try again...
");
page("cart/payment", $data);
?>