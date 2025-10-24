<?php
use MiMFa\Library\Html;
use MiMFa\Library\Script;
use MiMFa\Library\Convert;
use MiMFa\Module\PrePage;
use MiMFa\Module\Table;

inspect(\_::$User->AdminAccess);

module("Table");
$module = new Table("Content");
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;

module("PrePage");

$itemTable = table("Merchandise")->Name;
$contentTable = $module->DataTable->Name;
$userTable = \_::$User->DataTable->Name;
$module->SelectQuery = "
    SELECT C_T.{$module->KeyColumn}, C_T.{$module->KeyColumn} AS 'Update', C_T.AuthorId AS 'AuthorId', M_T.Id AS 'MerchandiseId', M_T.SupplierId, C_T.Type AS 'Type', C_T.Image AS 'Image', C_T.Title AS 'Item', C_T.CategoryIds AS 'Category', U_T.Name AS 'Supplier', M_T.Count, M_T.CountUnit, M_T.Price, M_T.PriceUnit, M_T.Discount, M_T.Total, M_T.Volume, M_T.Access, M_T.Status, M_T.UpdateTime
    FROM $contentTable AS C_T
    LEFT OUTER JOIN $itemTable AS M_T ON M_T.ContentId=C_T.Id
    LEFT OUTER JOIN $userTable AS U_T ON M_T.SupplierId=U_T.Id
    ORDER BY C_T.`Priority` DESC, C_T.`UpdateTime` DESC, M_T.`CreateTime` DESC, M_T.`UpdateTime` DESC
