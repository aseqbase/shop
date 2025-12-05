<?php
auth(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table("Request");
$module->SelectQuery = table("Request")->As("R")
    ->Join(table("User")->As("U"), "R.UserId=U.Id")
    ->Join(table("Merchandise")->As("M"), "R.MerchandiseId=M.Id")
    ->Join(table("Content")->As("C"), "M.ContentId=C.Id")
    ->OrderBy("R.Collection ASC, R.UserId ASC, R.UpdateTime DESC")
    ->SelectQuery("*, R.Id AS Id, C.Title AS 'Item', C.Id AS 'ItemPath',
        U.Name AS 'User', U.Signature AS 'UserPath',
        R.Count AS 'Count', M.CountUnit AS 'CountUnit',
        R.Price AS 'Price'");
$module->KeyColumns = ["Item" , "User" ];
$module->IncludeColumns = ["User" , "Item" , "Count" , "Price" , "Collection", 'Access' , 'UpdateTime' ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$users = table("User")->SelectPairs("Id" , "Name" );
$module->CellsValues = [
    "Item"=>function($v, $k, $r){
        return \MiMFa\Library\Struct::Link($v,"/item/".$r["ItemPath"], ["target"=>"blank"]);
    },
    "User"=>function($v, $k, $r){
        return \MiMFa\Library\Struct::Link($v,\_::$Router->UserRoot.$r["UserPath"], ["target"=>"blank"]);
    },
    "Count" => function ($v, $k, $r) {
        return $v . ($v?$r["CountUnit"]??\_::$Config->CountUnit:"");
    },
    "Price" => function ($v, $k, $r) {
        return $v . \_::$Config->PriceUnit;
    }
];
$superAccess = \_::$User->HasAccess(\_::$User->SuperAccess);
$module->CellsTypes = [
    "Id" => $superAccess?"disabled":false,
    "UserId" =>!$superAccess?"disabled":function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "User";
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$User->Id;
        return $std;
    },
    "MerchandiseId"=>!$superAccess?"disabled":function($t, $v) {
        $std = new stdClass();
        $std->Title = "Merchandise";
        $std->Type = "disabled";
        $std->Value = table("Merchandise")->As("M")
            ->Join(table("Content")->As("C"))
            ->SelectValue("C.Title", "M.Id=:Id", [":Id"=>$v]);
        return $std;
    },
    "UserCode"=>!$superAccess?"disabled":"TINYTEXT",
    "Collection"=>!$superAccess?"disabled":"TINYTEXT",
    "Like"=>"BOOLEAN",
    "Request"=>"BOOLEAN",
    "Count"=>!$superAccess?"disabled":"float",
    "Price"=>!$superAccess?"disabled":"float",
    "Contact"=>"TINYTEXT",
    "Address"=>"text",
    "Subject"=>"varchar",
    "Description"=>"mediumtext",
    "Priority"=>"int",
    "Attach" =>"json",
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return \_::$User->HasAccess(\_::$User->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "json";
        if(\_::$Front->AllowTranslate && !$r["Title"] && !$r["Content"]) $std->Value = "{\"lang\":\"".\_::$Front->Translate->Language."\"}";
        return $std;
    }
];
pod($module, $data);
$module->Render();
?>