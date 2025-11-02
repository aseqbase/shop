<?php
use MiMFa\Library\Html;
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
inspect(\_::$User->AdminAccess);
module("Table");
$module = new Table("Content");
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
(new Router())
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/merchandises",
            "Image" => "box",
            "Title" => "Merchandises Management"
        ]);
    })
    ->Put(function () {// Create new Item
        $received = receivePut();
        if ($received["Id"])
            try {
                if(table("Merchandise")->Insert([
                    "ContentId" => $received["Id"],
                    "SupplierId" => $received["AuthorId"],
                    "AuthorId" => \_::$User->Id,
                    "Count" => 1
                ])) return deliverBreaker(Html::Success("Your item sat as a Merchandise successfully!"));
                else return error("A problem is occurred in the process!");
            } catch (Exception $ex) {
                return error($ex);
            }
    })
    ->Patch(function () {// Update Item
        $received = receivePatch();
        if ($MerchandiseId = $received["MerchandiseId"])
            try {
                table("Merchandise")->Update("`Id`=:Id", [
                    ":Id" => $MerchandiseId,
                    ...(isset($received["Count"]) ? ["Count" => $received["Count"]] : []),
                    ...(isset($received["Price"]) ? ["Price" => $received["Price"]] : []),
                    "EditorId" => \_::$User->Id,
                    "UpdateTime" => Convert::ToDateTimeString()
                ]);
                return success(Html::Icon("check"));
            } catch (Exception $ex) {
                return error($ex);
            }
        return error(Html::Icon("close"));
    })
    ->Delete(function () {// Delete Item
        $received = receiveDelete();
        if ($MerchandiseId = $received["MerchandiseId"])
            try {
                table("Merchandise")->Delete("`Id`=:Id", [
                    ":Id" => $MerchandiseId
                ]);
                return success(Html::Icon("check"));
            } catch (Exception $ex) {
                return error($ex);
            }
        return error(Html::Icon("close"));
    })
    ->if(getReceived("ContentId"))->Set($module->ExclusiveMethod)->Route(function () use ($module) {
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
            "Type" => "enum",
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
        return $module->ToString();
    })
    ->else()->Default("admin/content/contents")
    ->Handle();
?>