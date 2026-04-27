<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
auth(\_::$User->UserAccess);
$data = $data ?? [];
$routeHandler = function ($data) {
    module("Table");
    $module = new Table(table("Invoice")->OrderBy("UpdateTime", false));
    $module->SelectCondition = "UserId=" . \_::$User->Id;
    $module->KeyColumns = ["Title"];
    $module->IncludeColumns = ["Code" => "Name", "Status", "Title", "Amount", "Currency", "Description", "UpdateTime", "MetaData"];
    $module->ExcludeColumns = ["Currency", "MetaData"];
    $module->AllowDataTranslation = false;
    $module->AllowServerSide = true;
    $module->ViewAccess = false;
    $module->Updatable = false;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->PrependControls = fn($v, $row) => [
        Struct::Icon("eye", \_::$Joint->Finance->InvoiceUrlPath . "?id={$v}", ["tooltip" => "To see the 'invoice'"]),
    ];
    $statuses = ["Pending" => "yellow", "Paid" => "green", "Cancelled" => "red", "Failed" => "red"];
    $module->CellsValues = [
        "Code" => function ($v, $k, $r) use ($statuses) {
            return Struct::Link("\${{$v}}", \_::$Joint->Finance->InvoiceUrlPath . "?id=" . ($v ?: $r["Id"]), ["target" => "blank", "class" => "be fore " . ($statuses[$r["Status"] ?: "Created"] ?? "gray")]);
        },
        "Amount" => fn($v, $k, $r) => \_::$Joint->Finance->AmountStruct($v, __($r["Currency"] ?? \_::$Joint->Finance->ShownCurrency), ["class" => "be fore " . ($statuses[$r["Status"] ?: "Created"] ?? "gray")]),
        "Status" => function ($v, $k, $r) use ($statuses) {
            return Struct::Span($v ?: "Created", \_::$Joint->Finance->InvoiceUrlPath . "?id=" . ($r["Code"] ?: $r["Id"]), ["target" => "blank", "class" => "be fore " . ($statuses[$r["Status"] ?: "Created"] ?? "gray")]);
        },
        "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v),
        "UpdateTime" => fn($v) => Convert::ToShownDateTimeString($v)
    ];
    if ($metadata = Convert::FromJson(\_::$Joint->Shop->RequestsMetaData))
        pod($module, $metadata);
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->Get(function () use ($routeHandler, $data) {
        return page("page", [
            "Image" => get($data, "Image") ?: \_::$Joint->Shop->RequestsImage,
            "Title" => get($data, "Title") ?: \_::$Joint->Shop->RequestsTitle,
            "Description" => get($data, "Description") ?: \_::$Joint->Shop->RequestsDescription,
            "Content" => fn() => \_::$Joint->Shop->RequestsContent . $routeHandler($data)
        ]);
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();