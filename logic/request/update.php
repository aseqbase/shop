<?php
logic("request/base");
if (($rid = get($data, "RequestId")) || ($mid = get($data, "MerchandiseId"))) {
    $count = get($data, "Count");
    $mid = $mid ?? table("Request")->SelectValue("MerchandiseId", "`Id`=:Id AND " . RequestConditionQuery(), [":Id" => $rid]);
    if (
        $count <= 0 &&
        table("Request")->Delete("(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . RequestConditionQuery(), [
            ":Id" => $rid,
            ":MerchandiseId" => $mid
        ])
    )
        return $count;
    elseif ($count <= $c = table("Merchandise")->SelectValue("Count", "`Id`=:Id", [":Id" => $mid])) {
        if (
            table("Request")->Update(
                "(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . RequestConditionQuery(),
                [
                    ":Id" => $rid,
                    ":MerchandiseId" => $mid,
                    ...(\_::$Back->User->Id ? ["UserId" => \_::$Back->User->Id] : []),
                    "Count" => $count = min($c, $count)
                ]
            )
        )
            return $count;
    }
}
?>