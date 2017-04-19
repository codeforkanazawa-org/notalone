<?php
include_once("include.php");

header("Content-type: text/html; charset=utf-8");

$_SESSION[$USER_session] = "";
$_SESSION[$LEVEL_session] = "";

//cookieの書き込み 過去時間の設定
$_COOKIE[$USERid_cookie]    = "";
$_COOKIE[$USERlevel_cookie] = "";

setcookie($USERid_cookie    , ""  , time() - 100 , "/"); 
setcookie($USERlevel_cookie , ""  , time() - 100 , "/"); 



?>
