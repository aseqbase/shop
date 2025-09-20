<?php
compute("request/base");
if ($data) {
    return table("Request")->Update(
            RequestConditionQuery(),
            [
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                ...$data
            ]
        );
}
?>