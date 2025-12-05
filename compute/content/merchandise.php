<?php
compute("request/base");
$ctable = pop($data, "Table")??pop($data, "ContentTable");
$ctable = $ctable instanceof MiMFa\Library\DataTable?$ctable:table($ctable ?? "Content");
$rtable = pop($data, "RequestTable");
$rtable = $rtable instanceof MiMFa\Library\DataTable?$rtable:table($rtable ?? "Request");
$mtable = pop($data, "MerchandiseTable");
$mtable = $mtable instanceof MiMFa\Library\DataTable?$mtable:table($mtable ?? "Merchandise");

$condition = pop($data, "Condition");
$filter = pop($data, "Filter")??[];
$columns = pop($filter, "Columns");
return compute("content/get", [
    "Filter"=>[
        "Columns"=>$columns??
            "$ctable->Name.*,
            $rtable->Name.Id AS 'RequestId', $rtable->Name.Count AS 'RequestCount',
            $mtable->Name.Id AS 'MerchandiseId', $mtable->Name.SupplierId AS 'MerchandiseSupplierId',
            $mtable->Name.Price AS 'MerchandisePrice', $mtable->Name.PriceUnit AS 'MerchandisePriceUnit',
            $mtable->Name.Count AS 'MerchandiseCount', $mtable->Name.CountUnit AS 'MerchandiseCountUnit',
            $mtable->Name.Limit AS 'MerchandiseLimit', $mtable->Name.Digital AS 'MerchandiseDigital',
            $mtable->Name.UpdateTime AS 'MerchandiseUpdateTime', $mtable->Name.CreateTime AS 'MerchandiseCreateTime',
            $mtable->Name.Discount AS 'MerchandiseDiscount', $mtable->Name.MetaData AS 'MerchandiseMetaData'",
        ...$filter
    ],
    "Condition"=>"
    LEFT OUTER JOIN $mtable->Name ON $mtable->Name.ContentId=$ctable->Name.Id
    LEFT OUTER JOIN $rtable->Name ON $mtable->Name.Id=$rtable->Name.MerchandiseId AND $rtable->Name.`Request`=TRUE AND $rtable->Name.`Count`>0 AND ".RequestConditionQuery(tableName:$rtable->Name)."
    ".\_::$Back->DataBase->ConditionNormalization([
        ...($condition?[$condition]:[]),
        //"$mtable->Name.Count > 0",
        authCondition(tableName:$mtable->Name)
    ]),
    "Table"=>$ctable,
    ...$data
]);
?>