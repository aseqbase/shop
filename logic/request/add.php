<?php
if (get($data,"MerchandiseId"))
{
    logic("request/base");
    $request = get($data, "Request");
    $count = get($data, "Count") ?? 1;
    if ($count <= table("Merchandise")->SelectValue("Count", "`Id`=:Id", [":Id" => get($data, "MerchandiseId")]))
        if (
            table("Request")->Insert([
                "MerchandiseId" => get($data, "MerchandiseId"),
                ...(\_::$Back->User->Id ? ["UserId" => \_::$Back->User->Id] : []),
                "UserCode" => getClientCode(),
                "Request" => $request,
                "Count" => $count
            ])
        )
            return $count;
}
?>