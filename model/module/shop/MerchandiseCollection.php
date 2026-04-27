<?php
namespace MiMFa\Module\Shop;
use MiMFa\Library\Struct;
use MiMFa\Library\Convert;
use MiMFa\Library\Style;

module("Collection");
/**
 * To show data as merchandises
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class MerchandiseCollection extends \MiMFa\Module\Collection
{
    public string|null $TitleTagName = "h1";
    public $Root = "/shop/item/";
    public $CollectionRoot = "/shop/items/";
    public $CartRoot = "/shop/cart/";
    public $MaximumColumns = 3;

    /**
     * The Width of thumbnail preshow
     * @var string
     */
    public $ImageWidth = "auto";
    /**
     * The Height of thumbnail preshow
     * @var string
     */
    public $ImageHeight = "20vh";

    /**
     * @var bool
     * @category Parts
     */
    public $AllowMetaData = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowSupplier = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowCreateTime = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowUpdateTime = false;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowTitle = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowImage = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowDescription = true;
    /**
     * @var bool
     * @category Parts
     */
    public $AllowAddress = true;
    /**
     * Allow to analyze all text and linking categories and tags to their messages, to improve the website's SEO
     * @var mixed
     */
    public $AutoReferring = true;
    /**
     * Selected Excerpts text automatically
     * @var bool
     * @category Excerption
     */
    public $AutoExcerpt = true;
    /**
     * The length of selected Excerpt text characters
     * @var int
     * @category Excerption
     */
    public $ExcerptLength = 150;
    /**
     * @var string
     * @category Excerption
     */
    public $ExcerptSign = "...";
    /**
     * @var bool
     * @category Parts
     */
    public $AllowButtons = true;
    public $DeliveryLabel = "→";
    /**
     * The label text of Decrease button
     * @var array|string|null
     * @category Management
     */
    public $DecreaseButtonLabel = "<i class='icon fa fa-minus'></i>";
    /**
     * The label text of Increase button
     * @var array|string|null
     * @category Management
     */
    public $IncreaseButtonLabel = "<i class='icon fa fa-plus'></i>";
    /**
     * The label text of Add button
     * @var array|string|null
     * @category Management
     */
    public $AddButtonLabel = "Add to Cart";
    /**
     * The label text of Delete button
     * @var array|string|null
     * @category Management
     */
    public $RemoveButtonLabel = "<i class='icon fa fa-trash-alt'></i>";
    /**
     * The label text of Delete button
     * @var array|string|null
     * @category Management
     */
    public $WishButtonLabel = "<i class='icon fa fa-heart'></i>";
    /**
     * The label text of Cart button
     * @var array|string|null
     * @category Management
     */
    public $CartButtonLabel = "<i class='icon fa fa-shopping-cart'></i>";
    public $NoSupplyLabel = "No supply";

    function __construct()
    {
        parent::__construct();
        if (\_::$Front->Translate->Direction == "rtl")
            $this->DeliveryLabel = "←";
        $this->Root = \_::$Joint->Shop->ItemRootUrlPath;
        $this->CollectionRoot = \_::$Joint->Shop->ItemsRootUrlPath;
        $this->CartRoot = \_::$Joint->Shop->CartRootUrlPath;
        $this->DefaultImage = \_::$Joint->Shop->ItemDefaultImagePath;
        $this->DefaultTitle = \_::$Joint->Shop->ItemDefaultTitle;
        $this->DefaultDescription = \_::$Joint->Shop->ItemDefaultDescription;
    }

    public function GetStyle()
    {
        return Struct::Style("
			.{$this->MainClass} div.item {
				background-Color: #88888808;
				height: fit-attach;
                width: -webkit-fill-available;
				width: fit-content;
                display: flex;
                align-content: space-between;
                flex-wrap: wrap;
                justify-content: space-between;
                flex-direction: column;
                margin: calc(var(--size-1) / 2);
            	padding: var(--size-2);
				font-size: var(--size-0);
				box-shadow: var(--shadow-1);
				border-radius: var(--radius-2);
            	border: var(--border-1) var(--back-color-special-input);
				" . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}
			.{$this->MainClass} div.item:has(.no-supply){
				background-Color: #88888828;
				box-shadow: var(--shadow-0);
            	border-color: var(--back-color);
            }
			.{$this->MainClass} div.item:not(:has(.no-supply)):hover{
				background-Color: #88888802;
				box-shadow: var(--shadow-2);
				border-radius:  var(--radius-1);
				border-color: var(--back-color-special-output);
				" . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}
			.{$this->MainClass} div.item.deactive {
				background-Color: #88888844;
				box-shadow: var(--shadow-0);
				border-radius: var(--radius-0);
            	border: none;
				" . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}

			.{$this->MainClass} div.item .header{
                display: flex;
                align-items: center;
                justify-content: center;
                gap: calc(var(--size-0) / 4);
                overflow: hidden;
			}
			.{$this->MainClass} div.item .title{
                font-weight: bold;
                display: inline;
                margin-top: 0px;
                margin-bottom: 0px;
			}
			.{$this->MainClass} div.item .item-image {
				color: var(--fore-color-output);
                line-height: {$this->ImageWidth};
				width: {$this->ImageWidth};
				height: {$this->ImageHeight};
                margin: 0px;
                padding: 0px;
                overflow: hidden;
                display: inline-flex;
                justify-content: center;
                align-items: center;
				" . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}
                
               .{$this->MainClass} div.item .supplier{
                    font-size: var(--size-0);
                    display: flex;
                    align-content: center;
                    flex-wrap: nowrap;
                    align-items: center;
                    justify-content: flex-start;
                    gap: calc(var(--size-0) / 2);
               }
               .{$this->MainClass} div.item .supplier .image{
                    font-size: var(--size-1);
                    height: var(--size-1);
                    min-height: var(--size-1);
                    display:inline-flex;
               }

			.{$this->MainClass} div.item .metadata{
				font-size: calc(var(--size-0) * 0.8);
                opacity: 0.8;
                line-height: 1;
                margin-bottom: calc(var(--size-0) / 2);
			}
			.{$this->MainClass} div.item .metadata>*{
				padding-inline-end: calc(var(--size-0) / 2);
                display: inline-block;
			}

			.{$this->MainClass} div.item .description{
            	font-size: var(--size-1);
				position: relative;
                overflow-wrap: break-word;
                flex-flow: wrap;
                text-wrap-mode: wrap;
                gap: var(--size-0);
				" . (Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}
			.{$this->MainClass} div.item .description :is(.excerpt, .full){
                padding-inline-end: calc(var(--size-0) / 2);
			}
                
			.{$this->MainClass} div.item .footer{
                display: flex;
                align-items: center;
                row-gap: var(--size-0);
                padding-top: var(--size-0);
                justify-content: space-between;
                flex-wrap: wrap;
                padding-left: 0;
                padding-right: 0;
			}
            .{$this->MainClass} .price{
                display: flex;
                column-gap: var(--size-0);
                justify-content: flex-start;
                align-items: center;
                flex-wrap: wrap;
                padding: 0px;
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
                
            .{$this->MainClass} .controls {
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }
            .{$this->MainClass}-additional{
                position:absolute;
            }
            .{$this->MainClass}-additional .group-name{
                position: absolute;
                right: var(--size-2);
            }
        ") . $this->GetDefaultStyle();
    }
    public function GetDefaultStyle()
    {
        return Struct::Style("
            .{$this->MainClass}-buttons {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: var(--size-0);
                padding-top: calc(var(--size-0) / 2);
                padding-bottom: calc(var(--size-0) / 2);
            }
            .{$this->MainClass}-buttons .button {
                background-color: transparent;
                color: var(--fore-color);
                font-weight: normal;
                border:none !important;
                aspect-ratio: 1;
                font-size: var(--size-2);
                padding: 0px;
               " . Style::UniversalProperty("filter", "drop-shadow(-1px -1px 0px var(--back-color)) drop-shadow(1px 1px 0px var(--back-color)) drop-shadow(1px -1px 0px var(--back-color)) drop-shadow(-1px 1px 0px var(--back-color)) ") . "
            }
            .{$this->MainClass}-buttons .button:not(.main):hover {
                font-weight: bold;
                background-color: transparent;
                box-shadow:none;
                border:none;
               " . Style::UniversalProperty("filter", "drop-shadow(0 0 1px #8888) drop-shadow(0 0 1px #8888) drop-shadow(0 0 1px #8888) drop-shadow(0 0 1px #8888)") . "
            }
            .{$this->MainClass}-buttons .button.main {
                color: var(--back-color-special-output);
               " . Style::UniversalProperty("filter", "drop-shadow(-1px -1px 0px var(--fore-color-special-output)) drop-shadow(1px 1px 0px var(--fore-color-special-output)) drop-shadow(1px -1px 0px var(--fore-color-special-output)) drop-shadow(-1px 1px 0px var(--fore-color-special-output)) ") . "
            }
            .{$this->MainClass}-buttons .button.main:hover {
                font-weight: bold;
                background-color: transparent;
                box-shadow:none;
                border:none;
            }
            .{$this->MainClass}-buttons .numbers {
                padding: 0px;
                min-width: var(--size-1);
                text-align: center;
            }

            .{$this->MainClass}-additional{
                display: flex;
                align-items: center;
                justify-content: center;
                width:fit-content;
                flex-direction: row-reverse;
            }
            .{$this->MainClass}-additional .wish{
                background-color: transparent;
                color: var(--color-gray);
                padding:0px !important;
                margin:0px !important;
                width: fit-content;
                height: fit-content;
                border: none !important;
            }
            .{$this->MainClass}-additional .wish.active{
                color: var(--color-red);
            }
            .{$this->MainClass}-additional:hover .wish{
                background-color: transparent;
                border: none !important;
                box-shadow: none;
               " . Style::UniversalProperty("filter", "drop-shadow(-1px -1px 0px var(--color-red)) drop-shadow(1px 1px 0px var(--color-red)) drop-shadow(1px -1px 0px var(--color-red)) drop-shadow(-1px 1px 0px var(--color-red)) ") . "
            }
            .{$this->MainClass}-additional .wish .icon{
                text-shadow:
                    -2px -2px 0 var(--back-color),
                    2px -2px 0 var(--back-color),
                    -2px 2px 0 var(--back-color),
                    2px 2px 0 var(--back-color) !important;
            }
            .{$this->MainClass}-additional .group-name{
                background-color: var(--back-color);
                color: var(--fore-color);
                padding: 2px calc(var(--size-0) / 4);
                border-radius: var(--radius-2);
                text-wrap-mode: nowrap;
                line-height: 1em;
                display: none;
            }
            .{$this->MainClass}-additional:has(.wish.active) .group-name{
                background-color: var(--color-red);
                color: var(--color-white);
            }
            .{$this->MainClass}-additional:has(.wish.active):hover .group-name{
                display: block;
            }
        ");
    }
    public function GetInner($items = null)
    {
        return join(PHP_EOL, iterator_to_array((function () use ($items) {
            $i = 0;
            yield $this->GetTitle();
            yield $this->GetDescription();
            yield $this->GetContent();
            foreach (Convert::ToItems($items ?? $this->Items) as $k => $item) {
                if ($meta = get($item, 'MetaData')) {
                    $meta = Convert::FromJson($meta);
                    pod($this, $meta);
                }

                $m_id = get($item, 'MerchandiseId');
                $m_discount = \_::$Joint->Shop->BaseDiscount + (get($item, 'MerchandiseDiscount') ?: \_::$Joint->Shop->DefaultDiscount);
                $m_priceunit = get($item, 'MerchandiseCurrency');//Unit Amount
                $m_price = \_::$Joint->Finance->StandardCurrency(get($item, 'MerchandisePrice'), $m_priceunit);//Total Amount
                $m_fprice = $m_price?($m_price - $m_discount * $m_price / 100):$m_price;

                $c_image = get($item, 'MerchandiseImage') ?: get($item, 'Image') ?: $this->DefaultImage;
                $c_title = get($item, 'MerchandiseTitle') ?: get($item, 'Title') ?: $this->DefaultTitle;
                $c_description = get($item, 'MerchandiseDescription') ?: get($item, 'Description') ?: $this->DefaultDescription;

                $shownId = "m_$m_id";
                $meta = null;
                if ($this->AllowMetaData) {
                    if ($this->AllowCreateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Struct::Span(Convert::ToShownDateTimeString($val), ["class" => 'createtime']);
                            },
                            $item,
                            'MerchandiseCreateTime'
                        );
                    if ($this->AllowUpdateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Struct::Span(Convert::ToShownDateTimeString($val), ["class" => 'updatetime']);
                            },
                            $item,
                            'MerchandiseUpdateTime'
                        );
                }

                if ($i % $this->MaximumColumns === 0)
                    yield "<div class='row'>";
                yield "<div id='$shownId' class='item col col-lg'" . ($this->Animation ? " data-aos-delay='" . ($i % $this->MaximumColumns * \_::$Front->AnimationSpeed) . "' data-aos='{$this->Animation}'" : "") . ">";
                yield $this->GetAdditionalButtons($shownId, $item);
                yield Struct::Rack(
                    ($this->AllowImage ? Struct::Image($c_title, $c_image, \_::$User->DefaultImagePath, ["class" => "item-image"]) : "") .
                    Struct::Division(($this->AllowTitle ? Struct::Heading3($c_title, $this->Root . $m_id, ["class" => 'title']) : "") .
                        ($this->AllowSupplier ? $this->GetSupplier($item) : "")) .
                    (isValid($meta) ? Struct::Sub($meta, ["class" => 'metadata']) : "")
                    ,
                    ["class" => 'header']
                );
                if ($this->AllowDescription && $c_description)
                    yield Struct::Division(Struct::Convert($this->AutoExcerpt ? Convert::ToExcerpt($c_description, 0, $this->ExcerptLength, $this->ExcerptSign) : $c_description), ["class" => 'description']);
                yield Struct::Frame(Struct::Row(
                    Struct::MediumSlot(
                        ($m_discount ? Struct::Division(
                            Struct::Span($m_discount . "%", null, ["class" => "value"]) . " " .
                            ($m_price?Struct::Strike($m_price):"") . " ",
                            ["class" => "discount"]
                        )
                            : "") .
                        Struct::Span(\_::$Joint->Finance->AmountStruct($m_fprice, null, ["class" => "be bold"])),
                        ["class" => 'col-md-4 price']
                    ) .
                    ($this->AllowButtons ? Struct::MediumSlot(
                        $this->GetButtons($shownId, $item),
                        ["class" => 'controls']
                    ) : "")
                ), ["class" => "footer"]);
                yield "</div>";
                if (++$i % $this->MaximumColumns === 0)
                    yield "</div>";
            }
            if ($i % $this->MaximumColumns !== 0)
                yield "</div>";
        })()));
    }

    public function GetAdditionalButtons($shownId, $item)
    {
        $m_id = get($item, 'MerchandiseId') ?: 0;
        $r_id = get($item, 'RequestId') ?: 0;
        $r_like = get($item, 'RequestGroup') ? \_::$Joint->Shop->GroupsTitle : false;
        return Struct::Box(
            [
                $this->WishButtonLabel ? Struct::Button($this->WishButtonLabel, "{$this->MainClass}_ToggleWish($m_id, $r_id, '$shownId')", ["class" => "wish" . ($r_like ? " active" : "")]) .
                ($r_like ? Struct::Span(Convert::ToExcerpt($r_like, 0, 25), \_::$Joint->Shop->GroupsUrlPath, ["class" => "group-name"]) : "") : "",
            ],
            ["class" => "{$this->MainClass}-additional"]
        );
    }
    public function GetButtons($shownId, $item)
    {
        $merchandiseId = get($item, 'MerchandiseId' ?? 0);
        $requestId = get($item, 'RequestId') ?? 0;
        $maxCount = get($item, 'MerchandiseCount') ?? 0;
        $maxCount = min(get($item, 'MerchandiseLimit') ?: $maxCount, $maxCount);
        $count = min(get($item, 'RequestCount') ?? 0, $maxCount);
        $controls = $maxCount ? ($this->AddButtonLabel ? Struct::Button($this->AddButtonLabel, "{$this->MainClass}_AddToCart($merchandiseId, $requestId, '$shownId', $count, $maxCount)", ["class" => "main button order" . ($count ? " hide" : "")]) : "") : Struct::Span($this->NoSupplyLabel);
        $controls .= Struct::Box([
            ($this->RemoveButtonLabel ? Struct::Button($this->RemoveButtonLabel, "{$this->MainClass}_RemoveFromCart($merchandiseId, $requestId, '$shownId', $count, $maxCount)", ["class" => "delete"]) : ""),
            ($this->DecreaseButtonLabel ? Struct::Button($this->DecreaseButtonLabel, "{$this->MainClass}_UpdateCart(-1, $merchandiseId, $requestId, '$shownId', $count, $maxCount)", ["class" => "decrease" . ($count > 1 ? "" : " hide")]) : ""),
            Struct::Division($count, ["class" => "numbers"]),
            ($this->IncreaseButtonLabel ? Struct::Button($this->IncreaseButtonLabel, "{$this->MainClass}_UpdateCart(1, $merchandiseId, $requestId, '$shownId', $count, $maxCount)", ["class" => "increase" . ($maxCount > $count ? "" : " hide")]) : ""),
            ($this->CartButtonLabel ? Struct::Button($this->CartButtonLabel, rtrim($this->CartRoot, "\/\\") . "#$shownId", ["class" => "main cart"]) : "")
        ], ["class" => "{$this->MainClass}-buttons" . ($count ? "" : " hide") . ($maxCount ? "" : " no-supply")]);
        $controls .= Convert::By($this->DefaultButtons, $item);
        return $controls;
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
        return Struct::Division($del, ["class" => "supplier"]);
    }
    public function GetScript()
    {
        $cart = rtrim($this->CartRoot, "\/\\");
        return Struct::Script("
            function {$this->MainClass}_ToggleWish(merchandiseId, requestId, shownId){
                if(_(`#\${shownId} .wish`).hasClass('active')) return {$this->MainClass}_RemoveFromWish(merchandiseId, requestId, shownId);
                else return {$this->MainClass}_AddToWish(merchandiseId, requestId, shownId);
            }
            function {$this->MainClass}_AddToWish(merchandiseId, requestId, shownId){
                return sendPut('$cart',{MerchandiseId:merchandiseId, RequestId:requestId, Group:true}, '#'+shownId, (data,err)=>{$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId));
            }
            function {$this->MainClass}_RemoveFromWish(merchandiseId, requestId, shownId){
                return sendDelete('$cart',{MerchandiseId:merchandiseId, RequestId:requestId, Group:false}, '#'+shownId, (data,err)=>{$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId));
            }
            function {$this->MainClass}_AddToCart(merchandiseId, requestId, shownId, count, maxCount){
                return sendPut('$cart',{MerchandiseId:merchandiseId, RequestId:requestId, Request:true}, '#'+shownId, count?()=>load():(data,err)=>{$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId, count, maxCount));
            }
            function {$this->MainClass}_RemoveFromCart(merchandiseId, requestId, shownId, count, maxCount){
                return sendDelete('$cart',{MerchandiseId:merchandiseId, RequestId:requestId}, shownId, (data,err)=>{$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId, count, maxCount));
            }
            function {$this->MainClass}_UpdateCart(change, merchandiseId, requestId, shownId, count, maxCount){
                return sendPatch('$cart', {
                        MerchandiseId:merchandiseId,
                        RequestId:requestId,
                        Count:{$this->MainClass}_CurrentCount(shownId)+change
                    },
                    '#'+shownId,
                    (data,err, xhr)=>{$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId, count, maxCount)
                );
            }

            function {$this->MainClass}_CurrentCount(shownId){
                return parseFloat(_(`#\${shownId} .numbers`).text())??1;
            }
            function {$this->MainClass}_CartUpdated(data, err, merchandiseId, requestId, shownId, count = null, maxCount = null){
                if(err) load();
                if(data === true || data === 'true') return _(`#\${shownId} .wish`).addClass('active');
                else if(data === false || data === 'false') return _(`#\${shownId} .wish`).removeClass('active');
                
                d = parseFloat(data?data:0);
                _(`#\${shownId} .numbers`).html(d);
                if(d<=0) {
                    _(`#\${shownId} .{$this->MainClass}-buttons`).addClass('hide');
                    _(`#\${shownId} .button.order`).removeClass('hide');
                } else {
                    _(`#\${shownId} .button.order`).addClass('hide');
                    _(`#\${shownId} .{$this->MainClass}-buttons`).removeClass('hide');
                    if(d > 1){
                        _(`#\${shownId} .button.decrease`).removeClass('hide');
                    } else {
                        _(`#\${shownId} .button.decrease`).addClass('hide');
                    }
                    if(maxCount !== null){
                        if(d < maxCount){
                            _(`#\${shownId} .button.increase`).removeClass('hide');
                        }
                        else {
                            _(`#\${shownId} .button.increase`).addClass('hide');
                        }
                    }
                }
            }
        ");
    }
}