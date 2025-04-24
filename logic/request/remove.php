<?php
logic("request/base");
if (
    get($data, "MerchandiseId") &&
    table("Request")->Delete("`MerchandiseId`=:MerchandiseId AND " . RequestConditionQuery(), [
        ":MerchandiseId" => get($data, "MerchandiseId")
    ])
)
    return 0;
?>