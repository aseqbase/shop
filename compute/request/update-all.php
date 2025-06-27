<?php
compute("request/base");
if ($data) {
    return table("Request")->Update(
            RequestConditionQuery(),
            [
                ...(\_::$Back->User->Id ? ["UserId" => \_::$Back->User->Id] : []),
                ...$data
            ]
        );
}
?>