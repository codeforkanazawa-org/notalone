<?php
include_once("include.php");

header("Content-type: text/html; charset=utf-8");

$_SESSION[$USER_session]  = "";
$_SESSION[$LEVEL_session] = "";

//cookieの書き込み 過去時間の設定
$_COOKIE[$USERid_cookie]    = "";
$_COOKIE[$USERlevel_cookie] = "";

setcookie($USERid_cookie    , ""  , time() - 100 , "/"); 
setcookie($USERlevel_cookie , ""  , time() - 100 , "/"); 

//$return = $_SESSION['CallJob'];
$return = "index.php";

//ログアウト完了時　ReturnFile　へ戻る
header('location:' . $return);

/*
print("ログアウトしました。<br><br>");
//print("<input type='button' value='戻る' onclick='history.go(-1)'>");
print("<form action ='../admin/index.php'>");
print("<input type='submit' value='indexに戻る'/>");
print("</form>");
*/

?>
