<?php
function RequestConditionQuery($tableName = null, $userId = null)
{
    $userId = $userId ?? \_::$User->Id;
    if ($tableName)
        $tableName .= ".";
    return "(
        {$tableName}Collection IS NULL AND
        (" .
            (isValid($userId) ? "{$tableName}UserId=$userId OR " : "") .
            "({$tableName}UserId IS NULL AND {$tableName}UserCode='" . getClientCode() . "')
        )
    )";
}
?>