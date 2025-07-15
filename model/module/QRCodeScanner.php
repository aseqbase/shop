<?php namespace MiMFa\Module;
use MiMFa\Library\Local;
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Library\Script;

/**
 * A Real-time webcam-driven HTML5 QR code scanner module.
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class QRCodeScanner extends Module{
	public $Tag = "video";
	public $CameraIndex = 0;
	/**
	 * The target JS function name
	 */
	public $TargetScriptFunction = null;
	public $TargetId = null;
	public $TargetSelector = null;
	public $ActiveAtBegining = true;
	public $ActiveAtEnding = true;
	public $CamerasNotFoundError = "No cameras found.";

	
	public function __construct(){
		parent::__construct();
	}

	public function Get(){
		//\Res::Script(null, \_::$Address->ScriptRoute . "Instascan.js");
		\Res::Script(null, "https://rawgit.com/schmich/instascan-builds/master/instascan.min.js");
		return Html::Script($this->ActiveAtBegining?$this->ActiveScript():$this->DeactiveScript());
	}
	public function Toggle(){
		\Res::Script($this->ToggleScript());
	}
	public function ToggleScript(){
		return "qrscanner = document.querySelector('.{$this->Name}');
		if(qrscanner.style.display =='none') {
			".$this->ActiveScript()."
		}
		else {
			".$this->DeactiveScript()."
		}";
	}

	public function Active(){
		\Res::Script($this->ActiveScript());
	}
	public function ActiveScript(){
		return "
			document.querySelector('.{$this->Name}').style.display = null;
			try{
				if(!Instascan.Scanner) Html.script.load(null, '" . asset(\_::$Address->ScriptDirectory, "Instascan.js", optimize: true) . "');
			}catch{Html.script.load(null, '" . asset(\_::$Address->ScriptDirectory, "Instascan.js", optimize: true) . "');}
			let scanner = new Instascan.Scanner({ video: document.querySelector('.{$this->Name}') });
			scanner.addListener('scan', function (content) {
				".($this->TargetScriptFunction?"{$this->TargetScriptFunction}(content);":"")."
				".($this->TargetId?"document.getElementById(".Script::Convert($this->TargetId).").value = content;":"")."
				".($this->TargetSelector?"document.querySelector(".Script::Convert($this->TargetSelector).").value = content;":"")."
				".($this->ActiveAtEnding?"":$this->DeactiveScript())."
			});
			Instascan.Camera.getCameras().then(function (cameras) {
				if (cameras.length >= {$this->CameraIndex}) scanner.start(cameras[{$this->CameraIndex}]);
				else console.error(".Script::Convert($this->CamerasNotFoundError).");
			}).catch(function (e) {
				console.error(e);
			});
		";
	}
	
	public function Deactive(){
		\Res::Script($this->DeactiveScript());
	}
	public function DeactiveScript(){
		return "document.querySelector('.{$this->Name}').style.display = 'none';";
	}
}
?>
