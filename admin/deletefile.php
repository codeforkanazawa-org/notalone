<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

$dir   = $_POST['dir'];
$fname = $_POST['fname'];

$path  = $dir . "/" . $fname;

unlink($path); 

echo $path;

?>