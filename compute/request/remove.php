<?php
compute("request/base");
if (
    (
        get($data, "RequestId") ||
        get($data, "MerchandiseId")
    ) &&
    table("Request")->Delete("(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . RequestConditionQuery(), [
        ":Id" => get($data, "RequestId"),
        ":MerchandiseId" => get($data, "MerchandiseId")
    ])
)
    return 0;
?>