<?php
$like = get($data, "Group");
$rid = get($data, "RequestId");
if ($rid) {
    if (is_bool($like))
        return table("Shop_Request")->Update("`Id`=:Id AND " . \_::$Joint->Shop->CartCondition(), [
            ":Id" => $rid,
            ":Group" => $like ? 1 : 0
        ]) ? "false" : "false";
    elseif (table("Shop_Request")->SelectValue("Group", "`Id`=:Id AND " . \_::$Joint->Shop->CartCondition(), [":Id" => $rid]))
        return table("Shop_Request")->Update("`Id`=:Id AND " . \_::$Joint->Shop->CartCondition(), [
            ":Id" => $rid,
            ":Count" => 0,
        ]) ? 0 : 0;
    elseif (table("Shop_Request")->Delete("`Id`=:Id AND " . \_::$Joint->Shop->CartCondition(), [":Id" => $rid]))
        return 0;
}