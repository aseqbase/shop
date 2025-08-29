<?php
namespace MiMFa\Module;
use MiMFa\Library\Html;
use MiMFa\Library\Convert;
use MiMFa\Library\Contact;
use MiMFa\Library\Script;
module("Form");
module("QRCodeBox");
module("QRCodeScanner");
library("SpecialCrypt");
class PaymentForm extends Form
{
	/**
	 * Leave null for dynamic key
	 * @var string
	 */
	public $ValidationRequest = "Code";
	public $ValidationKey = null;
	/**
	 * Leave null for dynamic code
	 * @var string
	 */
	public $ValidationCode = null;
	public $ValidationTimeout = 600000;
	public $QRCodeBox = null;
	public $QRCodeScanner = null;
	public $ExternalLink = true;
	public $Types = array();
	public Transaction $Transaction;
	public $Contact = null;
	public $SubmitLabel = "Pay";
	public $SuccessSubject = "Transaction";
	public $SuccessContent = "<h1>A Successful Transaction</h1>";
	public $BlockTimeout = 0;
	public $ResponseView = null;
	public $BackLabel = null;
	public $Method = "POST";

	/**
	 * New payment form
	 * @param array<Transaction> $types
	 */
	public function __construct(Transaction ...$transactions)
	{
		parent::__construct();
		$this->UseAjax = false;
		$this->QRCodeBox = new QRCodeBox();
		$this->QRCodeBox->Height = "30vmin";
		$this->QRCodeScanner = new QRCodeScanner();
		$this->QRCodeScanner->ActiveAtBegining = false;
		$this->QRCodeScanner->ActiveAtEnding = false;
		$this->QRCodeScanner->Style = "border: var(--border-1) #8888; margin-top: var(--size-0);";
		$this->Path = \_::$Url;
		$this->CancelLabel = "Cancel";
		$this->SetTypes(...$transactions);
	}

	public function SetTypes(Transaction ...$transactions)
	{
		$this->Types = [];
		foreach ($transactions as $v)
			$this->Types[$v->DestinationPath] = $v;
		$data = receiveGet();
		if (count($data) == 1)
			$data = Convert::FromJson(decrypt(array_key_first($data)));

		if (count($data) > 0)
			$this->Transaction = seek($transactions, fn($v) => $v->Unit == get($data, "Unit")) ??
				new Transaction(
					description: get($data, "Description"),
					path: get($data, "DestinationPath"),
					content: get($data, "DestinationContent"),
					value: get($data, "Value"),
					unit: get($data, "Unit"),
					network: get($data, "Network")
				);
		else
			$this->Transaction = first($transactions) ?? new Transaction();

		$this->Transaction->Relation = get($data, "Relation")??$this->Transaction->Relation;
		$this->Transaction->Transaction = get($data, "Transaction")??$this->Transaction->Transaction;
		
		$this->Transaction->Source = get($data, "Source") ?? $this->Transaction->Source ?? (is_null(\_::$Back->User) ? null : \_::$Back->User->Name);
		$this->Transaction->SourceContent = get($data, "Content");
		$this->Transaction->SourcePath = get($data, "Path");
		$this->Transaction->SourceEmail = get($data, "Email") ?? (is_null(\_::$Back->User) ? null : \_::$Back->User->Email);
		$this->Transaction->Others = $this->Contact = get($data, "Contact") ?? (is_null(\_::$Back->User) ? null : \_::$Back->User->GetValue("Contact"));

		$this->Transaction->Value = between($this->Transaction->Value, get($data, "Value"));
		$this->Transaction->Unit = between($this->Transaction->Unit, get($data, "Unit"));
		$this->Transaction->Identifier = between($this->Transaction->Identifier, get($data, "Identifier"));
		$this->Transaction->Network = between($this->Transaction->Network, get($data, "Network"));
		$this->Transaction->Description = between($this->Transaction->Description, get($data, "Description"));

		$this->Transaction->SuccessPath = between($this->Transaction->SuccessPath, get($data, "SuccessPath"));
		$this->CancelPath =
		$this->Transaction->FailPath = between($this->Transaction->FailPath, get($data, "FailPath"));

		$this->Title = $this->Transaction->Title;
		$this->Description = $this->Transaction->Description;
		if (!is_null($this->Transaction->Value))
			$this->Transaction->MaximumValue = $this->Transaction->MinimumValue = $this->Transaction->Value;
	}

