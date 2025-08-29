<?php
namespace MiMFa\Module;
use MiMFa\Library\Html;
use MiMFa\Library\Style;
use MiMFa\Library\Convert;
use MiMFa\Library\User;
module("Content");
/**
 * To show data as posts
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class Merchandise extends Content
{
     public $RootRoute = "/item/";
     public $CollectionRoute = "/items/";

     public $AllowAuthor = false;
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

     public $CommentTitle = "Leave Your Idea";
     public $CommentDescription = "How did you find this item?";

     function __construct()
     {
          parent::__construct();
          $this->CommentForm->Access = \_::$Config->WriteCommentAccess;
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
     }

     public function GetStyle()
     {
          return parent::GetStyle() . Html::Style("
               .{$this->Name} .controls{
                    background-color: var(--back-color-input);
                    color: var(--fore-color-input);
                    shadow: var(--shadow-1);
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
               .{$this->Name} .controls .buttons{
                    margin-bottom: var(--size-0);
               }
               .{$this->Name} .controls hr{
                    margin: var(--size-4) calc(var(--size-0) / 2);
               }
               .{$this->Name} .controls .super{
                    color: #8888;
                    line-height: 0;
               }
               .{$this->Name} .controls .supplier .division{
                    font-size: var(--size-0);
                    line-height: 0;
                    display: flex;
                    align-content: center;
                    flex-wrap: nowrap;
                    align-items: center;
                    justify-content: flex-start;
                    gap: calc(var(--size-0) / 2);
               }
               .{$this->Name} .controls .supplier .division *{
                    font-size: var(--size-0);
                    line-height: 0;
               }
               .{$this->Name} .controls .supplier .image{
                    font-size: var(--size-1);
                    height: var(--size-1);
                    min-height: var(--size-1);
                    display:inline-flex;
               }
               .{$this->Name} .controls .price .discount{
                    font-size: var(--size-0);
                    line-height: 0;
                    display: flex;
                    align-content: center;
                    flex-wrap: nowrap;
                    align-items: center;
                    justify-content: flex-start;
                    gap: calc(var(--size-0) / 2);
               }
               .{$this->Name} .controls .price .discount .value{
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
               .{$this->Name} .controls .price>.value{
                    line-height: var(--size-0);
               }
               .{$this->Name} .controls .count {
                    font-size: calc(var(--size-3) / 2);
                    color: var(--color-yellow);
                    padding-top: var(--size-0);
               }

               .{$this->Name} .controls .btn.order {
                    font-size: var(--size-3);
                    width: 100%;
               }

               .{$this->Name} .controls .btns {
                    display: flex;
                    justify-content: center;
                    align-items: center;
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
     public function GetScript()
     {
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
     public function GetTitle($attributes = null)
     {
          return null;
     }
     public function GetDescription($attributes = null)
     {
          $p_id = get( $this->Item, 'Id');
          $p_name = getValid( $this->Item, 'Name') ?? $p_id ?? $this->Title;
          $nameOrId = $p_id ?? $p_name;
          if (!$this->CompressPath) {
               $catDir = \_::$Back->Query->GetContentCategoryRoute( $this->Item);
               if (isValid($catDir))
                    $nameOrId = trim($catDir, "/\\") . "/" . ($p_name ?? $p_id);
          }
          return Html::Rack(
               $this->GetImage() .
               Html::MediumSlot(
                    Html::Division(
                         ($this->AllowTitle ? Html::ExternalHeading(getValid($this->Item, 'Title', $this->Title), $this->LinkedTitle ? $this->RootRoute . $nameOrId : null, ['class' => 'heading']) : "") .
                         $this->GetDetails($this->CollectionRoute . $nameOrId)
                    ) .
                    ($this->AllowDescription ? $this->GetExcerpt() : "")
               ) . $this->GetControls(),
               ["class" => "description"], $attributes
          );
     }
     public function GetImage()
     {
          if (!$this->AllowImage)
               return null;
          $p_image = getValid($this->Item, 'Image', $this->Image);
          return isValid($p_image) ? Html::MediumSlot(Html::Image(getValid($this->Item, 'Title', $this->Title), $p_image), ["class" => "col-lg-4", "style" => "text-align: center;"]) : "";
     }
     public function GetButtons()
     {
          if (!$this->AllowButtons)
               return null;
          $paths = Convert::FromJson(getValid($this->Item, 'Path', $this->Path));
          $p_morebuttontext = __(Convert::FromSwitch($this->ButtonsLabel, get($this->Item, 'Type')));
          return Html::Division(
               loop($paths, function ($v,$k) use ($p_morebuttontext) {
                    return Html::Button(is_numeric($k) ? $p_morebuttontext : $k, $v, ["class" => "btn outline"]);
               }),
               attributes: ["class" => "buttons view md-hide"]
          );
     }
     public function GetControls()
     {
          module("CartCollection");
          $merchandiseId = $this->Item["MerchandiseId"];
          $shownId = "cc_" . $this->Item["Id"];
          $output = $this->GetButtons();
          if (isValid($this->Item["MerchandiseSupplierId"])) {
               $d = table("User")->SelectRow("Id, Organization, Name, Image", "WHERE `Id`=:Id", [":Id" => $this->Item["MerchandiseSupplierId"]]);
               if(isset($d["Id"])) $output .= Html::Division(
                    Html::Super("Supplier") .
                    Html::Division(
                         Html::Image(null, $d["Image"]??User::$DefaultImagePath) .
                         Html::Link(
                              $d["Organization"]??$d["Name"]??"Unknown",
                              \_::$Aseq->UserRoute . $d["Id"]
                         )
                    ),
                    ["class" => "supplier"]
               );
               $output .= Html::$BreakLine;
          }
          $discount = $this->Item["MerchandiseDiscount"] ?? 0;
          $priceUnit = $this->Item["MerchandisePriceUnit"] ?? \_::$Config->PriceUnit;
          $price = $this->Item["MerchandisePrice"];
          if ($price) {
               $price = (\_::$Config->StandardPrice)($price, $priceUnit);
               $fprice = $price - $discount * $price / 100;
               $output .= Html::Division(
                    ($fprice != $price ? Html::Super(Html::Division($discount . "%", ["class" => "value"]) . Html::Strike($price), ["class" => "discount"]) : "") .
                    Html::Division($fprice . $priceUnit, ["class" => "value"]),
                    ["class" => "price"]
               );
          }
          $maxCount = $this->Item["MerchandiseCount"]??0;
          if (\_::$Config->MinimumSupply ?? $maxCount >= $maxCount)
               $output .= Html::Division(
                    $maxCount . ($this->Item["MerchandiseCountUnit"] ?? \_::$Config->CountUnit) . " remained",
                    ["class" => "count"]
               );
          $count = $this->Item["RequestCount"]??0;
          $countScript = "{$this->Name}_CurrentCount('$shownId')";
          $successScript = "(data,err)=>{$this->Name}_CartUpdated(data, err, '$shownId', $count, $maxCount)";
          $output .= $this->AddButtonLabel ? Html::Button($this->AddButtonLabel, "sendPut('/cart',{MerchandiseId:$merchandiseId,Request:true}, '#$shownId', $successScript)", ["class" => "btn main btn order" . ($count ? " hide" : "")]) : "";
          $output .= Html::Division(
               ($this->RemoveButtonLabel ? Html::Button($this->RemoveButtonLabel, "sendDelete('/cart',{MerchandiseId:$merchandiseId}, '#$shownId', $successScript)", ["class" => "btn delete"]) : "") .
               ($this->DecreaseButtonLabel ? Html::Button($this->DecreaseButtonLabel, "sendPatch('/cart',{MerchandiseId:$merchandiseId, Count:$countScript-1}, '#$shownId', $successScript)", ["class" => "btn decrease" . ($count > 1 ? "" : " hide")]) : "") .
               Html::Division($count, ["class" => "numbers"]) .
               ($this->IncreaseButtonLabel ? Html::Button($this->IncreaseButtonLabel, "sendPatch('/cart',{MerchandiseId:$merchandiseId, Count:$countScript+1}, '#$shownId', $successScript)", ["class" => "btn increase" . ($maxCount > $count ? "" : " hide")]) : "") .
               ($this->CartButtonLabel ? Html::Button($this->CartButtonLabel, "/cart#$shownId", ["class" => "btn main btn cart"]) : "")
               ,
               ["class" => "btns" . ($count ? "" : " hide")]
          );
          $output .= Convert::By($this->DefaultButtons, $item);

          return Html::LargeSlot($output, ["id" => $shownId, "class" => "controls col-lg-3"]);
     }
}
?>