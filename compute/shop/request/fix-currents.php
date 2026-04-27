<?php
$items = compute("shop/request/currents");
if($items) table("Request")->Update("Id=:Id", 
    loop(
        $items,
        fn($item)=>[
            ":Id"=>$item["RequestId"],
            ":Collection"=>"Collected",
            ":Count"=>$item["RequestCount"],
            ":Amount"=>\_::$Joint->Shop->ComputeAmount($item, \_::$Joint->Finance->StandardCurrency($item["MerchandisePrice"], $item["MerchandiseCurrency"] ?: \_::$Joint->Finance->Currency))
        ]
    )
);
return $items;