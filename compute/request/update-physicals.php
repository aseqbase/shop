<?php
compute("request/base");
if ($data) {
    $rt = table("Request");
    return $rt->DataBase->Update($rt->Name. " INNER JOIN ".table("Merchandise")->Name." AS M ON {$rt->Name}.MerchandiseId=M.Id",
            "WHERE ".(\_::$Config->DigitalStore?"M.Digital IS FALSE":"(M.Digital IS FALSE OR M.Digital IS NULL)")." AND ".
            RequestConditionQuery(),
            [
                ...(\_::$Back->User->Id ? ["UserId" => \_::$Back->User->Id] : []),
                ...$data
            ]
        );
}
?>