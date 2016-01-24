<?php

// ダウンロードさせるファイル名
if(isset($_GET['dir'])){
	$dir  = $_GET['dir'];
}else{
	exit();
}

if(isset($_GET['file'])){
	$file = $_GET['file'];
}else{
	exit();
}

if(isset($_GET['enc'])){
	$enc  = $_GET['enc'];
}else{
	$enc  = "utf8";
}

$path = $dir . "/" . $file;

if($enc == "sjis"){
	$string = mb_convert_encoding(file_get_contents($path),"SJIS","UTF-8");
}else{
	$string = file_get_contents($path);
}

header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=$file");

echo $string;

exit();

?>