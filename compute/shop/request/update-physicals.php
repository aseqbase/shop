<?php
if ($data) {
    $rt = table("Request");
    return $rt->DataBase->Update($rt->Name. " INNER JOIN ".table("Merchandise")->Name." AS M ON {$rt->Name}.MerchandiseId=M.Id",
            "WHERE ".(\_::$Joint->Shop->DigitalStore?"M.Digital IS FALSE":"(M.Digital IS FALSE OR M.Digital IS NULL)")." AND ".
            \_::$Joint->Shop->CartCondition(),
            [
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                ...$data
            ]
        );
}
?>