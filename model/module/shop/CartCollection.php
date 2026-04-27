<?php
namespace MiMFa\Module\Shop;
use MiMFa\Library\Struct;
use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Style;

module("shop\MerchandiseCollection");
/**
 * To show cart items
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class CartCollection extends MerchandiseCollection
{
    public string|null $TitleTagName = "h2";
    public $MaximumColumns = 1;
    /**
     * The Width of thumbnail preshow
     * @var string
     */
    public $ImageWidth = "var(--size-4)";
    /**
     * The Height of thumbnail preshow
     * @var string
     */
    public $ImageHeight = "var(--size-4)";

    public $AllowInvoice = true;

    /**
     * @var bool
     * @category Parts
     */
    public $AllowAddress = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowContact = true;

    public $AllowItems = true;

    public $BillAppend = null;
    public $BillPrepend = null;
    public $NextButton = null;
    public $BackButton = null;

    // public $AddButtonLabel = null;
    // public $CartButtonLabel = null;
    public $EmptyHandler = null;

    public $MoreButtonLabel = null;

    function __construct()
    {
        parent::__construct();
        if (is_null($this->EmptyHandler))
            $this->EmptyHandler = Struct::Container([
                Struct::Media("heart-broken", ["style" => "font-size:20vmin; color: #8888;"]),
                Struct::Center("You haven't selected anything yet!"),
                Struct::$Break,
                [Struct::Button("Add something...", $this->CollectionRoot, ["class" => "main be fit"])]
            ], ["class" => "be align center"]);
    }

    public function GetStyle()
    {
        return Struct::Style("
            .{$this->MainClass} div.item {
                height: fit-attach;
                width: -webkit-fill-available;
                width: fit-content;
                background-Color: #88888808;
                margin: calc(var(--size-1) / 2);
                padding: var(--size-2);
                font-size: var(--size-0);
                box-shadow: var(--shadow-1);
                border-radius: var(--radius-2);
                border: var(--border-1) var(--back-color-special-output);
                " . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->MainClass} div.item:hover{
                box-shadow: var(--shadow-2);
                border-radius:  var(--radius-1);
                border-color: var(--back-color-special-input);
                background-Color: #88888818;
                " . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->MainClass} div.item.deactive {
                background-Color: #88888844;
                box-shadow: var(--shadow-0);
                border-radius: var(--radius-0);
                border: none;
                " . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }

            .{$this->MainClass} div.item .item-title{
                display: flex;
                align-items: center;
                gap: var(--size-0);
            }
            .{$this->MainClass} div.item .item-title .divistion{
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
                gap: 0px;
            }
            .{$this->MainClass} div.item .title{
                font-weight: bold;
                display: inline;
                margin-top: 0px;
                margin-bottom: 0px;
            }
            .{$this->MainClass} div.item .item-image {
                background-color: var(--back-color-output);
                color: var(--fore-color-output);
                opacity: 0.6;
                aspect-ratio: 1;
                box-shadow: var(--shadow-1);
                border-radius: var(--radius-1);
                line-height: {$this->ImageWidth};
                width: {$this->ImageWidth};
                height: {$this->ImageHeight};
                margin: 0px;
                margin-inline-end: calc(var(--size-0) / 3);
                padding: 0px;
                overflow: hidden;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                " . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->MainClass} div.item:hover .item-image{
                opacity: 1;
                " . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->MainClass} .price{
                display: flex;
                align-content: end;
                justify-content: flex-end;
                flex-wrap: wrap;
                column-gap: var(--size-0);
                flex-direction: column;
            }
            .{$this->MainClass} .price .discount{
                color: #8888;
                font-size: var(--size-0);
                line-height: 0;
                display: flex;
                align-content: center;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: flex-start;
                gap: calc(var(--size-0) / 2);
            }
            .{$this->MainClass} .price .discount .value{
                font-size: calc(var(--size-4) / 2);
                background-color: var(--color-red);
                color: var(--color-white);
                border-radius: var(--radius-2);
                padding: calc(var(--size-0) / 2);
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: center;
            }
            .{$this->MainClass} div.item .description{
                gap: var(--size-0);
                position: relative;
                overflow-wrap: break-word;
                flex-flow: wrap;
                text-wrap-mode: wrap;
                " . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->MainClass} div.item .description :is(.excerpt, .full){
                padding-inline-end: calc(var(--size-0) / 3);
            }
            
            .{$this->MainClass} div.item .supplier{
                font-size: calc(var(--size-0) * 0.8);
                display: flex;
                align-content: center;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: flex-start;
                gap: calc(var(--size-0) / 3);
            }
            .{$this->MainClass} div.item .supplier .image{
                font-size: var(--size-1);
                height: var(--size-1);
                min-height: var(--size-1);
                display:inline-flex;
            }

            .{$this->MainClass} div.item .footer{
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                    align-content: flex-start;
            }
            .{$this->MainClass} div.item .detail{
                    font-size: calc(var(--size-0) * 0.8);
                    opacity: 0.8;
            }
            .{$this->MainClass} div.item .metadata{
                    font-size: calc(var(--size-0) * 0.8);
                    opacity: 0.8;
                    display: flex;
                    flex-wrap: wrap;
                    align-items: flex-start;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-content: flex-start;
            }
            .{$this->MainClass} div.item .metadata>:not(.supplier){
                padding-inline-end: calc(var(--size-0) / 2);
                display: inline-block;
            }
                
            .{$this->MainClass} .controls {
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: flex-start;
                flex-direction: row-reverse;
                gap: var(--size-0);
            }

            .{$this->MainClass} .invoice {
                padding: var(--size-1) calc(var(--size-1) / 3) var(--size-max);
            }
            .{$this->MainClass} .invoice>* {
                display: flex;
                justify-content: space-between; 
                align-items: center;
                gap: calc(var(--size-0) / 3);
            }
            .{$this->MainClass} .invoice .price-total{
                font-weight: bold;
            }
            .{$this->MainClass} .invoice .input{
                border-radius: var(--radius-3);
                text-align:center;
            }
            .{$this->MainClass} .invoice .final {
                justify-content: center;
            }
                
            .{$this->MainClass}-additional{
                color: unset;
            }
        ") . $this->GetDefaultStyle();
    }

    public function GetInner($items = null)
    {
        $bill = $this->ComputeInvoice($items);
        $cartItems = join(PHP_EOL, iterator_to_array((function () use ($items, $bill) {
            $i = 0;
            foreach ($bill["Items"] as $id => $item) {
                if ($meta = get($item["Content"], 'MetaData')) {
                    $meta = Convert::FromJson($meta);
                    pod($this, $meta);
                }

                $merch = first(seek($items, function($v) use($id) { return $v["RequestId"] === $id;})) ?? $item["Content"];

                $r_description = get($item["Request"], 'Description') ?: $this->DefaultDescription;
                $r_contact = get($item["Request"], 'Contact') ?: \_::$User->GetValue("Contact");

                $r_price = get($item["Request"], 'Price');//Total Price
                $r_amount = get($item["Request"], 'Amount');//Total Amount

                $m_id = get($item["Merchandise"], 'Id');
                $m_discount = get($item["Merchandise"], 'Discount');

                $c_image = get($item["Merchandise"], 'Image') ?: get($item["Content"], 'Image') ?: $this->DefaultImage;
                $c_title = get($item["Merchandise"], 'Title') ?: get($item["Content"], 'Title') ?: $this->DefaultTitle;

                $shownId = "m_$m_id";
                $meta = "";
                if ($this->AllowMetaData) {
                    if ($this->AllowCreateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Struct::Span(Convert::ToShownDateTimeString($val), ["class" => 'createtime']);
                            },
                            $item["Content"],
                            'CreateTime'
                        );
                    if ($this->AllowUpdateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Struct::Span(Convert::ToShownDateTimeString($val), ["class" => 'updatetime']);
                            },
                            $item["Content"],
                            'UpdateTime'
                        );
                }

                if ($i % $this->MaximumColumns === 0)
                    yield "<div class='row'>";
                yield "<div id='$shownId' class='item col col-lg'" . ($this->Animation ? " data-aos-delay='" . ($i % $this->MaximumColumns * \_::$Front->AnimationSpeed) . "' data-aos='{$this->Animation}'" : "") . ">";

                yield Struct::Rack(
                    Struct::MediumSlot(
                        ($this->AllowImage ? Struct::Image($c_title, $c_image, \_::$User->DefaultImagePath, ["class" => "item-image"]) : "") .
                        Struct::Division(
                            ($this->AllowTitle ? Struct::Heading4($c_title, $this->Root . $m_id, ["class" => 'title']) : "") .
                            ($this->AllowSupplier ? $this->GetSupplier($merch) : "")
                        ),
                        ["class" => 'item-title']
                    ) .
                    Struct::MediumSlot(
                        Struct::Division($m_discount ?
                            Struct::Span($m_discount . "%", null, ["class" => "value"]) . " " .
                            ($r_price?Struct::Strike($r_price):"") . " "
                            : "", ["class" => "discount"]) .
                        Struct::Span(\_::$Joint->Finance->AmountStruct($r_amount, null, ["class"=>"be bold"])),
                        ["class" => 'col-md-3 price']
                    )
                );
                if ($this->AllowDescription && $r_description)
                    yield Struct::Division(Struct::Convert($this->AutoExcerpt ? Convert::ToExcerpt($r_description, 0, $this->ExcerptLength, $this->ExcerptSign) : $r_description), ["class" => 'detail description']);
                if ($this->AllowContact && $r_contact)
                    yield Struct::Box([Struct::Icon("phone"), $r_contact], ["class" => 'detail contact']);
                yield Struct::Rack(
                    (isValid($meta) ? Struct::MediumSlot($meta, ["class" => 'col-md metadata']) : "") .
                    ($this->AllowButtons ? Struct::MediumSlot(
                        $this->GetButtons($shownId, $merch) .
                        $this->GetAdditionalButtons($shownId, $merch),
                        ["class" => 'col-md controls']
                    ) : "")
                    ,
                    ["class" => "footer"]
                );
                yield "</div>";
                if (++$i % $this->MaximumColumns === 0)
                    yield "</div>";
            }
            if ($i % $this->MaximumColumns !== 0)
                yield "</div>";
            yield ($this->MoreButtonLabel ? Struct::$Break . Struct::Center(Struct::Button($this->MoreButtonLabel, $this->CollectionRoot)) : "");
        })()));

        yield !$bill["Variety"] ? __($this->EmptyHandler, styling: true, referring: true) : Struct::Rack(
            ($this->AllowInvoice ? Struct::Aside($this->GetInvoice($bill), ["class" => "col-lg col-lg-4"]) : "") .
            Struct::Section(
                $this->GetTitle() .
                $this->GetDescription() .
                ($this->AllowItems ? $cartItems : "") .
                $this->GetContent()
                ,
                ["class" => "col-lg"]
            )
        );
    }

    public function ComputeInvoice($items = null)
    {
        $merchandises = 0;
        $totalCount = 0;
        $totalAmount = null;
        $totalPrice = null;
        $priceParams = [];
        $requests = [];

        foreach (Convert::ToItems($items ?? $this->Items) as $k => $item) {
            $merchandises++;
            $r_id = get($item, 'RequestId');
            $r_count = get($item, 'RequestCount');
            $r_address = get($item, 'RequestAddress');

            $m_id = get($item, 'MerchandiseId');
            $m_metadata = Convert::FromJson(get($item, 'MerchandiseMetaData'));
            $m_digital = get($item, 'MerchandiseDigital') ?? \_::$Joint->Shop->DigitalStore;
            $m_count = get($item, 'MerchandiseCount');
            $m_count = min(get($item, 'MerchandiseLimit') ?: $m_count, $m_count);
            $r_count = min($r_count, $m_count);
            $m_discount = \_::$Joint->Shop->BaseDiscount + (get($item, 'MerchandiseDiscount') ?: \_::$Joint->Shop->DefaultDiscount);
            $m_amountunit = get($item, 'MerchandiseCurrency');//Unit Amount
            $m_price = \_::$Joint->Finance->StandardCurrency(get($item, 'MerchandisePrice'), $m_amountunit);
            $r_price = $m_price?$r_count * $m_price:$m_price;//Total Amount
            $m_amount = $m_price?$m_price - $m_discount * $m_price / 100:$m_price;
            $r_amount = $r_price?$r_price - $m_discount * $r_price / 100:$r_price;

            $requests[$r_id] = [
                "Request" => [
                    "Id" => $r_id,
                    "Count" => $r_count,
                    "Price" => $r_price,
                    "Amount" => $r_amount,
                    "Subject" => get($item, 'RequestSubject'),
                    "Description" => get($item, 'RequestDescription'),
                    "Contact" => get($item, 'RequestContact'),
                    "Address" => $r_address,
                    "CreateTime" => get($item, 'RequestCreateTime')
                ],
                "Merchandise" => [
                    "Id" => $m_id,
                    "Count" => $m_count,
                    "Price" => $m_price,
                    "Amount" => $m_amount,
                    "Discount" => $m_discount,
                    "Digital" => $m_digital,
                    "Title" => get($item, 'MerchandiseTitle'),
                    "Image" => get($item, 'MerchandiseImage'),
                    "Description" => get($item, 'MerchandiseDescription'),
                    "MetaData" => $m_metadata
                ],
                "Content" => $item
            ];
            
            if(!isEmpty($r_price)) $totalPrice = ($totalPrice?:0) + $r_price;
            if(!isEmpty($m_price)) $totalAmount = ($totalAmount?:0) + \_::$Joint->Shop->ComputeAmount($item, $m_price, $r_count, $m_discount, $m_digital, $r_address, $m_metadata, $priceParams);
            $totalCount += $r_count;
        }

        if(!isEmpty($totalAmount)) $totalAmount = \_::$Joint->Shop->ComputeTotal($totalAmount, $requests, $priceParams);

        $priceParams = [
            "Price" => $totalPrice,
            ...$priceParams
        ];
        // if ($priceParams['Price'] !== $totalAmount)
        //     $priceParams['Amount'] = $totalAmount;
        return [
            "Amount" => $totalAmount,
            "Count" => $totalCount,
            "Variety" => $merchandises,
            "Params" => $priceParams,
            "Items" => $requests,
        ];
    }
    public function GetInvoice($bill = null)
    {
        $bill = $bill ?? $this->ComputeInvoice();
        return Struct::Division([
            $this->BillPrepend,
            [...($bill["Variety"] ? [Struct::Span($bill["Variety"] . \_::$Joint->Shop->MerchandiseUnit), Struct::Span($bill["Count"] . \_::$Joint->Shop->ItemsUnit)] : ["", Struct::Span($bill["Count"] . \_::$Joint->Shop->ItemsUnit)])],
            ...loop($bill["Params"], fn($v, $k) => [Struct::Span($k), \_::$Joint->Finance->AmountStruct($v)]),
            ...(
                ($dcode = \_::$Joint->Shop->GetDiscountCode()) ?
                [
                    Struct::Span("Discount Code") .
                    Struct::Box(
                        [
                            Struct::Span($dcode, ["style" => "opacity:0.7;", "Tooltip" => "Your confirmed discount code"]),
                            Struct::Icon("close", Script::Send(
                                "Delete",
                                \_::$Joint->Shop->DiscountUrlPath,
                                ["DiscountCode" => $dcode]
                            ), ["class" => "be red fore"])
                        ]
                    )
                ] : [
                    Struct::Span("Discount Code") .
                    Struct::Box(
                        [
                            Struct::TextInput("Code", null, ["style" => "width:calc(3 * var(--size-max));", "PlaceHolder" => "Put your discount code"]),
                            Struct::Icon("plus", "if(this.previousElementSibling.value) " . Script::Send(
                                "PUT",
                                \_::$Joint->Shop->DiscountUrlPath,
                                ["DiscountCode" => "\${this.previousElementSibling.value}"]
                            ) . "; else this.previousElementSibling.focus();", ["class" => "be green fore"])
                        ],
                        ["class"=>"be middle"]
                    )
                ]
            ),
            $this->BillAppend,
            Struct::$BreakLine,
            [Struct::Label("Total:"), Struct::Span(\_::$Joint->Finance->AmountStruct($bill["Amount"]), null, ["class" => "price-total"])],
            Struct::$Break,
            $this->NextButton . $this->BackButton
        ], ["class" => "invoice be sticky"]);
    }

    public function GetSupplier($item)
    {
        $m_digital = get($item, 'MerchandiseDigital') ?? \_::$Joint->Shop->DigitalStore;
        $sup = \_::$Front->Name;
        $del = "";
        if (isValid($item["MerchandiseSupplierId"]) && ($d = table("User")->SelectRow("Id, Organization, Name, Image", "WHERE `Id`=:Id", [":Id" => $item["MerchandiseSupplierId"]]))) {
            $sup = $d["Organization"] ? $d["Organization"] : ($d["Name"] ? $d["Name"] : "Unknown");
            $del = Struct::Image(null, $d["Image"] ? $d["Image"] : \_::$User->DefaultImagePath) .
                Struct::Link(
                    $sup,
                    \_::$Address->UserRootUrlPath . $d["Id"]
                );
        } else
            $del = Struct::Icon(\_::$Front->LogoPath);
        $del .= $this->DeliveryLabel . Struct::Icon($m_digital ? "envelope" : "map-marker") . Struct::Tooltip($m_digital ? "$sup will send to your email" : "$sup will send to your location");
        $r_address = get($item, 'RequestAddress');
        if ($this->AllowAddress && $r_address)
            $del .= $r_address;
        return Struct::Division($del, ["class" => "supplier"]);
    }

    public function GetScript()
    {
        yield parent::GetScript();
        yield Struct::Script("
            function {$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId, count = null, maxCount = null){
                if(data === true || data === 'true') return _(`#\${shownId} .wish`).addClass('active');
                else if(data === false || data === 'false') return _(`#\${shownId} .wish`).removeClass('active');
                
                d = parseFloat(data?data:0);
                _(`#\${shownId} .numbers`)?.html(d);
                if(d<=0) {
                    _(`#\${shownId} .{$this->MainClass}-buttons`).addClass('hide');
                    _(`#\${shownId} .button.order`).removeClass('hide');
                }
                else{
                    _(`#\${shownId} .button.order`).addClass('hide');
                    _(`#\${shownId} .{$this->MainClass}-buttons`).removeClass('hide');
                    
                    if(d > 1) _(`#\${shownId} .button.decrease`).removeClass('hide');
                    else _(`#\${shownId} .button.decrease`).addClass('hide');
                    
                    if(d < maxCount) _(`#\${shownId} .button.increase`).removeClass('hide');
                    else _(`#\${shownId} .button.increase`).addClass('hide');
                }
                if(!err) reload(location.pathname + '#' + shownId);
            }
        ");
    }
}