<?php
if ($mid = get($data, "MerchandiseId")) {
    $request = get($data, "Request");
    $rid = get($data, "RequestId");
    $like = get($data, "Group");
    $count = get($data, "Count") ?: ($request ? 1 : ($rid ? table("Request")->GetValue($rid, "Count", 0) : 0));
    $count = $count ?: ($like ? 0 : 1);
    $merch = table("Merchandise")->Get($mid);
    if ($count)
        $count = min($count, min($merch["Count"], isEmpty($merch["Limit"]) ? $merch["Count"] : $merch["Limit"]));
    if (\_::$User->Id)
        $rid = table("Request")->SelectValue("Id", "MerchandiseId=:MerchandiseId AND " . \_::$Joint->Shop->CartCondition(), [":MerchandiseId" => $mid]);
    $amount = \_::$Joint->Shop->ComputeAmount(
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
    );
    if ($rid) {
        if (
            table("Request")->Set($rid, [
                "MerchandiseId" => $mid,
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                "UserCode" => getClientCode(),
                ...(is_null($like) ? [] : ["Group" => $like ? 1 : 0]),
                "Count" => $count,
                "Amount" => $amount
            ])
        )
            return is_null($like) ? $count : ($like ? "true" : "false");
    } elseif (
        table("Request")->Insert([
            "MerchandiseId" => $mid,
            ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
            "UserCode" => getClientCode(),
            ...(is_null($like) ? [] : ["Group" => $like ? 1 : 0]),
            "Count" => $count,
            "Amount" => $amount
        ])
    )
        return is_null($like) ? $count : ($like ? "true" : "false");
}