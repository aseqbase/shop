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
            case 5: $status = __("Delivered", styling:false); break;
            case 4: $status = __("Received", styling:false); break;
            case 3: $status = __("Sent", styling:false); break;
            case 2: $status = __("Prepared", styling:false); break;
            case 1: $status = __("Accepted", styling:false); break;
            case 0: $status = __("Unchecked", styling:false); break;
            case -1: $status = __("Unaccepted", styling:false); break;
            case -2: $status = __("Unavailable", styling:false); break;
            case -3: $status = __("Defected", styling:false); break;
            case -4: $status = __("Canceled", styling:false); break;
            case -5: $status = __("Rejected", styling:false); break;
            default: $status = __("Undefined", styling:false); break;
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
        if(!isValid($v)) $std->Value = \_::$Back->User->Id;
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
            "-5"=> __("Rejected", styling:false),
            "-4"=> __("Canceled", styling:false),
            "-3"=> __("Defected", styling:false),
            "-2"=> __("Unavailable", styling:false),
            "-1"=> __("Unaccepted", styling:false),
            "0"=> __("Unchecked", styling:false),
            "1"=> __("Accepted", styling:false),
            "2"=> __("Prepared", styling:false),
            "3"=> __("Sent", styling:false),
            "4"=> __("Received", styling:false),
            "5"=> __("Delivered", styling:false)
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
        if(!isValid($v)) $std->Value = \_::$Back->User->Id;
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
        $std->Value = \_::$Back->User->Id;
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