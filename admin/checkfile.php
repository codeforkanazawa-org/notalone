<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

	//$data   = $_POST['data'];
	$dir    = $_POST['dir'];
	$ext    = $_POST['ext']; 
	$header = $_POST['header'];

	$filename = $dir . "/" . $header . "." . $ext;

	$result = file_exists ($filename );

	echo $result;

?>
