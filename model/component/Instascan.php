<?php
use \MiMFa\Library\Html;
use \MiMFa\Library\Local;
\_::$Front->Libraries = [
	...\_::$Front->Libraries,
	Html::Script(null, 'https://rawgit.com/schmich/instascan-builds/master/instascan.min.js'),
	Html::Script("
			try{
				if(!Instascan.Scanner)Html.script.load(null, '" . Local::OptimizeUrl(\_::$Address->ScriptRoute . "Instascan.js") . "');
			}catch{Html.script.load(null, '" . Local::OptimizeUrl(\_::$Address->ScriptRoute . "Instascan.js") . "');}
	")
];
?>