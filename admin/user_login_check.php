<?php
header("Content-type: text/html; charset=utf-8");

include_once("include.php");

//$_SESSION[$USER_session] = "";
//$_SESSION[$USER_level] = "";


if(isset($_COOKIE[$USERid_cookie])){
	$userid    = $_COOKIE[$USERid_cookie];
}else{
	$userid    = "";
}

if(isset($_COOKIE[$USERlevel_cookie])){
	$userlevel = $_COOKIE[$USERlevel_cookie];
}else{
	$userlevel = "";
}

print($userid . "," . $userlevel);

?>
