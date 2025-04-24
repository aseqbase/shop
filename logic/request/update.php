<?php
logic("request/base");
if (get($data, "MerchandiseId")) {
    $count = get($data, "Count");
    if (
        $count <= 0 && table("Request")->Delete("`MerchandiseId`=:MerchandiseId AND " . RequestConditionQuery(), [
            ":MerchandiseId" => get($data, "MerchandiseId")
        ])
    )
        return $count;
    elseif ($count <= $c = table("Merchandise")->SelectValue("Count", "`Id`=:Id", [":Id" => get($data, "MerchandiseId")]))
        if (
            table("Request")->Update(
                "`MerchandiseId`=:MerchandiseId AND " . RequestConditionQuery(),
                [
                    ":MerchandiseId" => get($data, "MerchandiseId"),
                    ...(\_::$Back->User->Id ? ["UserId" => \_::$Back->User->Id] : []),
                    "Count" => $count
                ]
            )
        )
            return $count;
}
?>