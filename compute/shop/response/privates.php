<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
$data = $data ?? [];
if (!$data)
    return null;
$row = get($data, "Merchandise");
$res = get($data, "Request");
$result = [];
$p = null;
if ($p = get($row, "PrivateSubject"))
    $result[] = Struct::Heading2(Convert::FromDynamicString($p));
if ($p = get($row, "PrivateMessage"))
    $result[] = Struct::Convert(Convert::FromDynamicString($p));
if ($p = get($row, "PrivateGenerator")) {
    if (isAbsoluteUrl($p)) {
        $search = [];
        $replacement = [];
        foreach ($res as $key => $value) {
            $search[] = "{{$key}}";
            $replacement[] = urlencode($value??"");
        }
        $search[] = "{Index}";
        $replacement[] = -1;
        $btns = [];
        $result[] = Struct::$BreakLine;
        for ($i = 0; $i < $res["Count"]; $i++){
            $replacement[count($replacement)-1] = $i;
            $url = str_replace($search, $replacement, $p);
            $btns[] = Struct::Button(Struct::Icon("download").__("'Link' ").($i+1),$url);
            load($url, true);
        }
        $result[] = Struct::Center(join(" ", $btns));
    } else $result[] = run($p, $res);
}
if ($p = get($row, "PrivateAttach")) {
    $result[] = Struct::$BreakLine;
    $result[] = Struct::Items(Convert::FromJson($p));
}

return $result ? Convert::ToString($result) : null;