	public function GetStyle()
	{
		return parent::GetStyle() . Html::Style("
			.{$this->Name} .header .details{
				background-color: var(--back-color-input);
				padding: var(--size-5);
				margin: var(--size-0);
			}
			.{$this->Name} .header .details .path{
				margin: var(--size-0);
				overflow-wrap: break-word;
			}
			.{$this->Name} .header .details .path .link{
				font-size: var(--size-0);
			}
			.{$this->Name} .input{
				background-color: var(--back-color);
				margin: 0px var(--size-0);
			}
		");
	}
	public function GetFields()
	{
		$trans = $this->Transaction;
		$id = "_" . getId();
		module("Field");
		$module = new Field();
		yield ($module->Set(
			type: "text",
			key: "SourceContent",
			title: "Source",
			value: $trans->SourceContent ?? $trans->Source,
			required: !is_null($trans->SourceContent),
			lock: !is_null($trans->SourceContent)
		)
		)->ToString();
		yield (
			$module->Set(
				type: "float",
				key: "Value",
				value: $trans->Value ?? 0,
				description: $trans->Unit,
				options: null,
				attributes: is_null($trans->Value) ? [...(is_null($trans->MaximumValue) ? [] : ["max" => $trans->MaximumValue]), ...(is_null($trans->MinimumValue) ? [] : ["min" => $trans->MinimumValue])] : ["min" => $trans->Value, "max" => $trans->Value],
				required: true,
				lock: !is_null($trans->Value)
			)
		)->ToString();
		$this->QRCodeScanner->TargetSelector = ".{$this->Name} input[name='Transaction']";
		yield $this->QRCodeScanner->Handle();
		yield (
			$module->Set(
				type: "text",
				key: "Transaction",
				value: $trans->Transaction,
				description: Html::Button(Html::Icon("qrcode"), $this->QRCodeScanner->ToggleScript()) . Html::Center($trans->Network, ["style" => "width:max-content;"]),
				options: null,
				attributes: null,
				required: true,
				lock: !is_null($trans->Transaction)
			)
		)->ToString();
		yield Html::$Break;
		yield Html::BreakLine("More", "document.getElementById('$id').style.display = document.getElementById('$id').computedStyleMap().get('display') == 'none'?'inherit':'none';");
		yield Html::$Break;
		yield Html::Division(
			Html::Rack(
				Html::SmallSlot(
					$module->Set(
						type: "text",
						key: "Source",
						title: "From",
						value: $trans->Source,
						required: !is_null($trans->Source),
						lock: !is_null($trans->Source)
					)
						->ToString()
				) .
				Html::SmallSlot(
					$module->Set(
						type: "text",
						key: "Destination",
						title: "To",
						value: $trans->Destination,
						required: false,
						lock: !is_null($trans->Destination)
					)
						->ToString()
				)
			) .
			$module->Set(
				type: "text",
				key: "Identifier",
				value: $trans->Identifier,
				required: !is_null($trans->Identifier),
				lock: !is_null($trans->Identifier)
			)
				->ToString() .
			$module->Set(
				type: "Email",
				key: "Email",
				value: $trans->SourceEmail,
				required: !is_null($trans->SourceEmail),
				lock: !is_null($trans->SourceEmail)
			)
				->ToString() .
			$module->Set(
				type: "text",
				key: "Contact",
				value: $this->Contact,
				required: !is_null($this->Contact),
				lock: !is_null($this->Contact)
			)
				->ToString() .
			Html::HiddenInput(
				key: $this->ValidationRequest,
				value: \_::$Back->Cryptograph->Encrypt($this->ValidationCode ?? join("|", [randomString(), getClientCode(), randomString(), microtime(true) * 1000, randomString()]), $this->ValidationKey, true),
				attributes: ["Required"]
			)
			,
			["Id" => $id, "style" => "display: none;"]
		);
		yield from parent::GetFields();
	}
	public function GetDescription($attrs = null)
	{
		if ($this->ExternalLink && $this->QRCodeBox != null && isValid($this->Transaction->DestinationPath ?? $this->Transaction->DestinationContent)) {
			$this->QRCodeBox->Content = $this->Transaction->DestinationPath ?? $this->Transaction->DestinationContent;
			$content = $this->Transaction->DestinationContent ?? $this->Transaction->DestinationPath;
			module("TimeCounter");
			$counter = new TimeCounter($this->ValidationTimeout / 1000, 0, $this->Transaction->FailPath);
			return Html::Center(__($this->Description) . Html::Big($counter->ToString())) . Html::Center(
				$this->QRCodeBox->ToString() .
				Html::Division(
					($this->Transaction->DestinationPath?Html::Link($content, $this->Transaction->DestinationPath):$content) . " " .
					Html::Panel(Html::Icon("copy", "copy('$content');") .
						Html::Tooltip("Copy to clipboard"))
					,
					["class" => "path"]
				)
				,
				["class" => "details"]
			);
		} else
			return parent::GetDescription($attrs);
	}
	public function GetSuccess($msg = null, ...$attr)
	{
		module("TimeCounter");
		$counter = new TimeCounter(5, 0, $this->Transaction->SuccessPath."?Id=".urlencode($this->Transaction->Id));
		$counter->Router->Get()->Switch();
		$counter->Description = "Refer to complete the process";
		$counter->Class = "button btn main";
		$counter->Tag = "button";
        if (!isScript($counter->Action) && isUrl($counter->Action))
			$counter["onclick"] = "load(".Script::Convert($counter->Action).")";
		else $counter["onclick"] = $counter->Action;
		$doc = Html::Document(__($this->SuccessContent) . $this->Transaction->ToHtml());
		$res = [Html::Success($msg)];
		$res[] = Html::$Break;
		$res[] = Html::Success("Your transaction recorded successfully", $attr);
		if (isValid($this->Transaction->DestinationEmail))
			if (Contact::SendHtmlEmail(\_::$Info->SenderEmail, $this->Transaction->DestinationEmail, __($this->SuccessSubject) . " - " . $this->Transaction->Relation, $doc, $this->Transaction->SourceEmail, $this->Transaction->DestinationEmail == \_::$Info->ReceiverEmail ? null : \_::$Info->ReceiverEmail))
				$res[] = Html::Success("Your transaction received", $attr);
			else
				$res[] = Html::Warning("We could not receive your transaction details, please notify us!", $attr);
		if (isValid($this->Transaction->SourceEmail))
			if (Contact::SendHtmlEmail(\_::$Info->SenderEmail, $this->Transaction->SourceEmail, __($this->SuccessSubject) . " - " . $this->Transaction->Relation, $doc, $this->Transaction->DestinationEmail))
				$res[] = Html::Success("A notification to '{$this->Transaction->SourceEmail}' has been sent!", $attr);
			else
				$res[] = Html::Warning("Could not send a notification to '{$this->Transaction->SourceEmail}'!", $attr);
		return Html::Center($this->Transaction->ToHtml() . $counter->ToString() . Html::$Break . join(Html::$Break,$res), ...$attr);
	}
	public function GetError($msg = null, ...$attr)
	{
		module("TimeCounter");
		$counter = new TimeCounter(3, 0, $this->Transaction->FailPath);
		$counter->Router->Get()->Switch();
		$counter->Description = "Refer to the previous page";
		$counter->Class = "button btn main";
		$counter->Tag = "button";
        if (!isScript($counter->Action) && isUrl($counter->Action))
			$counter["onclick"] = "load(".Script::Convert($counter->Action).")";
		else $counter["onclick"] = $counter->Action;
		return Html::Center(parent::GetError($msg, ...$attr). Html::$Break . $counter->ToString());
	}

	public function Put()
	{
		return null;
	}

	public function File()
	{
		return null;
	}

	public function Patch()
	{
		return null;
	}

	public function Delete()
	{
		return null;
	}

	public function Handler($received = null)
	{
		render(Html::Page(Html::Container(function () use ($received) {
			if ($code = get($received, $this->ValidationRequest))
				try {
					$code = \_::$Back->Cryptograph->Decrypt($code, $this->ValidationKey, true);
					if (is_null($this->ValidationCode)) {
						$arr = preg_split("/\|/", $code);
						if (isEmpty($arr[1]))
							return self::GetError("Your connection is not valid!");
						if ($arr[1] !== getClientCode())
							return self::GetError("Your connection is not secure!");
						$code = floatval($arr[3]);
						if (microtime(true) * 1000 - $this->ValidationTimeout > $code)
							return self::GetError("Your time is out!");
						elseif (microtime(true) * 1000 < $code)
							return self::GetError("A problem is occured!");
					} elseif ($this->ValidationCode === $code)
						return self::GetError("Your request is manipulated!");

					$this->Transaction->Relation = get($received, "Relation")??$this->Transaction->Relation;
					$this->Transaction->Transaction = get($received, "Transaction")??$this->Transaction->Transaction;
					
					$this->Transaction->Value = between($this->Transaction->Value, get($received, "Value"));
					$this->Transaction->Unit = between($this->Transaction->Unit, get($received, "Unit"));
					$this->Transaction->Identifier = between($this->Transaction->Identifier, get($received, "Identifier"));
					$this->Transaction->Network = between($this->Transaction->Network, get($received, "Network"));
					$this->Transaction->Description = between($this->Transaction->Description, get($received, "Description"));

					$this->Transaction->Source = between($this->Transaction->Source, get($received, "Source"));
					$this->Transaction->SourceContent = between($this->Transaction->SourceContent, get($received, "SourceContent"));
					$this->Transaction->SourceEmail = between($this->Transaction->SourceEmail, get($received, "SourceEmail"));
					$this->Transaction->SourcePath = between($this->Transaction->SourcePath, get($received, "SourcePath"));

					$this->Transaction->Destination = between($this->Transaction->Destination, get($received, "Destination"));
					$this->Transaction->DestinationContent = between($this->Transaction->DestinationContent, get($received, "DestinationContent"));
					$this->Transaction->DestinationEmail = between($this->Transaction->DestinationEmail, get($received, "DestinationEmail"));
					$this->Transaction->DestinationPath = between($this->Transaction->DestinationPath, get($received, "DestinationPath"));

					$this->Transaction->SuccessPath = between($this->Transaction->SuccessPath, get($received, "SuccessPath"));
					$this->Transaction->FailPath = between($this->Transaction->FailPath, get($received, "FailPath"));

					if (table("Payment")->SelectValue("Id", "Transaction=:Transaction", [":Transaction" => $this->Transaction->Transaction]))
						return self::GetError("You can not submit this transaction, again!");
					//Process
					if (
						table("Payment")->Insert([
							"Transaction" => $this->Transaction->Transaction,
							"Relation" => $this->Transaction->Relation,
							"Source" => $this->Transaction->Source,
							"SourceContent" => $this->Transaction->SourceContent,
							"SourcePath" => $this->Transaction->SourcePath,
							"SourceEmail" => $this->Transaction->SourceEmail,
							"Value" => $this->Transaction->Value,
							"Verify" => Convert::By($this->Transaction->Verify, $this->Transaction)?1:0,
							"Unit" => $this->Transaction->Unit,
							"Network" => $this->Transaction->Network,
							"Identifier" => $this->Transaction->Identifier,
							"Destination" => $this->Transaction->Destination,
							"DestinationContent" => $this->Transaction->DestinationContent,
							"Destinationpath" => $this->Transaction->DestinationPath,
							"DestinationEmail" => $this->Transaction->DestinationEmail,
							"Others" => $this->Transaction->Others
						])
					) {
						$tr = table("Payment")->SelectLastRow();
						$this->Transaction->Id = $tr["Id"];
						$this->Transaction->DateTime = Convert::ToDateTime($tr["CreateDate"]);
						if(!compute("request/update-all", ["Collection" => $this->Transaction->Relation]))
							return self::GetError("Your transaction doned successfully! But it is not sat on your card! Please let us to check this problem");
						return self::GetSuccess("Transaction done successfully!");
					} else return self::GetError("We could not record your transaction details, please notify us!");
				} catch (\Exception $ex) {
					return self::GetError("Error in transaction!") . Html::Error($ex);
				}
			return self::GetError("Fault in transaction!");
		})));
	}
}

class Transaction
{
	public $Id = null;
	public $Relation = null;
	public $Title = null;
	public $Description = null;

