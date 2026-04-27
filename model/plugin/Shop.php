<?php
namespace MiMFa\Plugin;

use MiMFa\Library\Convert;
use MiMFa\Library\Struct;

class Shop extends \MiMFa\Library\Revise
{
    /**
     * @field text
     */
    public $Title = "Shop";
    /**
     * @field texts
     */
    public $Description = null;
    /**
     * @field path
     */
    public $Image = "shop";
    /**
     * @field text
     */
    public $AdminTitle = "Vendor";
    /**
     * @field texts
     */
    public $AdminDescription = null;
    /**
     * @field path
     */
    public $AdminImage = "industry";
    /**
     * Supported sites for the physical merchandises
     * @field path
     */
    public $PhysicalSitesPath = "/model/data/sites.json";
    /**
     * Supported sites for the digital merchandises
     * @field path
     */
    public $DigitalSitesPath = null;

    public $DigitalStore = false;
    /**
     * @field float
     */
    public $CriticalSupply = 5;
    public $MerchandiseUnit = " merchandises";
    public $ItemsUnit = " items";
    public $RootUrlPath = "/shop/";
    public $DefaultMenu = true;

    /**
     * @category Price
     * @field float
     * @var float
     * A value in percentage
     */
    public $DefaultDiscount = 1;
    /**
     * @category Price
     * @field float
     * @var float
     * A value in percentage
     */
    public $BaseDiscount = 0;
    /**
     * @category Price
     * @field pairs
     * @example ["Global Discount" => "-20%", "Tax" => "10%"]
     */
    public $DigitalPriceParams = ["Initializing" => "0"];
    /**
     * @category Price
     * @field pairs
     * @example ["Global Discount" => "-20%", "Tax" => "10%"]
     */
    public $PhysicalPriceParams = ["Shipping" => "10%", "Insurance" => "2%", "Packing" => "0"];
    /**
     * An array of regex patterns to address based pricing
     * @category Price
     * @field pairs
     * @example ["USA$" => "1", "UK$" => "10"]
     */
    public $ShippingPriceParams = [];
    /**
     * The value parameters indicator
     * @category Price
     * @field text
     */
    public $ValueIndicator = " (value)";
    /**
     * The percent parameters indicator
     * @category Price
     * @field text
     */
    public $PercentIndicator = " (value {0}%)";
    /**
     * @category Price
     * @field pairs
     * @example ["Global Discount" => "-20%", "Tax" => "10%"]
     */
    public $GlobalPriceParams = ["Tax" => "10%"];
    /**
     * An array of minimum parameters
     * @category Price
     * @field pairs
     * @example ["Shipping" => "10", "Packing" => "0.01"]
     */
    public $MinimumPriceParams = ["Shipping" => "5"];
    /**
     * The minimum value parameters indicator
     * @category Price
     * @field text
     */
    public $MinimumValueIndicator = " (base)";
    /**
     * The minimum percent parameters indicator
     * @category Price
     * @field text
     */
    public $MinimumPercentIndicator = " (base {0}%)";
    /**
     * An array of maximum parameters
     * @category Price
     * @field pairs
     * @example ["Shipping" => "100", "Packing" => "50"]
     */
    public $MaxmumPriceParams = ["Shipping" => "500", "Packing" => "20%"];
    /**
     * The maximum value parameters indicator
     * @category Price
     * @field text
     */
    public $MaximumValueIndicator = " (limit)";
    /**
     * The maximum percent parameters indicator
     * @category Price
     * @field text
     */
    public $MaximumPercentIndicator = " (limit {0}%)";

    /**
     * @category Item
     * @field text
     */
    public $ItemDefaultTitle = "Merchandise";
    /**
     * @category Item
     * @field texts
     */
    public $ItemDefaultDescription = null;
    /**
     * @category Item
     * @field image
     */
    public $ItemDefaultImagePath = "/asset/logo/logo.png";
    /**
     * @category Item
     * @field text
     */
    public $ItemUrlPath = "/shop/item";
    /**
     * @category Item
     * @field text
     */
    public $ItemRootUrlPath = "/shop/item/";
    /**
     * @category Item
     * @field json
     */
    public $ItemMetaData = null;

    /**
     * @category Items
     * @field text
     */
    public $ItemsTitle = "Merchandises";
    /**
     * @category Items
     * @field texts
     */
    public $ItemsDescription = null;
    /**
     * @category Items
     * @field path
     */
    public $ItemsImage = "box";
    /**
     * @category Items
     * @field text
     */
    public $ItemsUrlPath = "/shop/items";
    /**
     * @category Items
     * @field text
     */
    public $ItemsRootUrlPath = "/shop/items/";
    /**
     * @category Items
     * @field json
     */
    public $ItemsMetaData = null;

