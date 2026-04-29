<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
if (!$data)
    return null;
if (!\_::$User->HasAccess())
    return part(\_::$User->InHandlerPath, ["AllowHeader" => false, "ContentClass" => "col"], print: false);

module("Table");
$module = new Table("Shop_Response");
$module->SelectQuery = table("Shop_Response")->As("R")
    ->Join(table("Shop_Merchandise")->As("M"), "R.MerchandiseId=M.Id")
    ->Join(table("Content")->As("C"), "M.ContentId=C.Id")
    ->OrderBy("R.CreateTime DESC")
    ->SelectQuery("*, R.Id AS 'Id', R.Id AS 'Code', R.Status AS 'Status',
                COALESCE(M.Title, C.Title) AS 'Item', R.MerchandiseId AS 'ItemPath',
                M.Digital AS 'Digital', R.Collection AS 'Collection',
                R.Address AS 'Destination', M.Unit AS 'Unit', R.Description AS 'Description',
                R.Count AS 'Count', R.Amount AS 'Amount'", (\_::$User->HasAccess(\_::$Joint->Shop->SellingAccess) ? "" : "R.UserId=" . \_::$User->Id . " AND ") . "R.Id IN (" . join(",", $data) . ")");
$module->KeyColumns = ["Item"];
$module->IncludeColumns = ["Status", "Item", "Code", "Description", "Count", "Amount", "Destination", "UpdateTime"];
$module->AllowServerSide = true;
$module->Updatable = false;
$module->SearchAccess = false;
$module->ExportAccess = false;
$module->ViewAccess = false;
$rsc = count(\_::$Joint->Shop->ResponsesStatuses) / 2;
$returnable = receiveGet("Returnable");
$srp = Script::Convert(\_::$Joint->Shop->ResponsesUrlPath);
$module->PrependControls = function ($id, $row) use ($srp, $rsc, $returnable) {
    $si = \_::$Joint->Shop->StatusToIInt($row["Status"]);
    $btns = [];
    if (
        $si > \_::$Joint->Shop->StatusToIInt(\_::$Joint->Shop->ReceivedStatus) && (
            isValid($row, "Private") ||
            isValid($row, "PrivateGenerator") ||
            isValid($row, "PrivateSubject") ||
            isValid($row, "PrivateMessage") ||
            isValid($row, "PrivateAttach")
        )
    )
        $btns[] = Struct::Icon("eye", "sendPatch($srp, {Id:$id, State:1})", ["class" => "be magenta", "Style" => "padding: 0px 5px;", "ToolTip" => "To see the private results"]);
    if ($returnable && \_::$Joint->Shop->Returnable) {
        if (!$si)
            $btns[] = Struct::Icon("undo", "desc = " . Script::Prompt("Why do you want to cancel the returning item?", "I made a mistake!") . "; if(desc) sendPatch($srp, {Id:$id, State:10, Description:desc})", ["class" => "be fore green", "ToolTip" => "To cancel returning the 'merchandise'"]);
        elseif ($si > 0 && $row["Status"] !== \_::$Joint->Shop->FinishedStatus)
            $btns[] = Struct::Icon("close", "desc = " . Script::Prompt("Why do you want to return this item?") . "; if(desc) sendPatch($srp, {Id:$id, State:-10, Description:desc})", ["class" => "be fore red", "ToolTip" => "Return 'merchandise'"]);
    }
    return $btns;
};
$sc = 127 / ($rsc + 2);
$module->CellsValues = [
    "Item" => function ($v, $k, $r) {
        return Struct::Link($v, \_::$Joint->Shop->ItemRootUrlPath . $r["ItemPath"], ["target" => "blank"]);
    },
    "Status" => function ($v) use ($sc) {
        $vi = \_::$Joint->Shop->StatusToIInt($v = $v?:\_::$Joint->Shop->UncheckedStatus);
        return Struct::Span(\_::$Joint->Shop->ResponsesStatuses[$v] ?? "Undefined", ["class" => "response-status " . ($vi > 0 ? "success" : ($vi < 0 ? "error" : "")), "style" => "background-color:rgba(" . (128 - $vi * $sc) . ", " . (128 + $vi * $sc) . ", 0)"]);
    },
    "Destination" => function ($v, $k, $r) {
        return Struct::Icon($r["Digital"] ? "envelope" : "truck") . " " . $v;
    },
    "Count" => function ($v, $k, $r) {
        return $v . ($v ? $r["Unit"] ?? \_::$Joint->Shop->ItemsUnit : "");
    },
    "Amount" => function ($v, $k, $r) {
        return \_::$Joint->Finance->AmountStruct($v);
    }
];
$superAccess = \_::$User->HasAccess(\_::$User->SuperAccess);
$users = table("User")->SelectPairs("Id", "Name");
$module->CellsTypes = [
    "Id" => $superAccess ? "disabled" : false,
    "UserId" => !$superAccess ? "disabled" : function ($t, $v) use ($users, $superAccess) {
        $std = new stdClass();
        $std->Title = "User";
        $std->Type = $superAccess ? "select" : "hidden";
        $std->Options = $users;
        if (!isValid($v))
            $std->Value = \_::$User->Id;
        return $std;
    },
    "MerchandiseId" => !$superAccess ? "disabled" : function ($t, $v) {
        $std = new stdClass();
        $std->Title = "Merchandise";
        $std->Type = "disabled";
        $std->Value = table("Shop_Merchandise")->As("M")
            ->Join(table("Content")->As("C"))
            ->SelectValue("C.Title", "M.Id=:Id", [":Id" => $v]);
        return $std;
    },
    "UserCode" => !$superAccess ? "disabled" : "TINYTEXT",
    "Collection" => !$superAccess ? "disabled" : "TINYTEXT",
    "Status" => function ($t, $v) {
        $std = new stdClass();
        $std->Title = "Status";
        $std->Type = "select";
        $std->Options = \_::$Joint->Shop->ResponsesStatuses;
        $std->Value = $v;
        return $std;
    },
    "Count" => !$superAccess ? "disabled" : "float",
    "Amount" => !$superAccess ? "disabled" : "float",
    "Contact" => "TINYTEXT",
    "Address" => "text",
    "AuthorId" => function ($t, $v) use ($users) {
        $std = new stdClass();
        $std->Title = "Responsible";
        $std->Type = "select";
        $std->Options = $users;
        if (!isValid($v))
            $std->Value = \_::$User->Id;
        return $std;
    },
    "Subject" => "varchar",
    "Description" => "mediumtext",
    "Content" => !$superAccess ? "disabled" : "Content",
    "Priority" => "int",
    "Attach" => "json",
    "EditorId" => function ($t, $v) use ($users, $superAccess) {
        $std = new stdClass();
        $std->Title = "Editor";
        $std->Type = $superAccess ? "select" : "disabled";
        $std->Options = $users;
        $std->Value = \_::$User->Id;
        return $std;
    },
    "UpdateTime" => function ($t, $v) {
        $std = new stdClass();
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : "hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function ($t, $v) {
        return \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
    },
    "MetaData" => function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "json";
        if (!$v && \_::$Front->AllowTranslate)
            $std->Value = "{\"lang\":\"" . \_::$Front->Translate->Language . "\"}";
        return $std;
    }
];
pod($module, $data);
return Struct::Style("
            .response-status{
                padding:4px var(--size-0);
                text-shadow: var(--shadow-1);
                color:var(--color-white);
            }") . $module->ToString() .
    Struct::Division(
        ($returnable || !\_::$Joint->Shop->Returnable ? "" : Struct::Button("Return 'merchandise'", \_::$Address->Url . "&Returnable=true")) .
        Struct::Button("See other 'requests'", \_::$Joint->Shop->RequestsUrlPath),
        ["class" => "be flex end middle"]
    );