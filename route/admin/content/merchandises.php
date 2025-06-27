<?php
use MiMFa\Library\Html;
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
inspect(\_::$Config->AdminAccess);
module("Table");
$module = new Table("Content");
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
(new MiMFa\Library\Router())
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/merchandises",
            "Image" => "box",
            "Title" => "Merchandises Management"
        ]);
    })
    ->Put(function () {// Create new Item
        $received = \Req::ReceivePut();
        if ($received["Id"])
            try {
                if(table("Merchandise")->Insert([
                    "ContentId" => $received["Id"],
                    "SupplierId" => $received["AuthorId"],
                    "AuthorId" => \_::$Back->User->Id,
                    "Count" => 1
                ])) return \Res::Flip(Html::Success("Your item sat as a Merchandise successfully!"));
                else return \Res::Error("A problem is occured in the process!");
            } catch (Exception $ex) {
                return \Res::Error($ex);
            }
    })
    ->Patch(function () {// Update Item
        $received = \Req::ReceivePatch();
        if ($MerchandiseId = $received["MerchandiseId"])
            try {
                table("Merchandise")->Update("`Id`=:Id", [
                    ":Id" => $MerchandiseId,
                    ...(isset($received["Count"]) ? ["Count" => $received["Count"]] : []),
                    ...(isset($received["Price"]) ? ["Price" => $received["Price"]] : []),
                    "EditorId" => \_::$Back->User->Id,
                    "UpdateTime" => Convert::ToDateTimeString()
                ]);
                return \Res::Success(Html::Icon("check"));
            } catch (Exception $ex) {
                return \Res::Error($ex);
            }
        return \Res::Error(Html::Icon("close"));
    })
    ->Delete(function () {// Delete Item
        $received = \Req::ReceiveDelete();
        if ($MerchandiseId = $received["MerchandiseId"])
            try {
                table("Merchandise")->Delete("`Id`=:Id", [
                    ":Id" => $MerchandiseId
                ]);
                return \Res::Success(Html::Icon("check"));
            } catch (Exception $ex) {
                return \Res::Error($ex);
            }
        return \Res::Error(Html::Icon("close"));
    })
    ->if(\Req::Receive("ContentId"))->On($module->ExclusiveMethod, function () use ($module) {
        $module->Set("Merchandise");
        $access = auth(\_::$Config->AdminAccess);
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
                    $std->Value = \_::$Back->User->Id;
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
                $std->Type = auth(\_::$Config->SuperAccess) ? "select" : "hidden";
                $std->Options = $users;
                if (!isValid($v))
                    $std->Value = \_::$Back->User->Id;
                return $std;
            },
            "EditorId" => function ($t, $v) use ($users) {
                $std = new stdClass();
                $std->Title = "Editor";
                $std->Type = auth(\_::$Config->SuperAccess) ? "select" : "hidden";
                $std->Options = $users;
                if (!isValid($v))
                    $std->Value = \_::$Back->User->Id;
                return $std;
            },
            "Status" => [-1 => "Unpublished", 0 => "Drafted", 1 => "Published"],
            "Access" => function () {
                $std = new stdClass();
                $std->Type = "number";
                $std->Attributes = ["min" => \_::$Config->BanAccess, "max" => \_::$Config->SuperAccess];
                return $std;
            },
            "UpdateTime" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = auth(\_::$Config->SuperAccess) ? "calendar" : "hidden";
                $std->Value = Convert::ToDateTimeString();
                return $std;
            },
            "CreateTime" => function ($t, $v) {
                return auth(\_::$Config->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
            },
            "MetaData" => "json"
        ];
        return $module->ToString();
    })
    ->else()->Default("admin/content/contents")
    ->Handle();
?>