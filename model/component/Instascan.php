<?php
use \MiMFa\Library\Html;
\_::$Front->Libraries = [
	...\_::$Front->Libraries,
	Html::Script(null, 'https://rawgit.com/schmich/instascan-builds/master/instascan.min.js'),
	Html::Script("
			try{
				if(!Instascan.Scanner)Html.script.load(null, '" . asset(\_::$Address->ScriptDirectory, "Instascan.js", optimize:true) . "');
			}catch{Html.script.load(null, '" . asset(\_::$Address->ScriptDirectory, "Instascan.js", optimize:true) . "');}
	")
];
?>