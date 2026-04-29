<?php
if (($rid = get($data, "RequestId")) || ($mid = get($data, "MerchandiseId"))) {
    if (($count = get($data, "Count")) !== null) {
        $mid = $mid ?? table("Shop_Request")->SelectValue("MerchandiseId", "`Id`=:Id AND " . \_::$Joint->Shop->CartCondition(), [":Id" => $rid]);
        $merch = table("Shop_Merchandise")->get($mid);
        $count = min($count, min($merch["Count"], isEmpty($merch["Limit"]) ? $merch["Count"] : $merch["Limit"]));
        if ($count <= 0)
            return compute("shop/request/remove", $data);
        elseif (
            table("Shop_Request")->Update(
                "(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . \_::$Joint->Shop->CartCondition(),
                [
                    ":Id" => $rid,
                    ":MerchandiseId" => $mid,
                    ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                    "Count" => $count,
                    "Amount" => \_::$Joint->Shop->ComputeAmount(
                        null,
                        \_::$Joint->Finance->StandardCurrency(
                            $merch["Amount"],
                            $merch["Currency"] ?: \_::$Joint->Finance->Currency
                        ),
                        $count,
                        $merch["Discount"],
                        $merch["Digital"],
                        \_::$User->GetValue("Address"),
                        $merch["MetaData"]
                    )
                ]
            )
        )
            return $count;
    }
    if (($address = get($data, "Address")) !== null) {
        if (
            table("Shop_Request")->Update(
                "(`Id`=:Id OR `MerchandiseId`=:MerchandiseId) AND " . \_::$Joint->Shop->CartCondition(),
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