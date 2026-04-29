<?php
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
auth(\_::$Joint->Shop->SellingAccess);
$data = $data ?? [];
$routeHandler = function ($data) {
    module("Table");
    $module = new Table("Shop_Discount");
    $module->SelectQuery = table("Shop_Discount")->As("D")
        ->Join(table("User")->As("U"), "D.UserId=U.Id")
        ->Join(table("Shop_Merchandise")->As("M"), "D.MerchandiseId=M.Id")
        ->Join(table("Content")->As("C"), "M.ContentId=C.Id")
        ->OrderBy("D.Number ASC, D.StartTime DESC, D.EndTime DESC")
        ->SelectQuery("D.Id AS Id, D.Name AS 'Name', COALESCE(M.Title, C.Title) AS 'Item', D.MerchandiseId AS 'ItemPath',
        U.Name AS 'User', U.Signature AS 'UserPath', D.Number AS 'Used',
        D.Count AS 'Count', D.Value AS 'Value', D.Access, D.StartTime, D.EndTime");
    $module->KeyColumns = ["Item", "User"];
    $module->IncludeColumns = ["Name", "User", "Item", "Count", "Used", 'Access', 'StartTime', 'EndTime'];
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->UpdateAccess = \_::$Joint->Shop->SellingAccess;
    $users = [0 => "Public"];
    foreach (table("User")->SelectPairs("Id", "Signature") as $k => $v)
        $users[$k] = $v;
    $module->CellsValues = [
        "Item" => function ($v, $k, $r) {
            return $v?\MiMFa\Library\Struct::Link($v, \_::$Joint->Shop->ItemRootUrlPath . $r["ItemPath"], ["target" => "blank"]):"";
        },
        "User" => function ($v, $k, $r) {
            return $v?\MiMFa\Library\Struct::Link($v, \_::$Address->UserRootUrlPath . $r["UserPath"], ["target" => "blank"]):"";
        }
    ];
    $superAccess = \_::$User->HasAccess(\_::$User->SuperAccess);
    $module->CellsTypes = [
        "Id" => $superAccess ? "disabled" : false,
        "Status" => [1 => "Active", -1 => "Deactive"],
        "UserId" => !$superAccess ? "disabled" : function ($t, $v) use ($users) {
            $std = new stdClass();
            $std->Title = "User";
            $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "select" : "hidden";
            $std->Options = $users;
            return $std;
        }
    ];
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "percent",
            "Title" => "Discounts Management"
        ]);
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();