    /**
     * @category Cart
     * @field text
     */
    public $CartTitle = "Cart";
    /**
     * @category Cart
     * @field texts
     */
    public $CartDescription = null;
    /**
     * @category Cart
     * @field content
     */
    public $CartContent = null;
    /**
     * @category Cart
     * @field path
     */
    public $CartImage = "shopping-cart";
    /**
     * @category Cart
     * @field text
     */
    public $CartRootUrlPath = "/shop/cart/";
    /**
     * @category Cart
     * @field text
     */
    public $CartUrlPath = "/shop/cart";
    /**
     * @category Cart
     * @field json
     */
    public $CartMetaData = null;

    /**
     * @category Groups
     * @field text
     */
    public $GroupsTitle = "Favorites";
    /**
     * @category Groups
     * @field texts
     */
    public $GroupsDescription = null;
    /**
     * @category Groups
     * @field content
     */
    public $GroupsContent = null;
    /**
     * @category Groups
     * @field path
     */
    public $GroupsImage = "heart";
    /**
     * @category Groups
     * @field text
     */
    public $GroupsUrlPath = "/shop/groups";
    /**
     * @category Groups
     * @field json
     */
    public $GroupsMetaData = null;

    /**
     * @category Requests
     * @field text
     */
    public $RequestsTitle = "Requests";
    /**
     * @category Requests
     * @field texts
     */
    public $RequestsDescription = null;
    /**
     * @category Requests
     * @field content
     */
    public $RequestsContent = null;
    /**
     * @category Requests
     * @field path
     */
    public $RequestsImage = "list";
    /**
     * @category Requests
     * @field text
     */
    public $RequestsUrlPath = "/shop/requests";
    /**
     * @category Requests
     * @field json
     */
    public $RequestsMetaData = null;
    
    /**
     * @category Responses
     * @field bool
     */
    public $Returnable = true;
    /**
     * @category Responses
     * @field text
     */
    public $ResponsesUrlPath = "/shop/responses";
    /**
     * @category Responses
     * @field Pairs
     */
    public $ResponsesStatuses = [
        "Returned"=>"Returned",
        "Rejected"=>"Rejected",
        "Canceled"=>"Canceled",
        "Defected"=>"Defected",
        "Unavailable"=>"Unavailable",
        "Unaccepted"=>"Unaccepted",
        "Unchecked"=>"Unchecked",
        "Accepted"=>"Accepted",
        "Prepared"=>"Prepared",
        "Sent"=>"Sent",
        "Received"=>"Received",
        "Delivered"=>"Delivered",
        "Finished"=>"Finished"
    ];
    /**
     * @category Responses
     * @field text
     */
    public $ReturnedStatus = "Returned";
    /**
     * @category Responses
     * @field text
     */
    public $RejectedStatus = "Rejected";
    /**
     * @category Responses
     * @field text
     */
    public $CanceledStatus = "Canceled";
    /**
     * @category Responses
     * @field text
     */
    public $DefectedStatus = "Defected";
    /**
     * @category Responses
     * @field text
     */
    public $UnavailableStatus = "Unavailable";
    /**
     * @category Responses
     * @field text
     */
    public $UnacceptedStatus = "Unaccepted";
    /**
     * @category Responses
     * @field text
     */
    public $UncheckedStatus = "Unchecked";
    /**
     * @category Responses
     * @field text
     */
    public $AcceptedStatus = "Accepted";
    /**
     * @category Responses
     * @field text
     */
    public $PreparedStatus = "Prepared";
    /**
     * @category Responses
     * @field text
     */
    public $SentStatus = "Sent";
    /**
     * @category Responses
     * @field text
     */
    public $ReceivedStatus = "Received";
    /**
     * @category Responses
     * @field text
     */
    public $DeliveredStatus = "Delivered";
    /**
     * @category Responses
     * @field text
     */
    public $FinishedStatus = "Finished";
    /**
     * @category Responses
     * @field text
     */
    public $PhysicalInitialStatus = "Unchecked";
    /**
     * @category Responses
     * @field text
     */
    public $PhysicalResponseStatus = "Delivered";
    /**
     * @category Responses
     * @field text
     */
    public $PhysicalFinalStatus = "Finished";
    /**
     * @category Responses
     * @field text
     */
    public $DigitalInitialStatus = "Accepted";
    /**
     * @category Responses
     * @field text
     */
    public $DigitalResponseStatus = "Sent";
    /**
     * @category Responses
     * @field text
     */
    public $DigitalFinalStatus = "Finished";

