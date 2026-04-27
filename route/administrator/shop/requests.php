<?php
use MiMFa\Library\Convert;
use MiMFa\Library\MetaDataTable;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
auth(\_::$Joint->Shop->AmbassadorsAccess);
$data = $data ?? [];
$routeHandler = function ($data) {
    module("Table");
    $module = new Table("Response");
    $module->SelectQuery = table("Response")->As("R")
        ->Join(table("User")->As("U"), "R.UserId=U.Id")
        ->Join(table("Merchandise")->As("M"), "R.MerchandiseId=M.Id")
        ->Join(table("Content")->As("C"), "M.ContentId=C.Id")
        ->OrderBy("R.Collection ASC, R.UserId ASC, R.CreateTime DESC")
        ->SelectQuery("*, R.Id AS 'Id', R.Id AS 'Code', R.Status AS 'Status',
        COALESCE(M.Title, C.Title) AS 'Item', R.MerchandiseId AS 'ItemPath',
        U.Name AS 'User', U.Signature AS 'UserPath',
        M.Digital AS 'Digital', R.Collection AS 'Collection',
        R.Address AS 'Address', M.Unit AS 'Unit',
        R.Count AS 'Count', R.Amount AS 'Amount', R.Description AS 'Description'");
    $module->KeyColumns = ["Item", "User"];
    $module->IncludeColumns = ["User", "Code", "Item", "Status", "Address", "Collection", "Count", "Amount", "Description", "UpdateTime"];
    $module->FilterColumns = ["Status", "Collection"];
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->UpdateAccess = \_::$Joint->Shop->SellingAccess;
    $module->DeleteAccess =
    $module->ModifyAccess =
        $module->AddAccess =
        $module->DuplicateAccess = \_::$User->SuperAccess;
    $module->PrependControls = function ($id, $row) {
        $v = $row["Status"]?:\_::$Joint->Shop->UncheckedStatus;
        $d = $row["Digital"]??\_::$Joint->Shop->DigitalStore;
        if ($v && strpos($v, ":")) {
            $v = preg_find("/^[A-Za-z]+\b/", $v);
            return [
                ...(($status = $v) ? [Struct::Icon("undo", "desc = " . Script::Prompt($row["Description"] . "\nPut your description", __("Could not return this one!")) . "; if(desc) sendPatch(null, {Id:$id, Status:\"$status\", Description:desc})", ["class" => "be fore green", "ToolTip" => "Switch to '$status'"])] : []),
                ...(($status = \_::$Joint->Shop->ReturnedStatus) ? [Struct::Icon("redo", "sendPatch(null, {Id:$id, Status:\"$status\"})", ["class" => "be fore red", "ToolTip" => "'$status' this one"])] : []),
            ];
        } else{
            $si = \_::$Joint->Shop->StatusToIInt($v);
            $sfi = \_::$Joint->Shop->StatusToIInt($d?\_::$Joint->Shop->DigitalFinalStatus:\_::$Joint->Shop->PhysicalFinalStatus);
            return [
                ...(($status = \_::$Joint->Shop->NextStatus($v)) ? [Struct::Icon("chevron-left", "desc = " . Script::Prompt($row["Description"] . "\nPut your description", __("I '$status' this item")) . "; if(desc) sendPatch(null, {Id:$id, Status:\"$status\", Description:desc})", ["class" => "be fore green", "ToolTip" => $status])] : []),
                ...(($status = \_::$Joint->Shop->PreviousStatus($v)) ? [Struct::Icon("chevron-right", "desc = " . Script::Prompt($row["Description"] . "\nPut your description", __("It is not '" . $row["Status"] . "' yet")) . "; if(desc) sendPatch(null, {Id:$id, Status:\"$status\", Description:desc})", ["class" => "be fore yellow", "ToolTip" => $status])] : []),
                ...(($v === \_::$Joint->Shop->ReturnedStatus) ? [Struct::Icon("undo", "desc = " . Script::Prompt($row["Description"] . "\nWhy do you want to cancel returning?", __("This merchandise is used before!")) . "; if(desc) sendPatch(null, {Id:$id, Status:\"".($d?\_::$Joint->Shop->DigitalInitialStatus:\_::$Joint->Shop->PhysicalInitialStatus)."\", Description:desc})", ["class" => "be fore green", "ToolTip" => $status])] : []),
                ...(($si<$sfi && $v !== \_::$Joint->Shop->ReturnedStatus) ? [Struct::Icon("check", "desc = " . Script::Prompt($row["Description"] . "\nWhy do you want to finish the process?") . "; if(desc) sendPatch(null, {Id:$id, Status:\"".\_::$Joint->Shop->FinishedStatus."\", Description:desc})", ["class" => "be fore blue", "ToolTip" => "To finish the process"])] : []),
                ...($si>=$sfi || $v === \_::$Joint->Shop->ReturnedStatus ? [] : [Struct::Icon("close", "desc = " . Script::Prompt("Why do you want to return this item?") . "; if(desc) sendPatch(null, {Id:$id, Status:\"$v:Returning\", Description:desc})", ["class" => "be fore red", "ToolTip" => "Return 'merchandise'"])])
            ];
        }
    };
    $users = table("User")->SelectPairs("Id", "Name");
    $sc = 256 / (count(\_::$Joint->Shop->ResponsesStatuses) + 4);
    $module->CellsValues = [
        "Item" => function ($v, $k, $r) {
            return Struct::Link($v, \_::$Joint->Shop->ItemRootUrlPath . $r["ItemPath"], ["target" => "blank"]);
        },
        "Status" => function ($v) use ($sc) {
            $vi = \_::$Joint->Shop->StatusToIInt($v = $v?:\_::$Joint->Shop->UncheckedStatus);
            return Struct::Span(\_::$Joint->Shop->ResponsesStatuses[$v] ?? "Undefined", ["class" => "response-status " . ($vi > 0 ? "success" : ($vi < 0 ? "error" : "")), "style" => "background-color:rgba(" . (128 - $vi * $sc) . ", " . (128 + $vi * $sc) . ", 0)"]);
        },
        "Address" => function ($v, $k, $r) {
            return Struct::Icon($r["Digital"] ? "envelope" : "truck") . " " . $v;
        },
        "User" => function ($v, $k, $r) {
            return Struct::Link($v, \_::$Address->UserRootUrlPath . $r["UserPath"], ["target" => "blank"]);
        },
        "Count" => function ($v, $k, $r) {
            return $v . ($v ? $r["Unit"] ?? \_::$Joint->Shop->ItemsUnit : "");
        },
        "Amount" => function ($v, $k, $r) {
            return \_::$Joint->Finance->AmountStruct($v);
        }
    ];
    $superAccess = \_::$User->HasAccess(\_::$User->SuperAccess);
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
            $std->Value = table("Merchandise")->As("M")
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
    }") . $module->ToString();
};

(new Router())
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "truck",
            "Title" => "Requests Management"
        ]);
    })->Patch(function () {
        auth(\_::$Joint->Shop->AmbassadorsAccess);
        $id = receivePatch("Id");
        $status = receivePatch("Status");
        $desc = receivePatch("Description");
        if ($id) {
            library("MetaDataTable");
            $MDT = new MetaDataTable(null, "Response");
            $MDT->AddProcedure($id, $md, $status, $desc);
            if (
                $MDT->Set($id, [
                    "Status" => $status,
                    "UpdateTime" => Convert::ToDateTimeString(),
                    "Description" => $desc,
                    "MetaData" => $md
                ])
            )
                return deliverRedirect(Struct::Success("The status switched to the `$status`!"));
            else
                return deliverError("Could not change the status!");
        } else
            return deliverError("Something went wrong!");
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();