";
$module->KeyColumns = ["Image", "Item"];
$module->IncludeColumns = ['Update', 'Image', 'Item', 'Category', 'Supplier', 'Count', 'Price', 'Total', 'Volume', 'Access', 'UpdateTime'];
$module->Set("Merchandise");
$access = \_::$User->GetAccess(\_::$User->AdminAccess);
$users = table("User")->SelectPairs("Id", "Name");
$module->CellsTypes = [
    "Id" => $access ? "disabled" : false,
    "ContentId" => function ($t, $v) {
        $std = new stdClass();
        $std->Title = "Item";
        $std->Type = "span";
        $std->Value = table("Content")->SelectValue("Title", "`Id`=:Id", [":Id" => $v]) ?? table("Content")->SelectValue("Name", "`Id`=:Id", [":Id" => $v]);
        return $std;
    },
    "SupplierId" => function ($t, $v) use ($access, $users) {
        $std = new stdClass();
        $std->Title = "Supplier";
        $std->Type = $access ? "select" : "hidden";
        $std->Options = $users;
        if (!isValid($v))
            $std->Value = \_::$User->Id;
        return $std;
    },
    "Digital" => function ($t, $v) {
        $std = new stdClass();
        $std->Description = "Is a digital item";
        $std->Type = "bool";
        $std->Value = $v??\_::$Config->DigitalStore;
        return $std;
    },
    "PrivatePath" => "string",
    "PrivateTitle" => "string",
    "PrivateMessage" => "content",
    "PrivateAttach" => "json",
    "AuthorId" => function ($t, $v) use ($users) {
        $std = new stdClass();
        $std->Title = "Author";
        $std->Type = \_::$User->GetAccess(\_::$User->SuperAccess) ? "select" : "hidden";
        $std->Options = $users;
        if (!isValid($v))
            $std->Value = \_::$User->Id;
        return $std;
    },
    "EditorId" => function ($t, $v) use ($users) {
        $std = new stdClass();
        $std->Title = "Editor";
        $std->Type = \_::$User->GetAccess(\_::$User->SuperAccess) ? "select" : "hidden";
        $std->Options = $users;
        if (!isValid($v))
            $std->Value = \_::$User->Id;
        return $std;
    },
    "Status" => [-1 => "Unpublished", 0 => "Drafted", 1 => "Published"],
    "Access" => function () {
        $std = new stdClass();
        $std->Type = "number";
        $std->Attributes = ["min" => \_::$User->BanAccess, "max" => \_::$User->SuperAccess];
        return $std;
    },
    "UpdateTime" => function ($t, $v) {
        $std = new stdClass();
        $std->Type = \_::$User->GetAccess(\_::$User->SuperAccess) ? "calendar" : "hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function ($t, $v) {
        return \_::$User->GetAccess(\_::$User->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
    },
    "MetaData" => "json"
];
style(".{$module->Name} tr td input{
    background-color: var(--back-color-input);
    color: var(--fore-color-input);
    max-width: 100px;
}");
$forLabel = __("for");
$eachLabel = __("each");
$module->CellsValues = [
    "Update" => function ($id, $k, $r) use ($module, $forLabel, $eachLabel) {
        $selector = "'table:nth-child(1)'";
        if ($MerchandiseId = $r["MerchandiseId"]) {
            $fid = "F_$MerchandiseId";
            $onsubmit = "sendPatchRequest(null, {
                    MerchandiseId:$MerchandiseId,
                    Count:document.querySelector('#$fid>[name=Count]').value,
                    Price:document.querySelector('#$fid>[name=Price]').value
                }
                , '#$fid', (data, err)=>document.querySelector('#$fid>[name=Submit]').innerHTML = data??err
                , '#$fid', (data, err)=>document.querySelector('#$fid>[name=Submit]').innerHTML = data??err
            );";
            $onkeydown = "if(event.key === 'Enter'){event.preventDefault(); $onsubmit}";
            return Html::Division(
                Html::FloatInput("Count", $r["Count"], ["min" => 0, "onkeydown" => $onkeydown, "max" => 99999999, "step" => 1]) . ($r["CountUnit"]??\_::$Config->CountUnit)." $forLabel " .
                Html::FloatInput("Price", $r["Price"], ["min" => 0, "onkeydown" => $onkeydown, "max" => 99999999, "step" => $r["Price"] ?? 10 / 10]) . ($r["PriceUnit"]??\_::$Config->PriceUnit)." $eachLabel " .
                Html::Icon("check", $onsubmit, ["name"=>"Submit"]) . " " .
                Html::Icon("ellipsis-h", "sendRequest(" . Script::Convert($module->ExclusiveMethod) . ", 'null',
            {{$module->SecretKey}:".Script::Convert($module->ModifySecret).",{$module->KeyColumn}:$id,MerchandiseId:$MerchandiseId}, '#$fid');") . " " .
            Html::Icon("close", "if(confirm('Are you sure to delete \"{$r["Item"]}\" from the merchandaises?')) sendDeleteRequest(null, {MerchandiseId:$MerchandiseId}, '#$fid');")
            , ["id" => $fid]
            );
        } else
            return Html::Division($r["Type"] . " " . Html::Icon("box", "sendPutRequest(null, {Id:$id, AuthorId:".$r["AuthorId"]."}, $selector);"));
    },
    "Item" => function ($v, $k, $r) {
        return Html::Link($v, "/item/" . $r["Id"], ["target"=>"blank"]);
    },
    "Supplier" => function ($v, $k, $r) {
        return $r["SupplierId"] ? Html::Link($v, \_::$Address->UserRoot . $r["SupplierId"], ["target"=>"blank"]) : $v;
    },
    "Category" => function ($v, $k, $r) {
        $val = trim(\_::$Back->Query->GetCategoryRoute(first(Convert::FromJson($v))) ?? "", "/\\");
        if (isValid($val))
            return Html::Link($val, \_::$Address->CategoryRoot . $val, ["target"=>"blank"]);
        return $v;
    },
    "Count" => function ($v, $k, $r) {
        return $v . ($v?$r["CountUnit"]??\_::$Config->CountUnit:"");
    },
    "Price" => function ($v, $k, $r) {
        return $v . ($v?$r["PriceUnit"]??\_::$Config->PriceUnit:"");
    },
    "Discount" => function ($v, $k, $r) {
        return $v ? "$v%" : "";
    },
    "Total" => function ($v, $k, $r) {
        return $v ? $v . ($r["CountUnit"]??\_::$Config->CountUnit) : "";
    },
    "Volume" => function ($v, $k, $r) {
        return $v ? $v . ($r["PriceUnit"]??\_::$Config->PriceUnit) : "";
    }
];
$module->Render();
?>