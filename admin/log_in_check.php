<?php
include_once("include.php");

if($_SESSION[$USER_session] == ""){
	include("log_in.php");
	exit();
}

?>
