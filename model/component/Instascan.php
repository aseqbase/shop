<?php
use \MiMFa\Library\Struct;
\_::$Front->Libraries = [
	...\_::$Front->Libraries,
	Struct::Script(null, 'https://rawgit.com/schmich/instascan-builds/master/instascan.min.js'),
	Struct::Script("
			try{
				if(!Instascan.Scanner)Struct.script.load(null, '" . asset(\_::$Address->ScriptDirectory, "Instascan.js", optimize:true) . "');
			}catch{Struct.script.load(null, '" . asset(\_::$Address->ScriptDirectory, "Instascan.js", optimize:true) . "');}
	")
];
?>