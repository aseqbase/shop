<?php
namespace MiMFa\Module;
use MiMFa\Library\Html;
use MiMFa\Library\Convert;
use MiMFa\Library\Internal;


module("MerchandiseCollection");
/**
 * To show cart items
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class CartCollection extends MerchandiseCollection
{
    public $TitleTag = "h2";
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

    public $AllowBill = true;

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

    public $NextButton = null;
    public $BackButton = null;
    
    public $AddButtonLabel = null;
    public $CartButtonLabel = null;
    public $EmptyHandler = null;

    function __construct()
    {
        parent::__construct();
        if(is_null($this->EmptyHandler))
            $this->EmptyHandler = Html::Container([
                Html::Media("heart-broken", ["style"=>"font-size:20vmin; color: #8888;"]),
                Html::Center("You haven't selected aAll right lobbynything yet!"),
                Html::$Break,
                [Html::Button("Add something...", $this->CollectionRoute, ["class"=>"main be fit"])]
            ], ["class"=>"be align center"]);
    }

    public function GetStyle()
    {
        return Html::Style("
            .{$this->Name} div.item {
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
                " . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->Name} div.item:hover{
                box-shadow: var(--shadow-2);
                border-radius:  var(--radius-1);
                border-color: var(--back-color-special-input);
                background-Color: #88888818;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->Name} div.item.deactive {
                background-Color: #88888844;
                box-shadow: var(--shadow-0);
                border-radius: var(--radius-0);
                border: none;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }

            .{$this->Name} div.item .item-title{
                display: flex;
                align-items: center;
                gap: var(--size-0);
            }
            .{$this->Name} div.item .item-title .divistion{
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
                gap: 0px;
            }
            .{$this->Name} div.item .title{
                font-weight: bold;
                display: inline;
                margin-top: 0px;
                margin-bottom: 0px;
            }
            .{$this->Name} div.item .item-image {
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
                " . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->Name} div.item:hover .item-image{
                opacity: 1;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->Name} .price{
                display: flex;
                align-content: end;
                justify-content: flex-end;
                flex-wrap: wrap;
                column-gap: var(--size-0);
            }
            .{$this->Name} .price .discount{
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
            .{$this->Name} .price .discount .value{
                font-size: calc(var(--size-3) / 2);
                background-color: var(--color-red);
                color: var(--color-white);
                border-radius: var(--radius-2);
                padding: calc(var(--size-0) / 2);
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: center;
            }
            .{$this->Name} div.item .description{
                gap: var(--size-0);
                position: relative;
                overflow-wrap: break-word;
                flex-flow: wrap;
                text-wrap-mode: wrap;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
            }
            .{$this->Name} div.item .description :is(.excerpt, .full){
                padding-inline-end: calc(var(--size-0) / 3);
            }
            
            .{$this->Name} div.item .supplier{
                font-size: calc(var(--size-0) * 0.8);
                display: flex;
                align-content: center;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: flex-start;
                gap: calc(var(--size-0) / 3);
            }
            .{$this->Name} div.item .supplier .image{
                font-size: var(--size-1);
                height: var(--size-1);
                min-height: var(--size-1);
                display:inline-flex;
            }

            .{$this->Name} div.item .footer{
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                    align-content: flex-start;
            }
            .{$this->Name} div.item .detail{
                    font-size: calc(var(--size-0) * 0.8);
                    opacity: 0.8;
            }
            .{$this->Name} div.item .metadata{
                    font-size: calc(var(--size-0) * 0.8);
                    opacity: 0.8;
                    display: flex;
                    flex-wrap: wrap;
                    align-items: flex-start;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-content: flex-start;
            }
            .{$this->Name} div.item .metadata>:not(.supplier){
                padding-inline-end: calc(var(--size-0) / 2);
                display: inline-block;
            }
                
            .{$this->Name} .controls {
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: flex-end;
                flex-direction: row;
            }
            .{$this->Name} .controls * {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: calc(var(--size-0) / 3);
            }
            .{$this->Name} .controls .btns .button {
                aspect-ratio: 1;
                font-size: var(--size-2);
                padding: calc(var(--size-1) / 3);
            }
            .{$this->Name} .controls .numbers {
                min-width: var(--size-5);
                padding: calc(var(--size-1) / 3);
            }

            .{$this->Name} .bill {
                padding: var(--size-1) calc(var(--size-1) / 3);
            }
            .{$this->Name} .bill .final {
                justify-content: center;
            }
        ");
    }

    public function Get($items = null)
    {
        $bill = $this->ComputeBill($items);
        $cartItems = join(PHP_EOL, iterator_to_array((function () use ($bill) {
            $i = 0;
            foreach ($bill["Items"] as $item) {
                if ($meta = getValid($item["Content"], 'MetaData', null)) {
                    $meta = Convert::FromJson($meta);
                    swap($this, $meta);
                }
               
                $r_description = getValid($item["Request"], 'Description', $this->DefaultDescription);
                $r_contact = getValid($item["Request"], 'Contact', \_::$User->GetValue("Contact"));

                $m_discount = get($item["Merchandise"], 'Discount');
                $m_tprice = get($item["Request"], 'Price');//Total Price
                $m_price = get($item["Merchandise"], 'Price');//Price

                $c_id = get($item["Content"], 'Id');
                $c_image = getValid($item["Content"], 'Image', $this->DefaultImage);
                $c_title = getValid($item["Content"], 'Title', $this->DefaultTitle);

                $uid = "cc_$c_id";
                $meta = "";
                if ($this->AllowMetaData) {
                    if ($this->AllowCreateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Html::Span(Convert::ToShownDateTimeString($val), ["class" => 'createtime']);
                            },
                            $item["Content"],
                            'CreateTime'
                        );
                    if ($this->AllowUpdateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Html::Span(Convert::ToShownDateTimeString($val), ["class" => 'updatetime']);
                            },
                            $item["Content"],
                            'UpdateTime'
                        );
                }

                if ($i % $this->MaximumColumns === 0) yield "<div class='row'>";
                yield "<div id='$uid' class='item col col-lg'" . ($this->Animation ? " data-aos-delay='" . ($i % $this->MaximumColumns * \_::$Front->AnimationSpeed) . "' data-aos='{$this->Animation}'" : "") . ">";

                yield Html::Rack(
                    Html::MediumSlot(
                        ($this->AllowImage ? Html::Image($c_title, $c_image, \User::$DefaultImagePath, ["class" => "item-image"]) : "") .
                        Html::Division(
                            ($this->AllowTitle ? Html::SubHeading($c_title, $this->RootRoute . $c_id, ["class" => 'title']) : "").
                            ($this->AllowSupplier ? $this->GetSupplier($item["Content"]) : "")
                        ), ["class" => 'item-title']) .
                    Html::MediumSlot(
                        Html::Division($m_discount?
                            Html::Span($m_discount . "%", null, ["class" => "value"]) . " " .
                            Html::Strike($m_tprice) . " "
                            : "", ["class" => "discount"]) .
                        Html::Bold($m_price . \_::$Config->PriceUnit),
                        ["class" => 'col-md-3 price']
                    )
                );
                if ($this->AllowDescription && $r_description)
                    yield Html::Division(Html::Convert($this->AutoExcerpt?Convert::ToExcerpt($r_description,0,$this->ExcerptLength,$this->ExcerptSign):$r_description), ["class" => 'detail description']);
                if ($this->AllowContact && $r_contact)
                    yield Html::Division([Html::Icon("phone"), $r_contact], ["class" => 'detail contact']);
                yield Html::Rack(
                        (isValid($meta)?Html::MediumSlot($meta, ["class" => 'col-md-8 metadata']):"").
                    ($this->AllowButtons?Html::MediumSlot($this->GetButtons($uid, $item["Content"]), ["class" => 'col-md controls']):"")
                , ["class"=>"footer"]);
                yield "</div>";
                if (++$i % $this->MaximumColumns === 0) yield "</div>";
            }
            if ($i % $this->MaximumColumns !== 0) yield "</div>";
            yield ($this->MoreButtonLabel?Html::$Break.Html::Center(Html::Button($this->MoreButtonLabel, $this->CollectionRoute)):"");
        })()));

        return !$bill["Variety"]? __($this->EmptyHandler, styling:true, referring:true) : Html::Rack(
            ($this->AllowBill?Html::Aside($this->GetBill( $bill), ["class" => "col-lg col-lg-4"]):"") .
            Html::Section(
                $this->GetTitle().
                $this->GetDescription().
                ($this->AllowItems?$cartItems:"").
                $this->GetContent()
                , ["class" => "col-lg"]
            )
        );
    }

    public function ComputeBill($items = null)
    {
        $merchandises = 0;
        $totalCount = 0;
        $totalPrice = 0;
        $priceParams = ["Price" => 0];
        $requests = [];
        foreach (Convert::ToItems($items ?? $this->Items) as $k => $item) {
            $merchandises++;
            $r_count = get($item, 'RequestCount');

            $m_id = get($item, 'MerchandiseId');
            $m_digital = get($item, 'MerchandiseDigital')??\_::$Config->DigitalStore;
            $m_count = get($item, 'MerchandiseCount');
            $m_count = min(get($item, 'MerchandiseLimit')??$m_count, $m_count);
            $r_count = min($r_count, $m_count);
            $m_discount = get($item, 'MerchandiseDiscount');
            $m_priceunit = get($item, 'MerchandisePriceUnit');//Unit Price
            $m_tprice = $r_count * (\_::$Config->StandardPrice)(get($item, 'MerchandisePrice'), $m_priceunit);//Total Price
            $m_price = $m_tprice - $m_discount *  $m_tprice /100;
            $m_metadata = Convert::FromJson(get($item, 'MerchandiseMetaData'));

            $priceParams['Price'] += $m_tprice;
            $totalPrice += (\_::$Config->ComputePrice)($m_tprice, $m_discount, $m_metadata, $m_id, $priceParams);
            $totalCount += $r_count;

            $requests[] = [
                "Request"=>[
                    "Id"=>get($item, 'RequestId'),
                    "Count"=>$r_count,
                    "Price"=>$m_tprice,
                    "Subject"=>get($item, 'RequestSubject'),
                    "Description"=>get($item, 'RequestDescription'),
                    "Contact"=>get($item, 'RequestContact'),
                    "Address"=>get($item, 'RequestAddress')
                ],
                "Merchandise"=>[
                    "Id"=>$m_id,
                    "Count"=>$m_count,
                    "Discount"=>$m_discount,
                    "Price"=>$m_price,
                    "Digital"=>$m_digital,
                    "MetaData"=>$m_metadata
                ],
                "Content"=>$item
            ];
        }
        return [
            "Price"=>$totalPrice,
            "Count"=>$totalCount,
            "Variety"=>$merchandises,
            "Params"=>$priceParams,
            "Items"=>$requests
        ];
    }
    public function GetBill($bill=null)
    {
        $bill = $bill??$this->ComputeBill();
        return Html::Frame([
            [...($bill["Variety"] ? [$bill["Variety"] . \_::$Config->MerchandiseUnit, Html::Span($bill["Count"] . \_::$Config->CountUnit)] : ["", Html::Span($bill["Count"] . \_::$Config->CountUnit)])],
            Html::$BreakLine,
            ...loop($bill["Params"], fn($v,$k) => [Html::Small($k), Html::Small($v . \_::$Config->PriceUnit)]),
            Html::$BreakLine,
            [Html::Label("Total:"), Html::Bold($bill["Price"] . \_::$Config->PriceUnit)],
            Html::$Break,
            $this->NextButton.$this->BackButton
        ], ["class" => "bill be sticky"]);
    }
    public function GetSupplier($item){
        $m_digital = get($item, 'MerchandiseDigital')??\_::$Config->DigitalStore;
        $sup = \_::$Info->Name;
        $del = "";
        if (isValid($item["MerchandiseSupplierId"]) && ($d = table("User")->SelectRow("Id, Organization, Name, Image", "WHERE `Id`=:Id", [":Id" => $item["MerchandiseSupplierId"]])))
        {
            $sup = $d["Organization"] ? $d["Organization"] : ($d["Name"] ? $d["Name"] : "Unknown");
            $del = Html::Image(null, $d["Image"] ? $d["Image"] : \User::$DefaultImagePath) .
                    Html::Link(
                        $sup,
                        \_::$Aseq->UserRoute . $d["Id"]
                    );
        }else $del = Html::Icon(\_::$Info->LogoPath);
        $del .= $this->DeliveryLabel.Html::Icon($m_digital?"envelope":"map-marker").Html::Tooltip($m_digital?"$sup will deliver to your email":"$sup will deliver to your location");
        $r_address = get($item, 'RequestAddress');
        if ($this->AllowAddress && $r_address) $del .= $r_address;
        return Html::Division($del, ["class" => "supplier"]);
    }
    
    public function GetScript(){
        return Html::Script("
            function {$this->Name}_CurrentCount(shownId){
                return parseFloat(document.querySelector(`#\${shownId} .numbers`).innerText)??1;
            }
            function {$this->Name}_CartUpdated(data, err, shownId, count, maxCount){
                d = parseFloat(data?data:0);
                $(`#\${shownId} .numbers`)?.html(d);
                if(d<=0) {
                    document.querySelector(`#\${shownId} .btns`)?.classList.add('hide');
                    document.querySelector(`#\${shownId} .btn.order`)?.classList.remove('hide');
                }
                else{
                    document.querySelector(`#\${shownId} .btn.order`)?.classList.add('hide');
                    document.querySelector(`#\${shownId} .btns`)?.classList.remove('hide');
                    if(d > 1){
                        document.querySelector(`#\${shownId} .btn.decrease`)?.classList.remove('hide');
                    } else {
                        document.querySelector(`#\${shownId} .btn.decrease`)?.classList.add('hide');
                    }
                    if(d < maxCount){
                        document.querySelector(`#\${shownId} .btn.increase`)?.classList.remove('hide');
                    }
                    else {
                        document.querySelector(`#\${shownId} .btn.increase`)?.classList.add('hide');
                    }
                }
                if(!err) reload(location.pathname+'#'+shownId);
            }
        ");
    }
}
?>