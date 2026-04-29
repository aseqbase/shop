<?php
if ($data) {
    return table("Shop_Request")->Update(
            \_::$Joint->Shop->CartCondition(),
            [
                ...(\_::$User->Id ? ["UserId" => \_::$User->Id] : []),
                ...$data
            ]
        );
}
?>