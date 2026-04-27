<?php
$ctable = pop($data, "ContentTable");
$ctable = $ctable instanceof MiMFa\Library\DataTable?$ctable:table($ctable ?? "Content");
$rtable = pop($data, "RequestTable");
$rtable = $rtable instanceof MiMFa\Library\DataTable?$rtable:table($rtable ?? "Request");
$mtable = pop($data, "Table")??pop($data, "MerchandiseTable");
$mtable = $mtable instanceof MiMFa\Library\DataTable?$mtable:table($mtable ?? "Merchandise");

$condition = pop($data, "Condition");
$filter = pop($data, "Filter")??[];
$columns = pop($filter, "Columns");
return compute("content/get", [
    "Filter"=>[
        "Columns"=>$columns??
            "$ctable->Name.*,
            $rtable->Name.Id AS 'RequestId', LEAST($rtable->Name.Count, $mtable->Name.Count) AS 'RequestCount',
            $rtable->Name.Group AS 'RequestGroup',
            $mtable->Name.Id AS 'MerchandiseId', $mtable->Name.Name AS 'MerchandiseName',
            $mtable->Name.Title AS 'MerchandiseTitle', $mtable->Name.Description AS 'MerchandiseDescription',
            $mtable->Name.Image AS 'MerchandiseImage', $mtable->Name.SupplierId AS 'MerchandiseSupplierId',
            $mtable->Name.Amount AS 'MerchandisePrice', $mtable->Name.Currency AS 'MerchandiseCurrency',
            $mtable->Name.Count AS 'MerchandiseCount', $mtable->Name.Unit AS 'MerchandiseUnit',
            COALESCE($mtable->Name.Limit, $mtable->Name.Count) AS 'MerchandiseLimit', $mtable->Name.Digital AS 'MerchandiseDigital',
            $mtable->Name.UpdateTime AS 'MerchandiseUpdateTime', $mtable->Name.CreateTime AS 'MerchandiseCreateTime',
            $mtable->Name.Discount AS 'MerchandiseDiscount', $mtable->Name.MetaData AS 'MerchandiseMetaData'",
        ...$filter
    ],
    "Condition"=>"
    LEFT OUTER JOIN $ctable->Name ON $mtable->Name.ContentId=$ctable->Name.Id
    LEFT OUTER JOIN $rtable->Name ON $mtable->Name.Id=$rtable->Name.MerchandiseId AND ".\_::$Joint->Shop->CartCondition(tableName:$rtable->Name)."
    ".\_::$Back->DataBase->ConditionNormalization([
        ...($condition?[$condition]:[]),
        //"$mtable->Name.Count > 0",
        authCondition(tableName:$mtable->Name)
    ]),
    "Table"=>$mtable,
    ...($data??[])
]);