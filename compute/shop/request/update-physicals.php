<?php
if ($data) {
    $rt = table("Shop_Request");
    return $rt->DataBase->Update($rt->Name. " INNER JOIN ".table("Shop_Merchandise")->Name." AS M ON {$rt->Name}.MerchandiseId=M.Id",
            "WHERE ".(\_::$Joint->Shop->DigitalStore?"M.Digital IS FALSE":"(M.Digital IS FALSE OR M.Digital IS NULL)")." AND ".
            \_::$Joint->Shop->CartCondition(),
            [
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                ...$data
            ]
        );
}
?>