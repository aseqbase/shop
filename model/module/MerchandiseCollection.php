<?php
namespace MiMFa\Module;
use MiMFa\Library\Html;
use MiMFa\Library\Convert;


module("Collection");
/**
 * To show data as merchandises
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class MerchandiseCollection extends Collection
{
    public $TitleTag = "h1";
    public $Root = "/item/";
    public $CollectionRoot = "/items/";
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
    public $RemoveButtonLabel = "<i class='icon fa fa-trash'></i>";
    /**
    * The label text of Cart button
    * @var array|string|null
    * @category Management
    */
    public $CartButtonLabel = "<i class='icon fa fa-shopping-cart'></i>";

    function __construct()
    {
        parent::__construct();
        if(\_::$Back->Translate->Direction == "rtl") $this->DeliveryLabel = "←";
    }

    public function GetStyle()
    {
        return Html::Style("
			.{$this->Name} div.item {
				height: fit-attach;
                width: -webkit-fill-available;
				width: fit-content;
                display: flex;
                align-content: space-between;
                flex-wrap: wrap;
                justify-content: space-between;
                flex-direction: column;
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

			.{$this->Name} div.item .header{
                display: flex;
                align-items: center;
                justify-content: center;
                gap: calc(var(--size-0) / 4);
                overflow: hidden;
			}
			.{$this->Name} div.item .title{
                font-weight: bold;
                display: inline;
                margin-top: 0px;
                margin-bottom: 0px;
			}
			.{$this->Name} div.item .item-image {
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
				" . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}
                
               .{$this->Name} div.item .supplier{
                    font-size: var(--size-0);
                    display: flex;
                    align-content: center;
                    flex-wrap: nowrap;
                    align-items: center;
                    justify-content: flex-start;
                    gap: calc(var(--size-0) / 2);
               }
               .{$this->Name} div.item .supplier .image{
                    font-size: var(--size-1);
                    height: var(--size-1);
                    min-height: var(--size-1);
                    display:inline-flex;
               }

			.{$this->Name} div.item .metadata{
				font-size: calc(var(--size-0) * 0.8);
                opacity: 0.8;
                line-height: 1;
                margin-bottom: calc(var(--size-0) / 2);
			}
			.{$this->Name} div.item .metadata>*{
				padding-inline-end: calc(var(--size-0) / 2);
                display: inline-block;
			}

			.{$this->Name} div.item .description{
            	font-size: var(--size-1);
				position: relative;
                overflow-wrap: break-word;
                flex-flow: wrap;
                text-wrap-mode: wrap;
                gap: var(--size-0);
				" . (\MiMFa\Library\Style::UniversalProperty("transition", "var(--transition-1)")) . "
			}
			.{$this->Name} div.item .description :is(.excerpt, .full){
                padding-inline-end: calc(var(--size-0) / 2);
			}
                
			.{$this->Name} div.item .footer{
                display: flex;
                align-items: center;
                row-gap: var(--size-0);
                padding-top: var(--size-0);
                justify-content: space-between;
                flex-wrap: wrap;
                padding-left: 0;
                padding-right: 0;
			}
            .{$this->Name} .price{
                display: flex;
                column-gap: var(--size-0);
                justify-content: flex-start;
                align-items: center;
                flex-wrap: wrap;
                padding: 0px;
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
            .{$this->Name} .controls {
                display: flex;
                align-items: center;
                justify-content: flex-end;
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
        ");
    }
    public function Get($items = null)
    {
        return join(PHP_EOL, iterator_to_array((function () use($items) {
            $i = 0;
            yield $this->GetTitle();
            yield $this->GetDescription();
            yield $this->GetContent();
            foreach (Convert::ToItems($items ?? $this->Items) as $k => $item) {
                if ($meta = getValid($item, 'MetaData', null)) {
                    $meta = Convert::FromJson($meta);
                    dip($this, $meta);
                }

                $m_discount = get($item, 'MerchandiseDiscount');
                $m_priceunit = get($item, 'MerchandisePriceUnit');//Unit Price
                $m_price = (\_::$Config->StandardPrice)(get($item, 'MerchandisePrice'), $m_priceunit);//Total Price
                $m_fprice = $m_price - $m_discount *  $m_price /100;

                $c_id = get($item, 'Id');
                $c_image = getValid($item, 'Image', $this->DefaultImage);
                $c_title = getValid($item, 'Title', $this->DefaultTitle);
                $c_description = get($item, 'Description');

                $uid = "cc_$c_id";
                $meta = null;
                if ($this->AllowMetaData) {
                    if ($this->AllowCreateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Html::Span(Convert::ToShownDateTimeString($val), ["class" => 'createtime']);
                            },
                            $item,
                            'MerchandiseCreateTime'
                        );
                    if ($this->AllowUpdateTime)
                        doValid(
                            function ($val) use (&$meta) {
                                if (isValid($val))
                                    $meta .= Html::Span(Convert::ToShownDateTimeString($val), ["class" => 'updatetime']);
                            },
                            $item,
                            'MerchandiseUpdateTime'
                        );
                }

                if ($i % $this->MaximumColumns === 0)
                    yield "<div class='row'>";
                yield "<div id='$uid' class='item col col-lg'" . ($this->Animation ? " data-aos-delay='" . ($i % $this->MaximumColumns * \_::$Front->AnimationSpeed) . "' data-aos='{$this->Animation}'" : "") . ">";
                yield Html::Rack(
                        ($this->AllowImage ? Html::Image($c_title, $c_image, \_::$User->DefaultImagePath, ["class" => "item-image"]) : "") .
                        Html::Division(($this->AllowTitle ? Html::Heading3($c_title, $this->Root . $c_id, ["class" => 'title']) : "").
                        ($this->AllowSupplier ? $this->GetSupplier($item) : "")).
                        (isValid($meta)?Html::Sub($meta, ["class" => 'metadata']):"")
                    , ["class" => 'header']);
                if ($this->AllowDescription && $c_description)
                    yield Html::Division(Html::Convert($this->AutoExcerpt?Convert::ToExcerpt($c_description,0,$this->ExcerptLength,$this->ExcerptSign):$c_description), ["class" => 'description']);
                yield Html::Frame(Html::Row(
                    Html::MediumSlot(
                        ($m_discount?Html::Division(
                            Html::Span($m_discount . "%", null, ["class" => "value"]) . " " .
                            Html::Strike($m_price) . " ", ["class" => "discount"])
                            : "") .
                        Html::Bold($m_fprice . \_::$Config->PriceUnit),
                        ["class" => 'col-md-4 price']
                    ).
                    ($this->AllowButtons?Html::MediumSlot(
                        $this->GetButtons($uid, $item),
                        ["class" => 'controls']):"")
                ), ["class"=>"footer"]);
                yield "</div>";
                if (++$i % $this->MaximumColumns === 0)
                    yield "</div>";
            }
            if ($i % $this->MaximumColumns !== 0)
                yield "</div>";
        })()));
    }
    public function GetButtons($shownId, $item) {
        $merchandiseId = get($item, 'MerchandiseId'??0);
        $requestId = get($item, 'RequestId')??0;
        $maxCount = get($item, 'MerchandiseCount')??0;
        $maxCount = min(get($item, 'MerchandiseLimit')??$maxCount, $maxCount);
        $count = min(get($item, 'RequestCount')??0, $maxCount);
        $countScript = "{$this->Name}_CurrentCount('$shownId')";
        $successScript = "(data,err)=>{$this->Name}_CartUpdated(data, err, '$shownId', $count, $maxCount)";
        $controls = $this->AddButtonLabel?Html::Button($this->AddButtonLabel, "sendPutRequest('/cart',{MerchandiseId:$merchandiseId, Request:true}, '#$shownId', ".($count?"()=>load()":$successScript).")", ["class" => "btn main btn order".($count?" hide":"")]):"";
        $controls .= Html::Division(
            ($this->RemoveButtonLabel?Html::Button($this->RemoveButtonLabel, "sendDeleteRequest('/cart',{MerchandiseId:$merchandiseId, RequestId:$requestId}, '#$shownId', $successScript)", ["class" => "btn delete"]):"").
            ($this->DecreaseButtonLabel?Html::Button($this->DecreaseButtonLabel, "sendPatchRequest('/cart',{MerchandiseId:$merchandiseId, RequestId:$requestId, Count:$countScript-1}, '#$shownId', $successScript)", ["class" => "btn decrease".($count > 1?"":" hide")]):"").
            Html::Division($count, ["class" => "numbers"]).
            ($this->IncreaseButtonLabel?Html::Button($this->IncreaseButtonLabel, "sendPatchRequest('/cart',{MerchandiseId:$merchandiseId, RequestId:$requestId, Count:$countScript+1}, '#$shownId', $successScript)", ["class" => "btn increase".($maxCount > $count?"":" hide")]):"").
            ($this->CartButtonLabel?Html::Button($this->CartButtonLabel, "/cart#$shownId", ["class" => "btn main btn cart"]):"")
        ,["class"=>"btns".($count?"":" hide")]);
        $controls .= Convert::By($this->DefaultButtons, $item);
        return $controls;
    }
    public function GetSupplier($item){
        $m_digital = get($item, 'MerchandiseDigital')??\_::$Config->DigitalStore;
        $sup = \_::$Info->Name;
        $del = "";
        if (isValid($item["MerchandiseSupplierId"]) && ($d = table("User")->SelectRow("Id, Organization, Name, Image", "WHERE `Id`=:Id", [":Id" => $item["MerchandiseSupplierId"]])))
        {
            $sup = $d["Organization"] ? $d["Organization"] : ($d["Name"] ? $d["Name"] : "Unknown");
            $del = Html::Image(null, $d["Image"] ? $d["Image"] : \_::$User->DefaultImagePath) .
                    Html::Link(
                        $sup,
                        \_::$Address->UserRoot . $d["Id"]
                    );
        }else $del = Html::Icon(\_::$Info->LogoPath);
        $del .= $this->DeliveryLabel.Html::Icon($m_digital?"envelope":"map-marker").Html::Tooltip($m_digital?"$sup will deliver to your email":"$sup will deliver to your location");
        return Html::Division($del, ["class" => "supplier"]);
    }
    public function GetScript(){
        return Html::Script("
            function {$this->Name}_CurrentCount(shownId){
                return parseFloat(document.querySelector(`#\${shownId} .numbers`).innerText)??1;
            }
            function {$this->Name}_CartUpdated(data, err, shownId, count, maxCount){
                if(err) load();
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
            }
        ");
    }
}
?>