<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

$dir   = $_POST['dir'];
$fname = $_POST['fname'];
$rname = $_POST['rname'];

$fpath  = $dir . "/" . $fname;
$rpath  = $dir . "/" . $rname;

if ( file_exists( $rpath )) {
	echo "exists";
	return;
}

$result = rename($fpath , $rpath); 

if($result){
	echo $rname;
}else{
	//false
	echo $result;
}

?>