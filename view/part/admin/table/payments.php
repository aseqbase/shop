<?php
inspect(\_::$Config->AdminAccess);

use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Library\Script;
use MiMFa\Module\Table;

module("Table");
$module = new Table(table("Payment"));
$module->SelectCondition = "ORDER BY `CreateTime` DESC";
$module->KeyColumns = ['Value', "Relation"];
$module->IncludeColumns = ['Relation', 'Verify', 'Value', 'Unit', 'Source', 'Destination', 'Transaction', 'CreateTime'];
$module->ExcludeColumns = ["Unit", 'Verify'];
$module->AllowServerSide = true;
$module->Id = "_table_".getId(true);
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->DeleteAccess = 
$module->AddAccess = 
$module->DuplicateAccess =
$module->ModifyAccess =
$module->DeleteAccess = \_::$Config->SuperAccess;
\Res::Style("
    .{$module->Name} tr:has(.verified){
        color: var(--color-2);
    }
");
$module->CellsValues = [
    'Relation' => fn($v, $k, $r) => $r['Verify'] ? Html::Span($v, ["class" => "be verified"]) : Html::Span($v . " " . Html::Icon("check", "sendPut(null,{Id:" . Script::Convert($r["Id"]) . "}, '#{$module->Id}')")),
    'Value' => function ($v, $k, $r) {
        return (\_::$Config->StandardPrice)($v, $r['Unit']) . \_::$Config->PriceUnit;
    },
    'Source',
    'Destination',
    'Transaction',
    'CreateTime'
];
$module->CellsTypes = [
    "Id" => auth(\_::$Config->SuperAccess) ? "disabled" : false,
    'Relation' => "string",
    'Verify' => "check",
    'Source' => "string",
    'SourceEmail' => "email",
    'SourceContent' => "string",
    'SourcePath' => "string",
    'Value' => "string",
    'Unit' => "string",
    'Network' => "string",
    'Transaction' => "string",
    'Identifier' => "string",
    'Destination' => "string",
    'DestinationEmail' => "email",
    'DestinationContent' => "string",
    'DestinationPath' => "string",
    'Others' => "string",
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
swap($module, $data);
$module->Render();
?>