    public function StatusToIInt($status) {
        $keys = array_keys($this->ResponsesStatuses);
        if(find( $keys, $status, $key, $index))
            return $index - (count($this->ResponsesStatuses)-1)/2;
        return null;
    }
    public function PreviousStatus($status) {
        $keys = array_keys($this->ResponsesStatuses);
        if(find( $keys, $status, $key, $index))
            return $keys[$index-1]??null;
        return null;
    }
    public function NextStatus($status) {
        $keys = array_keys($this->ResponsesStatuses);
        if(find( $keys, $status, $key, $index))
            return $keys[$index+1]??null;
        return null;
    }


    /**
     * @category Options
     * @field text
     */
    public $OptionsTitle = "Delivery";
    /**
     * @category Options
     * @field texts
     */
    public $OptionsDescription = null;
    /**
     * @category Options
     * @field content
     */
    public $OptionsContent = null;
    /**
     * @category Options
     * @field path
     */
    public $OptionsImage = "truck";
    /**
     * @category Options
     * @field text
     */
    public $OptionsUrlPath = "/shop/options";
    /**
     * @category Options
     * @field json
     */
    public $OptionsMetaData = null;

    /**
     * @category Preview
     * @field text
     */
    public $PreviewTitle = "Preview";
    /**
     * @category Preview
     * @field texts
     */
    public $PreviewDescription = null;
    /**
     * @category Preview
     * @field content
     */
    public $PreviewContent = null;
    /**
     * @category Preview
     * @field path
     */
    public $PreviewImage = "check";
    /**
     * @category Preview
     * @field text
     */
    public $PreviewUrlPath = "/shop/preview";
    /**
     * @category Preview
     * @field json
     */
    public $PreviewMetaData = null;

    /**
     * @category Payment
     * @field text
     */
    public $PaymentTitle = "Payment";
    /**
     * @category Payment
     * @field texts
     */
    public $PaymentDescription = null;
    /**
     * @category Payment
     * @field content
     */
    public $PaymentContent = null;
    /**
     * @category Payment
     * @field path
     */
    public $PaymentImage = "credit-card";
    /**
     * @category Payment
     * @field text
     */
    public $PaymentUrlPath = "/shop/payment";

    /**
     * @category Access
     * @field int
     * @options min:1, max:999999999
     */
    public $BuyingAccess = 1;
    /**
     * @category Access
     * @field int
     * @options min:1, max:999999999
     */
    public $SellingAccess = 1000;
    /**
     * @category Access
     * @field int
     * @options min:1, max:999999999
     */
    public $AmbassadorsAccess = 500;
    /**
     * @category Access
     * @field int
     */
    public $CommentsAccess = 1;
    /**
     * @category Access
     * @field text
     */
    public $SignInUrlPath = "/shop/sign-in";
    /**
     * @category Access
     * @field text
     */
    public $SignUpUrlPath = "/shop/sign-up";
    /**
     * @category Access
     * @field text
     */
    public $SignRecoverUrlPath = "/shop/sign-recover";
    /**
     * @category Access
     * @field text
     */
    public $DiscountUrlPath = "/shop/discount";

