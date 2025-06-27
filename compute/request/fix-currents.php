<?php
$items = compute("request/currents");
table("Request")->Update("Id=:Id", 
    loop(
        $items,
        fn($v)=>[
            ":Id"=>$v["RequestId"],
            ":Price"=>$v["RequestCount"] * ($p = (\_::$Config->StandardPrice)(get($v, 'MerchandisePrice'), $v["MerchandisePriceUnit"])) - $v["MerchandiseDiscount"] * $p/100
        ]
    )
);
return $items;
?>