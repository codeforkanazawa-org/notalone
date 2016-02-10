<?php
include_once("include.php");

if(!isset($_SESSION[$USER_session])){
	$_SESSION[$USER_session] = "";
}

if($_SESSION[$USER_session] == ""){
	include("log_in.php");
	exit();
}

?>