    public function ComputeTotal($totalAmount, $cart = [], &$billPriceParams = [])
    {
        if ($discountRecord = $this->ValidDiscount()) {
            if ($discountRecord["Condition"] && !graphAnd($cart, $discountRecord["Condition"]))
                return $totalAmount;
            $percent = 0;
            $dc = $this->ComputeDiscount($discountRecord, $totalAmount, 1, $percent);
            if ($dc) {
                $t = $discountRecord["Title"] ?? 'Dedicated Discount';
                $billPriceParams[$t] = ($billPriceParams[$t] ?? 0) - $dc;
                $totalAmount = round($totalAmount - $dc, \_::$Joint->Finance->DecimalPercision);
            }
        }
        return $totalAmount;
    }
    public function ComputeAmount($item = null, $amount = null, $count = null, $discount = null, $isDigital = null, $address = null, $metadata = null, &$billPriceParams = [])
    {
        if ($item) {
            $amount = $amount ?? \_::$Joint->Finance->StandardCurrency(get($item, 'MerchandisePrice'), get($item, 'MerchandiseCurrnecy'));
            $count = $count ?? get($item, 'RequestCount') ?? 1;
            $discount = $discount ?? ($this->BaseDiscount + (get($item, 'MerchandiseDiscount') ?: $this->DefaultDiscount));
            $isDigital = $isDigital ?? get($item, 'MerchandiseDigital');
            $address = $address ?? get($item, 'RequestAddress');
            $metadata = $metadata ?? get($item, 'MerchandiseMetaData');
        }
        if(isEmpty($amount)) return $amount;
        
        if ($discount)
            $billPriceParams['Discount'] = ($billPriceParams['Discount'] ?? 0) - ($discount * $count * $amount / 100);
        else
            $discount = 0;

        $amount = ($count * $amount) - ($discount * $count * $amount / 100);
        $params = [];

        if ($isDigital && $this->DigitalPriceParams)
            foreach ($this->DigitalPriceParams ?? [] as $key => $value)
                $params[$key] = [...($params[$key] ?? []), $value];
        elseif ($this->PhysicalPriceParams) {
            if ($address && !isset($billPriceParams["Shipping"]) && $this->ShippingPriceParams) {
                foreach ($this->ShippingPriceParams ?? [] as $key => $value)
                    if (preg_match($key, $address))
                        $params["Shipping"] = [...($params["Shipping"] ?? []), $value];
            }
            foreach ($this->PhysicalPriceParams ?? [] as $key => $value)
                $params[$key] = [...($params[$key] ?? []), $value];
        }
        if ($this->GlobalPriceParams)
            foreach ($this->GlobalPriceParams ?? [] as $key => $value)
                $params[$key] = [...($params[$key] ?? []), $value];

        if (isset($metadata["PriceParams"]))
            foreach ($metadata["PriceParams"] ?? [] as $key => $value)
                $params[$key] = [...($params[$key] ?? []), $value];

        foreach ($params as $key => $values) {
            $add = 0;
            $sx = 0;
            foreach ($values as $value)
                $add += $this->ComputeParam($amount, $count, $value, $sx);

            if ($sx)
                $sx = str_replace("{0}", $sx, $this->PercentIndicator);
            else
                $sx = str_replace("{0}", $add, $this->ValueIndicator);
            if ($value = ($this->MinimumPriceParams[$key] ?? null)) {
                $sxm = 0;
                $addm = $this->ComputeParam($amount, 1, $value, $sxm);
                if ($add < $addm) {
                    $add = $addm;
                    if ($sxm)
                        $sx = str_replace("{0}", $sxm, $this->MinimumPercentIndicator);
                    else
                        $sx = str_replace("{0}", $add, $this->MinimumValueIndicator);
                }
            }
            if ($value = ($this->MaxmumPriceParams[$key] ?? null)) {
                $sxm = 0;
                $addm = $this->ComputeParam($amount, 1, $value, $sxm);
                if ($add > $addm) {
                    $add = $addm;
                    if ($sxm)
                        $sx = str_replace("{0}", $sxm, $this->MaximumPercentIndicator);
                    else
                        $sx = str_replace("{0}", $add, $this->MaximumValueIndicator);
                }
            }

            $amount += $add;
            if ($sx)
                $key .= $sx;
            $billPriceParams[$key] = round(($billPriceParams[$key] ?? 0) + $add, \_::$Joint->Finance->DecimalPercision);
        }
        return round($amount, \_::$Joint->Finance->DecimalPercision);
    }
    public function ComputeParam($amount = 0, $count = 1, $value = 0, &$percent = 0)
    {
        if ($perc = preg_find("/^\s*[-+]?\d*\.?\d+\s*(?=%)/", "$value")) {
            $perc = floatval($perc);
            if ($percent >= 0)
                $percent += $perc;
            return $perc * $amount / 100;
        } else {
            $percent = null;
            return $count * floatval($value);
        }
    }
    public function ComputeDiscount($discountCard = null, $amount = 0, $count = 1, &$percent = 0)
    {
        if ($count === 0 || !$amount)
            return 0;
        $val = $this->ComputeParam($amount, $count, $discountCard["Value"], $prc);
        $percent = $val * 100 / ($amount * $count);
        if ($discountCard["MinimumAmount"] && $discountCard["MinimumAmount"] >= $val)
            return $discountCard["MinimumAmount"];
        if ($discountCard["MaximumAmount"] && $discountCard["MaximumAmount"] <= $val)
            return $discountCard["MaximumAmount"];
        if ($discountCard["MinimumValue"] && $discountCard["MinimumValue"] >= $percent)
            return $discountCard["MinimumValue"] * $amount / 100;
        if ($discountCard["MaximumValue"] && $discountCard["MaximumValue"] <= $percent)
            return $discountCard["MaximumValue"] * $amount / 100;
        return $val;
    }