	/**
	 * The client|source name
	 * @var string|null
	 */
	public $Source = null;
	/**
	 * The client|source email
	 * @var string|null
	 */
	public $SourceEmail = null;
	/**
	 * Shown payment Source address
	 * @var string|null
	 */
	public $SourceContent = null;
	/**
	 * Payment Source path
	 * @var string|null
	 */
	public $SourcePath = null;

	/**
	 * The host|destination name
	 * @var string|null
	 */
	public $Destination = null;
	/**
	 * The host|destination email
	 * @var string|null
	 */
	public $DestinationEmail = null;
	/**
	 * Shown payment address
	 * @var string|null
	 */
	public $DestinationContent = null;
	/**
	 * Payment path
	 * @var string|null
	 */
	public $DestinationPath = null;

	/**
	 * The value of payment
	 * @var float|null
	 */
	public $Value = null;
	public $Rate = 1;
	public $MinimumValue = null;
	public $MaximumValue = null;
	/**
	 * The payment Unit
	 * @example "USDT"
	 * @var string|null
	 */
	public $Unit = null;
	/**
	 * Selected network to transfer
	 * @example "TRC-20"
	 * @var string|null
	 */
	public $Network = null;
	/**
	 * Transaction reference
	 * @var string|null
	 */
	public $Transaction = null;
	/**
	 * The transaction identifier
	 * @var string|null
	 */
	public $Identifier = null;
	public $DateTime = null;

