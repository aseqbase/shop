<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Module\Table;
module("Table");
$module = new Table("Response");
$module->SelectQuery = table("Response")->As("R")
    ->Join(table("User")->As("U"), "R.UserId=U.Id")
    ->Join(table("Merchandise")->As("M"), "R.MerchandiseId=M.Id")
    ->Join(table("Content")->As("C"), "M.ContentId=C.Id")
    ->OrderBy("R.Collection ASC, R.UserId ASC, R.UpdateTime DESC")
    ->SelectQuery("*, R.Id AS 'Id', R.Status AS 'Status',
        C.Title AS 'Item', C.Id AS 'ItemPath',
        U.Name AS 'User', U.Signature AS 'UserPath',
        M.Digital AS 'Digital',
        R.Address AS 'Address', M.CountUnit AS 'CountUnit',
        R.Count AS 'Count', R.Price AS 'Price'");
$module->KeyColumns = ["Item", "User"];
$module->IncludeColumns = ["User", "Item", "Status", "Address", "Collection", "Count", "Price", "UpdateTime"];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->DeleteAccess = 
$module->AddAccess = 
$module->DuplicateAccess = \_::$Config->SuperAccess;
$users = table("User")->SelectPairs("Id" , "Name");
$module->CellsValues = [
    "Item"=>function($v, $k, $r){
        return Html::Link($v,"/item/".$r["ItemPath"], ["target"=>"blank"]);
    },
    "Status"=>function($v){
        $status = $v;
        switch ($v) {
            case 5: $status = __("Delivered"); break;
            case 4: $status = __("Received"); break;
            case 3: $status = __("Sent"); break;
            case 2: $status = __("Prepared"); break;
            case 1: $status = __("Accepted"); break;
            case 0: $status = __("Unchecked"); break;
            case -1: $status = __("Unaccepted"); break;
            case -2: $status = __("Unavailable"); break;
            case -3: $status = __("Defected"); break;
            case -4: $status = __("Canceled"); break;
            case -5: $status = __("Rejected"); break;
            default: $status = __("Undefined"); break;
        }
        return Html::Span($status,["class"=>$v>0?"success":($v<0?"error":""), "style"=>"color:rgba(".(128-$v*127/5).", ".(128+$v*127/5).", 0)"]);
    },
    "Address"=>function($v, $k, $r){
        return Html::Icon($r["Digital"]?"envelope":"truck")." ".$v;
    },
    "User"=>function($v, $k, $r){
        return Html::Link($v,\_::$Address->UserRoute.$r["UserPath"], ["target"=>"blank"]);
    },
    "Count" => function ($v, $k, $r) {
        return $v . ($v?$r["CountUnit"]??\_::$Config->CountUnit:"");
    },
    "Price" => function ($v, $k, $r) {
        return $v . \_::$Config->PriceUnit;
    }
];
$superAccess = auth(\_::$Config->SuperAccess);
$module->CellsTypes = [
    "Id" => $superAccess?"disabled":false,
    "UserId" =>!$superAccess?"disabled":function($t, $v) use($users, $superAccess){
        $std = new stdClass();
        $std->Title = "User";
        $std->Type = $superAccess?"select":"hidden";
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
    "Status"=>function($t, $v){
        $std = new stdClass();
        $std->Title = "Status";
        $std->Type = "select";
        $std->Options = [
            "-5"=> __("Rejected"),
            "-4"=> __("Canceled"),
            "-3"=> __("Defected"),
            "-2"=> __("Unavailable"),
            "-1"=> __("Unaccepted"),
            "0"=> __("Unchecked"),
            "1"=> __("Accepted"),
            "2"=> __("Prepared"),
            "3"=> __("Sent"),
            "4"=> __("Received"),
            "5"=> __("Delivered")
        ];
        $std->Value = $v;
        return $std;
    },
    "Count"=>!$superAccess?"disabled":"float",
    "Price"=>!$superAccess?"disabled":"float",
    "Contact"=>"TINYTEXT",
    "Address"=>"text",
    "AuthorId" =>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Responsible";
        $std->Type = "select";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$User->Id;
        return $std;
    },
    "Subject"=>"varchar",
    "Description"=>"mediumtext",
    "Content"=>!$superAccess?"disabled":"Content",
    "Priority"=>"int",
    "Attach" =>"json",
    "EditorId" =>function($t, $v) use($users, $superAccess){
        $std = new stdClass();
        $std->Title = "Editor";
        $std->Type = $superAccess?"select":"disabled";
        $std->Options = $users;
        $std->Value = \_::$User->Id;
        return $std;
    },
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = auth(\_::$Config->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return auth(\_::$Config->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "json";
        if(\_::$Config->AllowTranslate && !$r["Title"] && !$r["Content"]) $std->Value = "{\"lang\":\"".\_::$Back->Translate->Language."\"}";
        return $std;
    }
];
swap($module, $data);
$module->Render();
?>