<?php
if ($mid = get($data,"MerchandiseId"))
{
    compute("request/base");
    $request = get($data, "Request");
    $count = get($data, "Count") ?? 1;
    if ($count <= table("Merchandise")->SelectValue("Count", "Id=:Id", [":Id" => $mid]))
        if (
            table("Request")->Insert([
                "MerchandiseId" => $mid,
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                "UserCode" => getClientCode(),
                "Request" => $request,
                "Count" => $count
            ])
        )
            return $count;
}
?>