    public function ValidDiscount($code = null, &$message = null)
    {
        $code = $code ?: $this->GetDiscountCode();
        if ($code && $this->SetDiscountCode($code, $message))
            return $this->GetDiscount($code);
        $message = Struct::Error("The code is not correct!");
        return null;
    }
    public function UseDiscount($code = null)
    {
        $n = table("Discount")->SelectValue("Number", "Name=:Name", [":Name" => $code = $code ?: $this->PopDiscountCode()]);
        if (is_null($n))
            return false;
        return table("Discount")->Update("Name=:Name", [":Name" => $code, ":Number" => ++$n]);
    }
    public function GetDiscount($code = null)
    {
        $discountRecord = table("Discount")->SelectRow("*", "Name=:Name", [":Name" => $code = $code ?: $this->GetDiscountCode()]);
        if ($discountRecord) {
            if ($discountRecord["Condition"])
                $discountRecord["Condition"] = Convert::FromJson($discountRecord["Condition"]);
            if ($discountRecord["MetaData"])
                $discountRecord["MetaData"] = Convert::FromJson($discountRecord["MetaData"]);
        }
        return $discountRecord;
    }
    public function SetDiscountCode($code = null, &$message = null)
    {
        if ($dc = $this->GetDiscount($code)) {
            if ($dc["Status"] && intval($dc["Status"]) <= 0)
                return ($message = Struct::Error("This code is not active!")) ? false : false;
            if ($dc["Count"] <= $dc["Number"])
                return ($message = Struct::Error("This code is used {$dc["Number"]} time before!")) ? false : false;
            if ($dc["Access"]) if (!\_::$User->HasAccess($dc["Access"]))
                return ($message = Struct::Error("You do not have enough access to use this code!")) ? false : false;
            if ($dc["UserId"]) if ($dc["UserId"] !== \_::$User->Id)
                return ($message = Struct::Error("This code is for another 'user'!")) ? false : false;
            if ($dc["UserCode"]) if ($dc["UserCode"] !== getClientCode())
                return ($message = Struct::Error("This code is for another 'client'!")) ? false : false;
            if ($dc["Contact"]) if ($dc["Contact"] !== \_::$User->GetValue("Contact"))
                return ($message = Struct::Error("This code is for another 'user contact'!")) ? false : false;
            $now = Convert::ToDateTime();
            if (Convert::ToDateTime($dc["StartTime"]) > $now)
                return ($message = Struct::Warning("The code is not usable yet!")) ? false : false;
            if (Convert::ToDateTime($dc["EndTime"]) < $now)
                return ($message = Struct::Error("The code is expired!")) ? false : false;

            if (setSecret("__SHOP_DISCOUNT", $code))
                return ($message = Struct::Success("This discount code submitted successfuly!")) ? true : true;
        }
        return ($message = Struct::Error("There not find any discount!")) ? false : false;
    }
    public function GetDiscountCode()
    {
        return getSecret("__SHOP_DISCOUNT");
    }
    public function PopDiscountCode()
    {
        return popSecret("__SHOP_DISCOUNT");
    }
    /**
     * Get the lists of sites
     * @param mixed ...$levels Continent, Country, State, City, ...
     */
    public function GetSites($isDigital = null, ...$levels)
    {
        $sites = null;
        if (is_null($isDigital)) {
            $sites = Convert::FromJson(open($this->PhysicalSitesPath)) ?? [];
            foreach (Convert::FromJson(open($this->DigitalSitesPath)) ?? [] as $key => $value)
                $sites[$key] = $value;
        }
        if ($isDigital)
            $sites = Convert::FromJson(open($this->DigitalSitesPath)) ?? [];
        else
            $sites = Convert::FromJson(open($this->PhysicalSitesPath)) ?? [];
        $nsites = [];
        foreach (get($sites, ...$levels) as $key => $value) {
            if ($value)
                if (is_array($value))
                    $nsites[$key] = $levels ? $key : loop($value, fn($v, $k) => is_numeric($k) ? [$v => $v] : [$k => $k], pair: true);
                else
                    $nsites[$value] = $value;
        }
        return $nsites;
    }

    public function CartCondition($tableName = null, $userId = null)
    {
        $userId = $userId ?? \_::$User->Id;
        if ($tableName)
            $tableName .= ".";
        return "(
            ({$tableName}Collection IS NULL OR {$tableName}Collection='Collected') AND
            (" .
            (isValid($userId) ? "{$tableName}UserId=$userId OR " : "") .
            "({$tableName}UserId IS NULL AND {$tableName}UserCode='" . getClientCode() . "')
            )
        )";
    }
}