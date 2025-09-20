<?php
library("Math");
use MiMFa\Library\Math;

compute("request/base");
if (($rid = get($data, "RequestId")) || ($mid = get($data, "MerchandiseId"))) {
    if(($count = get($data, "Count")) !== null){
        $mid = $mid ?? table("Request")->SelectValue("MerchandiseId", "`Id`=:Id AND " . RequestConditionQuery(), [":Id" => $rid]);
        if (
            $count <= 0 &&
            table("Request")->Delete("(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . RequestConditionQuery(), [
                ":Id" => $rid,
                ":MerchandiseId" => $mid
            ])
        )
            return $count;
        elseif ($count <= $c = Math::Minimum(table("Merchandise")->SelectRow("Count, Limit", "`Id`=:Id", [":Id" => $mid]))) {
            if (
                table("Request")->Update(
                    "(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . RequestConditionQuery(),
                    [
                        ":Id" => $rid,
                        ":MerchandiseId" => $mid,
                        ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                        "Count" => $count = min($c, $count)
                    ]
                )
            )
                return $count;
        }
    }
    if (($address = get($data, "Address")) !== null) {
        if (
            table("Request")->Update(
                "(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . RequestConditionQuery(),
                [
                    ":Id" => $rid,
                    ":MerchandiseId" => $mid,
                    ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                    "Address" => $address
                ]
            )
        )
            return $address;
    }
}
?>