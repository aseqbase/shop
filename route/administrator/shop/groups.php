<?php
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
auth(\_::$Joint->Shop->SellingAccess);
$data = $data??[];
$routeHandler = function ($data) {
    module("Table");
    $module = new Table("Request");
    $module->SelectQuery = table("Request")->As("R")
        ->Join(table("User")->As("U"), "R.UserId=U.Id")
        ->Join(table("Merchandise")->As("M"), "R.MerchandiseId=M.Id")
        ->Join(table("Content")->As("C"), "M.ContentId=C.Id")
        ->OrderBy("R.UserId ASC, R.UpdateTime DESC")
        ->SelectQuery("R.*, COALESCE(M.Title, C.Title) AS 'Item', R.MerchandiseId AS 'ItemPath',
        U.Name AS 'User', U.Signature AS 'UserPath',
        R.Count AS 'Count', M.Unit AS 'Unit',
        R.Amount AS 'Amount'", "R.Group IS NOT NULL AND R.Group NOT IN ('','0')");
    $module->KeyColumns = ["Item", "User"];
    $module->IncludeColumns = ["User", "Item", "Count", "Amount", "Group", 'Access', 'UpdateTime'];
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->UpdateAccess = \_::$Joint->Shop->SellingAccess;
    $users = table("User")->SelectPairs("Id", "Name");
    $module->CellsValues = [
        "Item" => function ($v, $k, $r) {
            return \MiMFa\Library\Struct::Link($v, "/item/" . $r["ItemPath"], ["target" => "blank"]);
        },
        "User" => function ($v, $k, $r) {
            return \MiMFa\Library\Struct::Link($v, \_::$Address->UserRootUrlPath . $r["UserPath"], ["target" => "blank"]);
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
        "UserId" => !$superAccess ? "disabled" : function ($t, $v) use ($users) {
            $std = new stdClass();
            $std->Title = "User";
            $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "select" : "hidden";
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
        "Group" => "TINYTEXT",
        "Count" => !$superAccess ? "disabled" : "float",
        "Amount" => !$superAccess ? "disabled" : "float",
        "Contact" => "TINYTEXT",
        "Address" => "text",
        "Subject" => "varchar",
        "Description" => "mediumtext",
        "Priority" => "int",
        "Attach" => "json",
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
    return $module->ToString();
};

(new Router())
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "heart",
            "Title" => "Wishes Management"
        ]);
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();