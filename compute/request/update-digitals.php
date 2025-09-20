<?php
compute("request/base");
if ($data) {
    $rt = table("Request");
    return $rt->DataBase->Update($rt->Name. " INNER JOIN ".table("Merchandise")->Name." AS M ON {$rt->Name}.MerchandiseId=M.Id",
            "WHERE ".(\_::$Config->DigitalStore?"(M.Digital IS TRUE OR M.Digital IS NULL)":"M.Digital IS TRUE")." AND ".
            RequestConditionQuery(),
            [
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                ...$data
            ]
        );
}
?>