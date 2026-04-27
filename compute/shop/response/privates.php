<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
$row = get($data, "Merchandise");
$res = get($data, "Request");
$result = [];
$p = null;
if ($p = get($row, "PrivateSubject"))
    $result[] = Struct::Heading2(Convert::FromDynamicString($p));
if ($p = get($row, "PrivateMessage"))
    $result[] = Convert::FromDynamicString($p);
if ($p = get($row, "PrivateGenerator"))
    $result[] = run($p, $res);
if ($p = get($row, "PrivateAttach")) {
    $result[] = Struct::$BreakLine;
    $result[] = Struct::Items(Convert::FromJson($p));
}

return $result?Convert::ToString($result):null;