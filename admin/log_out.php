<?php
header("Content-type: text/html; charset=utf-8");

include_once("include.php");

$_SESSION[$USER_session] = "";
$_SESSION[$USER_level] = "";


print("ログオフしました。<br><br>");
print("<input type='button' value='戻る' onclick='history.go(-1)'>");

?>
