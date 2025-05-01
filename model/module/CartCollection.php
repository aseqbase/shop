<?php
namespace MiMFa\Module;
use MiMFa\Library\Html;
use MiMFa\Library\Convert;
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

    /**
     * @var bool
     * @category Parts
     */
    public $ShowAddress = true;

    public $ShowItems = true;

    public $NextButton = null;
    public $BackButton = null;
    
    public $AddButtonLabel = null;
    public $CartButtonLabel = null;

    function __construct()
    {
        parent::__construct();
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
                border: var(--border-1) var(--back-color-5);
                " . (\MiMFa\Library\Style::UniversalProperty("transition", \_::$Front->Transition(1))) . "
            }
            .{$this->Name} div.item:hover{
                box-shadow: var(--shadow-2);
                border-radius:  var(--radius-1);
                border-color: var(--back-color-4);
                background-Color: #88888818;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", \_::$Front->Transition(1))) . "
            }
            .{$this->Name} div.item.deactive {
                background-Color: #88888844;
                box-shadow: var(--shadow-0);
                border-radius: var(--radius-0);
                border: none;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", \_::$Front->Transition(1))) . "
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
                background-color: var(--back-color-2);
                color: var(--fore-color-2);
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
                " . (\MiMFa\Library\Style::UniversalProperty("transition", \_::$Front->Transition(1))) . "
            }
            .{$this->Name} div.item:hover .item-image{
                opacity: 1;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", \_::$Front->Transition(1))) . "
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
                background-color: var(--color-1);
                color: var(--color-7);
                border-radius: var(--radius-2);
                padding: calc(var(--size-0) / 2);
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: center;
            }
            .{$this->Name} div.item .description{
                gap: var(--size-0);
                font-size: var(--size-1);
                position: relative;
                overflow-wrap: break-word;
                flex-flow: wrap;
                text-wrap-mode: wrap;
                " . (\MiMFa\Library\Style::UniversalProperty("transition", \_::$Front->Transition(1))) . "
            }
            .{$this->Name} div.item .description :is(.excerpt, .full){
                padding-inline-end: calc(var(--size-0) / 3);
            }
            .{$this->Name} div.item .address{
                font-size: var(--size-1);
                text-align: justify;
            }
            
            .{$this->Name} div.item .supplier{
                font-size: var(--size-0);
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

            .{$this->Name} .cart {
                padding: var(--size-1) calc(var(--size-1) / 3);
            }
            .{$this->Name} .cart .final {
                justify-content: center;
            }
        ");
    }

    public function Get($items = null)
    {
        $merchandises = 0;
        $totalCount = 0;
        $totalCount = 0;
        $totalPrice = 0;
        $priceParams = ["Price" => 0];
        $cartItems = join(PHP_EOL, iterator_to_array((function () use ($items, &$merchandises, &$totalPrice, &$totalCount, &$priceParams) {
            $i = 0;
            foreach (Convert::ToItems($items ?? $this->Items) as $k => $item) {
                $merchandises++;
                if ($meta = getValid($item, 'MetaData', null)) {
                    $meta = Convert::FromJson($meta);
                    swap($this, $meta);
                }

                $r_id = get($item, 'RequestId');
                $r_count = get($item, 'RequestCount');
                $r_description = getValid($item, 'RequestDescription', $this->DefaultDescription);
                $r_address = getValid($item, 'RequestAddress', \_::$Back->User->GetValue("Address"));

                $m_id = get($item, 'MerchandiseId');
                $m_count = get($item, 'MerchandiseCount');
                $r_count = min($r_count, $m_count);
                $m_discount = get($item, 'MerchandiseDiscount');
                $m_priceunit = get($item, 'MerchandisePriceUnit');//Unit Price
                $m_tprice = $r_count * (\_::$Config->StandardPrice)(get($item, 'MerchandisePrice'), $m_priceunit);//Total Price
                $m_price = $m_tprice - $m_discount *  $m_tprice /100;
                $m_metadata = Convert::FromJson(get($item, 'MerchandiseMetaData'));

                $c_id = get($item, 'Id');
                $c_image = getValid($item, 'Image', $this->DefaultImage);
                $c_title = getValid($item, 'Title', $this->DefaultTitle);

                $priceParams['Price'] += $m_tprice;
                $totalPrice += (\_::$Config->ComputePrice)($m_tprice, $m_discount, $m_metadata, $m_id, $priceParams);
                $totalCount += $r_count;

                $uid = "cc_$c_id";
                $meta = "";
                if ($this->ShowMetaData) {
                    if ($this->ShowCreateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Html::Span(Convert::ToShownDateTimeString($val), ["class" => 'createtime']);
                            },
                            $item,
                            'CreateTime'
                        );
                    if ($this->ShowUpdateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Html::Span(Convert::ToShownDateTimeString($val), ["class" => 'updatetime']);
                            },
                            $item,
                            'UpdateTime'
                        );
                }

                if ($i % $this->MaximumColumns === 0)
                    yield "<div class='row'>";
                yield "<div id='$uid' class='item col col-lg'" . ($this->Animation ? " data-aos-delay='" . ($i % $this->MaximumColumns * \_::$Front->AnimationSpeed) . "' data-aos='{$this->Animation}'" : "") . ">";

                yield Html::Rack(
                    Html::MediumSlot(
                        ($this->ShowImage ? Html::Image($c_title, $c_image, \MiMFa\Library\User::$DefaultImagePath, ["class" => "item-image"]) : "") .
                        Html::Division(
                            ($this->ShowTitle ? Html::SubHeading($c_title, $this->RootRoute . $c_id, ["class" => 'title']) : "").
                            ($this->ShowSupplier ? $this->GetSupplier($item) : "")
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
                if ($this->ShowDescription && $r_description)
                    yield Html::Division(Html::Convert($this->AutoExcerpt?Convert::ToExcerpt($r_description,0,$this->ExcerptLength,$this->ExcerptSign):$r_description), ["class" => 'description']);
                if ($this->ShowAddress && $r_address)
                    yield Html::Division([Html::Icon("map-marker"), $r_address], ["class" => 'address']);
                yield Html::Rack(
                        (isValid($meta)?Html::MediumSlot($meta, ["class" => 'col-md-8 metadata']):"").
                    ($this->ShowButtons?Html::MediumSlot($this->GetButtons($uid, $r_count, $m_count, $m_id??0, $r_id??0), ["class" => 'col-md controls']):"")
                , ["class"=>"footer"]);
                yield "</div>";
                if (++$i % $this->MaximumColumns === 0)
                    yield "</div>";
            }
            if ($i % $this->MaximumColumns !== 0)
                yield "</div>";
            yield ($this->MoreButtonLabel?Html::$NewLine.Html::Center(Html::Button($this->MoreButtonLabel, $this->CollectionRoute)):"");
        })()));

        return Html::Rack(
            Html::LargeSlot($this->GetCart($totalPrice, $totalCount, $priceParams, $merchandises), ["class" => "col-lg-4"]) .
            Html::LargeSlot(
                $this->GetTitle().
                $this->GetDescription().
                ($this->ShowItems?$cartItems:"").
                $this->GetContent()
            )
        );
    }
    public function GetCart($totalPrice, $totalCount, $priceParams = [], $merchandises = null)
    {
        return Html::Frame([
            [Html::Label("Numbers:"), ($merchandises ? $merchandises . \_::$Config->MerchandiseUnit . " (" . Html::Span($totalCount . \_::$Config->CountUnit) . ")" : Html::Span($totalCount . \_::$Config->CountUnit))],
            Html::$HorizontalBreak,
            ...loop($priceParams, fn($k, $v) => [Html::Small($k), Html::Small($v . \_::$Config->PriceUnit)]),
            Html::$HorizontalBreak,
            [Html::Label("Total:"), Html::Bold($totalPrice . \_::$Config->PriceUnit)],
            Html::$NewLine,
            $this->NextButton.$this->BackButton
        ], ["class" => "cart be sticky"]);
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
                if(!err) load(location.pathname+'#'+shownId);
            }
        ");
    }
}
?>