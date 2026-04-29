<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
auth(\_::$Joint->Shop->SellingAccess);
module("Table");
$module = new Table("Shop_Merchandise");
$module->AllowServerSide = true;
$module->Updatable = true;
$module->RemoveIcon = "close";
$module->ViewAccess = false;
$module->UpdateAccess = \_::$Joint->Shop->SellingAccess;
$data = $data ?? [];
(new Router())
    ->Get(function () use ($module) {
        (\_::$Front->AdminView)(function ($data) use ($module) {
            module("PrePage");

            $contentTable = table("Content")->Name;
            $itemTable = $module->DataTable->Name;
            $userTable = \_::$User->DataTable->Name;
            $module->SelectQuery = "
                SELECT M_T.Id, M_T.Image AS 'MerchandiseImage', M_T.Title AS 'MerchandiseTitle',
                M_T.SupplierId, M_T.Count, M_T.Unit, M_T.Amount, M_T.Currency, M_T.Discount, M_T.Total, M_T.Volume, M_T.Access, M_T.Status, M_T.UpdateTime,
                C_T.Id AS ContentId, C_T.AuthorId AS 'AuthorId', C_T.Type AS 'Type', C_T.Image AS 'Image', C_T.Title AS 'Item', C_T.CategoryIds AS 'Category',
                U_T.Name AS 'Supplier'
                FROM $contentTable AS C_T
                LEFT OUTER JOIN $itemTable AS M_T ON M_T.ContentId=C_T.Id
                LEFT OUTER JOIN $userTable AS U_T ON M_T.SupplierId=U_T.Id
                WHERE C_T.Type='Merchandise'
                ORDER BY C_T.`Priority` DESC, C_T.`UpdateTime` DESC, M_T.`CreateTime` DESC
            ";
            $module->KeyColumns = ["Image", "Item"];
            $module->IncludeColumns = ['Image', 'Item', 'Category', 'Supplier', 'Count', 'Amount', 'Discount', 'Total', 'Volume', 'Status', 'UpdateTime'];

            $onsubmit = null;
            $module->PrependControls = function ($id, $r) use ($module, &$onsubmit) {
                if ($id = $r["Id"]) {
                    $selector = ".table tr:has(* hidden[value='$id'])";
                    $onsubmit = "sendPatch(null, {
                            MerchandiseId:$id,
                            Count:_(\"$selector [name='Count']\").val(),
                            Amount:_(\"$selector [name='Amount']\").val()?_(\"$selector [name='Amount']\").val():null,
                            Discount:_(\"$selector [name='Discount']\").val()
                        }
                        , \"$selector\", (data, err)=>{if(data) _(\"$selector [name='Submit']\").removeClass('red').addClass('green'); else _(\"$selector\").html(err);}
                        , \"$selector\", (data, err)=>{_(\"$selector [name='Submit']\").removeClass('green').addClass('red'); _(\"$selector [name='Submit']\").html(data??err);}
                    );";
                    return [
                        Struct::Icon("check", $onsubmit, ["name" => "Submit", "Id" => "{$module->MainClass}_submit_$id", "class" => "be"]),
                        Struct::Icon("ellipsis-h", "{$module->Modal->MainClass}_Modify(`{$r['ContentId']}`, '/administrator/content/contents');", ["name" => "Submit"]),
                    ];
                } else
                    return [
                        Struct::Icon("tag", "sendPut(null, {Id:{$r['ContentId']}, AuthorId:" . $r["AuthorId"] . "}, 'table:nth-child(1)');", ["ToolTip" => "Add this '{$r["Type"]}' to shop"]),
                        Struct::Icon("ellipsis-h", "{$module->Modal->MainClass}_Modify(`{$r['ContentId']}`, '/administrator/content/contents');", ["name" => "Submit"]),
                    ];
            };

            $up = Script::Convert(\_::$Joint->Finance->ShownUnknownPrice);
            $module->CellsValues = [
                "Image" => function ($v, $k, $r) {
                    return $r["MerchandiseImage"] ?: $v;
                },
                "Item" => function ($v, $k, $r) use ($module) {
                    return Struct::Link($r["MerchandiseTitle"] ?: $v, \_::$Joint->Shop->ItemRootUrlPath . $r[$module->KeyColumn], ["target" => "blank"]);
                },
                "Supplier" => function ($v, $k, $r) {
                    return $r["SupplierId"] ? Struct::Link($v, \_::$Address->UserRootUrlPath . $r["SupplierId"], ["target" => "blank"]) : $v;
                },
                "Category" => function ($v, $k, $r) {
                    $val = trim(\_::$Back->Query->GetCategoryRoute(first(Convert::FromJson($v))) ?? "", "/\\");
                    if (isValid($val))
                        return Struct::Link($val, \_::$Address->CategoryRootUrlPath . $val, ["target" => "blank"]);
                    return $v;
                },
                "Count" => function ($v, $k, $r) use (&$onsubmit) {
                    if ($r["Id"])
                        return Struct::FloatInput("Count", $v, ["min" => 0, "onkeyup" => "if(event.key === 'Enter'){event.preventDefault(); $onsubmit}"]) . ($r["Unit"] ?? \_::$Joint->Shop->ItemsUnit);
                    else
                        return $v . ($v ? $r["Unit"] ?? \_::$Joint->Shop->ItemsUnit : "");
                },
                "Amount" => function ($v, $k, $r) use (&$onsubmit, $up, $module) {
                    if ($r["Id"])
                        return Struct::Box(
                            Struct::NumberInput("Amount", $v, ["min" => 0, "PlaceHolder"=>\_::$Joint->Finance->ShownUnknownPrice, "onkeyup" => "if(event.key === 'Enter'){event.preventDefault(); $onsubmit}"]) .
                            __($r["Currency"] ?: \_::$Joint->Finance->ShownCurrency) .
                            Struct::Icon("question", "
                            this.previousElementSibling.previousElementSibling.value = this.previousElementSibling.value = null;
                            $onsubmit", ["class" => "be small", "Tooltip" => "To make price unknown"]),
                            ["class" => "be flex middle"]
                        );
                    else
                        return $v . __($v ? ($r["Currency"] ?: \_::$Joint->Finance->ShownCurrency) : "");
                },
                "Discount" => function ($v, $k, $r) use (&$onsubmit) {
                    if ($r["Id"])
                        return Struct::FloatInput("Discount", $v, ["min" => 0, "max" => 100, "onkeyup" => "if(event.key === 'Enter'){event.preventDefault(); $onsubmit}"]) . "%";
                    else
                        return $v ? "$v%" : "";
                },
                "Total" => function ($v, $k, $r) {
                    return $v ? $v . ($r["Unit"] ?? \_::$Joint->Shop->ItemsUnit) : "";
                },
                "Volume" => function ($v, $k, $r) {
                    return $v ? Struct::Number(round($v, \_::$Joint->Finance->DecimalPercision)) . __(\_::$Joint->Finance->ShownCurrency) : "";
                }
            ];

            return Struct::Style("
            .{$module->MainClass} tr td input{
                background-color: transparent;
                color: var(--fore-color-input);
                border-radius: var(--radius-3);
            }
            .{$module->MainClass} tr td input:is(:focus,:hover){
                background-color: var(--back-color-input);
                color: var(--fore-color-input);
            }
            .{$module->MainClass} tr td input:not(.numberinput, [name='Amount']){
                max-width: 100px;
            }
            ") . $module->ToString();
        }, [
            "Image" => "box",
            "Title" => "Merchandises Management"
        ]);
    })
    ->Put(function () {// Create new Item
        $received = receivePut();
        if ($received["Id"])
            try {
                if (
                    table("Shop_Merchandise")->Insert([
                        "ContentId" => $received["Id"],
                        "SupplierId" => $received["AuthorId"],
                        "AuthorId" => \_::$User->Id,
                        "Count" => 1
                    ])
                )
                    return deliverRedirect(Struct::Success("Your item sat as a Merchandise successfully!"));
                else
                    return error("A problem is occurred in the process!");
            } catch (Exception $ex) {
                return error($ex);
            }
    })
    ->Patch(function () {// Update Item
        $received = receivePatch();
        if ($MerchandiseId = $received["MerchandiseId"])
            try {
                table("Shop_Merchandise")->Update("`Id`=:Id", [
                    ":Id" => $MerchandiseId,
                    ...(isset($received["Count"]) ? ["Count" => $received["Count"]] : []),
                    "Amount" => $received["Amount"],
                    ...(isset($received["Discount"]) ? ["Discount" => $received["Discount"]] : []),
                    "EditorId" => \_::$User->Id,
                    "UpdateTime" => Convert::ToDateTimeString()
                ]);
                return success(Struct::Icon("check"));
            } catch (Exception $ex) {
                return error($ex);
            }
        return error(Struct::Icon("close"));
    })
    // ->Delete(function () {// Delete Item
    //     $received = receiveDelete();
    //     if ($MerchandiseId = $received["MerchandiseId"])
    //         try {
    //             table("Shop_Merchandise")->Delete("`Id`=:Id", [
    //                 ":Id" => $MerchandiseId
    //             ]);
    //             return redirect(Struct::Success(Struct::Icon("check")));
    //         } catch (Exception $ex) {
    //             return error($ex);
    //         }
    //     return error(Struct::Icon("close"));
    // })
    ->if($module->AddSecret === received($module->SecretRequestKey, null, $module->Method))
    ->Default("administrator/content/contents", ["Type" => "Merchandise"])
    ->else()->Default(function () use ($module) {
        $access = \_::$User->HasAccess(\_::$Joint->Shop->SellingAccess);
        $users = table("User")->SelectPairs("Id", "Name");
        $module->CellsTypes = [
            // "Id" => function ($t, $v, $k, $r) {
            //     $std = new stdClass();
            //     $std->Key = "ContentId";
            //     $std->Type = "hidden";
            //     $std->Value = $r["ContentId"];
            //     return $std;
            // },
            "ContentId" => function ($t, $v) {
                $std = new stdClass();
                $std->Title = "Item";
                $std->Type = "find";
                $std->Options = table("Content")->SelectPairs("Title", "Id");
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
            "Media" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "collection";
                $std->Options = ["Type" => "path"];
                return $std;
            },
            "Digital" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "bool";
                $std->Value = ($v ?? \_::$Joint->Shop->DigitalStore) ? 1 : 0;
                return $std;
            },
            "Count" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "float";
                $std->Options = ["min" => 0, "max" => 999999999999];
                return $std;
            },
            "Amount" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "number";
                $std->Options = ["min" => 0, "max" => 999999999999];
                return $std;
            },
            "Currency" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "select";
                $std->Options = \_::$Joint->Finance->GetAllCurrencyOptions();
                return $std;
            },
            "Limit" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "float";
                $std->Options = ["min" => 0, "max" => 999999999999];
                return $std;
            },
            "Discount" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "float";
                $std->Options = ["min" => 0, "max" => 100];
                return $std;
            },
            "Total" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "float";
                $std->Options = ["min" => 0, "max" => 999999999999];
                return $std;
            },
            "Volume" => function ($t, $v) {
                $std = new stdClass();
                $std->Type = "number";
                $std->Options = ["min" => 0, "max" => 999999999999];
                return $std;
            },
            "Property" => "json",
            "PrivateGenerator" => "text",
            "PrivateTitle" => "text",
            "PrivateMessage" => "content",
            "PrivateAttach" => "json",
            "AuthorId" => function ($t, $v) use ($users) {
                $std = new stdClass();
                $std->Title = "Author";
                $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "select" : "hidden";
                $std->Options = $users;
                if (!isValid($v))
                    $std->Value = \_::$User->Id;
                return $std;
            },
            "EditorId" => function ($t, $v) use ($users) {
                $std = new stdClass();
                $std->Title = "Editor";
                $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "select" : "hidden";
                $std->Options = $users;
                if (!isValid($v))
                    $std->Value = \_::$User->Id;
                return $std;
            },
            "Status" => [1 => "Published", 0 => "Drafted", -1 => "Unpublished"],
            "Access" => function () {
                $std = new stdClass();
                $std->Type = "number";
                $std->Attributes = ["min" => \_::$User->BanAccess, "max" => \_::$User->SuperAccess];
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
            "MetaData" => "json"
        ];
        return $module->Render();
    })
    ->Handle();