	public $Verify = null;

	public $Others = null;

	public $SuccessPath = null;
	public $FailPath = null;

	public function __construct($title = null, $description = null, $path = null, $value = null, $unit = null, $network = null, $identifier = null, $content = null, $source = null, $destination = null, $destinationEmail = null, $rate = 1, $transaction = null, $verify = null)
	{
		$this->Relation = "_".rand(0,999999999)."_".first(preg_split("/\./", microtime(true)));
		$this->Transaction = $transaction;
		$this->Title = $title ?? \_::$Info->FullName;
		$this->Description = $description;
		$this->Value = $value;
		$this->Unit = $unit;
		$this->Network = $network;
		$this->Identifier = $identifier;
		$this->Source = $source;
		$this->Destination = $destination ?? \_::$Info->FullOwner;
		$this->DestinationPath = $path;
		$this->DestinationContent = $content;
		$this->DestinationEmail = $destinationEmail ?? \_::$Info->ReceiverEmail;
		$this->Rate = $rate;
		$this->Verify = $verify;
	}

	public function ToHtml()
	{
		return Html::Heading(__("Transaction" . ($this->DateTime ? " Succeed" : " Failed")), ["class" => "result" . ($this->DateTime ? " success" : " error")]) .
			Html::Table([
				[__("Traction Number") . ":", Html::Bold($this->Relation).Html::Icon("copy", "copy(".Script::Convert($this->Relation).")")],
				[__("From") . ":", "{$this->Source} {$this->SourceEmail} {$this->SourceContent}"],
				[__("To") . ":", "{$this->Destination} {$this->DestinationEmail} {$this->DestinationContent}"],
				[__("Value") . ":", $this->Value . $this->Unit],
				[__("Network") . ":", $this->Network],
				[__("Transaction") . ":", $this->Transaction.Html::Icon("copy", "copy(".Script::Convert($this->Transaction).")")],
				[__("Identifier") . ":", $this->Identifier],
				[__("Time") . ":", Convert::ToShownDateTimeString($this->DateTime)],
				[__("Others") . ":", $this->Others]
			], ["RowHeaders" => [], "ColHeaders" => []]) .
			Html::Button(Html::Big(Html::Icon("print")), "window.print()");
	}
}
?>