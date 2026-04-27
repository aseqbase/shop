<?php
namespace MiMFa\Module\Shop;
use MiMFa\Library\Struct;
use MiMFa\Library\Convert;

module("Content");
/**
 * To show data as posts
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class Merchandise extends \MiMFa\Module\Content
{
     public $Root = "/shop/item/";
     public $CollectionRoot = "/shop/items/";

     public $AllowAuthor = false;

     public $CommentTitle = "Leave Your Idea";
     public $CommentDescription = "How did you find this item?";
     public $CartCollection = null;

     function __construct()
     {
          parent::__construct();
          $this->CommentForm->Access = \_::$User->WriteCommentAccess;
          $this->CommentForm->Title = $this->CommentTitle;
          $this->CommentForm->Description = $this->CommentDescription;
          $this->CommentForm->NameLabel = "Name";
          $this->CommentForm->ContactLabel = "Email";
          $this->CommentForm->SubjectLabel = "Title";
          $this->CommentForm->SubjectPlaceHolder = "The title of your idea";
          $this->CommentForm->MessageLabel = "Description";
          $this->CommentForm->MessagePlaceHolder = "Describe your idea details here...";
          $this->CommentForm->AttachLabel = "Attachment";
          $this->CommentForm->AttachPlaceHolder = "Attach something";
          module("shop/MerchandiseCollection");
          $this->CartCollection = new MerchandiseCollection();
          $this->Root = \_::$Joint->Shop->ItemRootUrlPath;
          $this->CollectionRoot = \_::$Joint->Shop->ItemsRootUrlPath;
          $this->DefaultImage = \_::$Joint->Shop->ItemDefaultImagePath;
          $this->DefaultTitle = \_::$Joint->Shop->ItemDefaultTitle;
          $this->DefaultDescription = \_::$Joint->Shop->ItemDefaultDescription;
     }

     public function GetStyle()
     {
          yield parent::GetStyle();
          yield Struct::Style("
          .{$this->MainClass} h1.heading {
               margin-top: 0px;
          }

          .{$this->MainClass} .controls{
               background-color: var(--back-color-input);
               color: var(--fore-color-input);
               shadow: var(--shadow-1);
               border: var(--border-1) #8888;
               border-radius: var(--radius-1);
               padding: var(--size-0);
               display: flex;
               flex-direction: column;
               justify-content: space-between;
               align-items: stretch;
               height: fit-content;
               position: sticky;
               top: 10px;
          }
          .{$this->MainClass} .controls hr{
               margin: var(--size-4) calc(var(--size-0) / 2);
          }
          .{$this->MainClass} .controls .super{
               color: #8888;
               line-height: 0;
          }
               
          .{$this->CartCollection->MainClass}-additional{
               position: absolute;
               " . (\_::$Front->Translate->Direction === "rtl" ? "left" : "right") . ":var(--size-0);
          }
          .{$this->CartCollection->MainClass}-additional .button{
               color: unset;
          }
               
          .{$this->MainClass} .controls .supplier{
               display: flex;
               align-items: center;
               flex-wrap: wrap;
               gap: var(--size-0);
          }
          .{$this->MainClass} .controls .supplier .division{
               font-size: var(--size-0);
               line-height: 0;
               display: flex;
               align-content: center;
               flex-wrap: nowrap;
               align-items: center;
               justify-content: flex-start;
               gap: calc(var(--size-0) / 2);
          }
          .{$this->MainClass} .controls .supplier .division *{
               font-size: var(--size-0);
               line-height: 0;
          }
          .{$this->MainClass} .controls .supplier .image{
               font-size: var(--size-1);
               height: var(--size-1);
               min-height: var(--size-1);
               display:inline-flex;
          }
          .{$this->MainClass} .controls .price{
               display: flex;
               flex-wrap: nowrap;
               align-items: flex-start;
               justify-content: center;
               flex-direction: column;
               gap: calc(var(--size-0) / 2);
               padding-top: var(--size-0);
               padding-bottom: var(--size-0);
          }
          .{$this->MainClass} .controls .price .discount{
               font-size: var(--size-0);
               line-height: 0;
               display: flex;
               align-content: center;
               flex-wrap: nowrap;
               align-items: center;
               justify-content: flex-start;
               gap: calc(var(--size-0) / 2);
          }
          .{$this->MainClass} .controls .price .discount .value{
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
          .{$this->MainClass} .controls .price>.value{
               line-height: var(--size-0);
          }
          .{$this->MainClass} .controls .count {
               font-size: var(--size-0);
               color: var(--color-yellow);
               padding-top: var(--size-0);
          }
     ") . $this->CartCollection->GetDefaultStyle();
     }
     public function GetScript()
     {
          return $this->CartCollection->GetScript();
     }
     public function GetTitle($attributes = null)
     {
          return null;
     }
     public function GetDescription($attributes = null)
     {
          $p_id = get($this->Item, 'Id');
          $p_name = getBetween($this->Item, 'MerchandiseName', 'Name') ?? $p_id ?? $this->Title;
          $nameOrId = $p_id ?? $p_name;
          if (!$this->CompressPath) {
               $catDir = \_::$Back->Query->GetContentCategoryRoute($this->Item);
               if (isValid($catDir))
                    $nameOrId = trim($catDir, "/\\") . "/" . ($p_name ?? $p_id);
          }
          return Struct::Rack(
               $this->GetImage() .
               Struct::MediumSlot(
                    Struct::Division(
                         ($this->AllowTitle ? Struct::Heading1(getBetween($this->Item, 'MerchandiseTitle', 'Title', $this->Title), $this->LinkedTitle ? $this->Root . $nameOrId : null, ['class' => 'heading']) : "") .
                         $this->GetDetails($this->CollectionRoot . $nameOrId)
                    ) .
                    ($this->AllowDescription ? $this->GetExcerpt() : "")
               ) . $this->GetControls(),
               ["class" => "description"],
               $attributes
          );
     }
     public function GetImage()
     {
          if (!$this->AllowImage)
               return null;
          $p_image = getBetween($this->Item, 'MerchandiseImage', 'Image', $this->Image);
          return isValid($p_image) ? Struct::MediumSlot(Struct::Image(getValid($this->Item, 'Title', $this->Title), $p_image), ["class" => "col-lg-4", "style" => "text-align: center;"]) : "";
     }
     public function GetExcerpt()
     {
          $excerpt = Struct::Convert(getBetween($this->Item, 'MerchandiseDescription', 'Description') ?? (
               $this->AutoExcerpt ? Convert::ToExcerpt(
                    Convert::ToText(getValid($this->Item, 'Content', $this->Content)),
                    0,
                    $this->ExcerptLength,
                    $this->ExcerptSign
               ) : $this->Description));
          return $excerpt ? Struct::MediumSlot(
               __(
                    $excerpt,
                    styling: true,
                    referring: $this->AutoReferring
               )
               ,
               ["class" => "excerpt"]
          ) : null;
     }
     public function GetButtons()
     {
          if (!$this->AllowButtons)
               return null;
          $paths = Convert::FromJson(getValid($this->Item, 'Path', $this->Path));
          $p_morebuttontext = __(Convert::FromSwitch($this->ButtonsLabel, get($this->Item, 'Type')));
          return Struct::Box(
               [
                    $this->GetButtons(),
                    ...loop($paths, function ($v, $k) use ($p_morebuttontext) {
                         return Struct::Button(is_numeric($k) ? $p_morebuttontext : $k, $v, ["class" => "button outline"]);
                    })
               ],
               attributes: ["class" => "buttons view md-hide"]
          );
     }
     public function GetControls()
     {
          $shownId = "m_" . $this->Item["MerchandiseId"];
          $output = $this->CartCollection->GetAdditionalButtons($shownId, $this->Item);
          if (isValid($this->Item["MerchandiseSupplierId"])) {
               $d = table("User")->SelectRow("Id, Organization, Name, Image", "WHERE `Id`=:Id", [":Id" => $this->Item["MerchandiseSupplierId"]]);
               if (isset($d["Id"]))
                    $output .= Struct::Division(
                         Struct::Super("Supplier:", attributes:["class"=>"be small"]) .
                         Struct::Division(
                              Struct::Image(null, $d["Image"] ?? \_::$User->DefaultImagePath) .
                              Struct::Link(
                                   $d["Organization"] ?? $d["Name"] ?? "Unknown",
                                   \_::$Address->UserRootUrlPath . $d["Id"]
                              )
                         ),
                         ["class" => "supplier"]
                    );
               $output .= Struct::$BreakLine;
          }
          $discount = \_::$Joint->Shop->BaseDiscount + (get($this->Item, 'MerchandiseDiscount') ?: \_::$Joint->Shop->DefaultDiscount);
          $price = $this->Item["MerchandisePrice"];
          $price = \_::$Joint->Finance->StandardCurrency($price, $this->Item["MerchandiseCurrency"]?: \_::$Joint->Finance->Currency);
          $fprice = $price?($price - $discount * $price / 100):$price;
          $output .= Struct::Division(
               ($discount ? Struct::Super(Struct::Division($discount . "%", ["class" => "value"]) . 
               ($price?Struct::Strike($price):""), ["class" => "discount"]) : "") .
               Struct::Division(\_::$Joint->Finance->AmountStruct($fprice, null, ["class"=>"be bold"]), ["class" => "value"]),
               ["class" => "price"]
          );
          $maxCount = $this->Item["MerchandiseCount"] ?? 0;
          if (\_::$Joint->Shop->CriticalSupply >= $maxCount)
               $output .= Struct::Division(
                    $maxCount . ($this->Item["MerchandiseUnit"] ?? \_::$Joint->Shop->ItemsUnit) . " remained",
                    ["class" => "count"]
               );
          $output .= $this->CartCollection->GetButtons($shownId, $this->Item);
          $output .= Convert::By($this->DefaultButtons, $item);
          return Struct::LargeSlot($output, ["id" => $shownId, "class" => "controls col-lg-3"]);
     }
}