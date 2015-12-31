<?php
include_once("include.php");

$ThisFile   = "cont_event.php";
//$NextFile   = $_SESSION["NextJob"];
//$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

//=============================
$acc_level  = 1;			//アクセスレベル

$ReturnFile = $ThisFile;		//戻り先のファイル名
//$_SESSION['CallJob'] = $ThisFile;	//log_in.php　からの戻り用
$_SESSION['CallJob'] = $ThisFile . "?" . $_SERVER["QUERY_STRING"];	//log_in.php　からの戻り用
include("log_in_check.php");
//=============================


//使用するテキストDB
if(isset($_GET['dir'])){
	$db_Dir   = "../" . trim($_GET['dir']);
}else{
	$db_Dir   = "../uploads/events";
}
if(isset($_GET['fname'])){
	$db_filename = trim($_GET['fname']);
	$files = explode("." , $db_filename);
	$db_Head = $files[0];
	$db_Ext  = $files[1];
}else{
	$db_Head = "201512";
	$db_Ext  = "csv";
}
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


common_header("control EventTable");

$user_level = Access_check( $acc_level ,1,1,$ReturnFile);
print('レベル　＝　1:一般ユーザ　2:管理ユーザ　3:システム管理者<br>');

//ファイルの存在確認
if(!file_exists($db_Table)){
	print("データベースファイルがありません");
	exit();
}

//一般ユーザーで他者のファイルにアクセスする場合は、読み出し専用（閲覧）
//javascript ReadOnly とセット
$id = $_SESSION[$USER_session];
$id_length   = strlen($id);
$head_length = strlen($db_Head);
$myfile = FALSE;
if($id_length <= $head_length){
	if(substr($db_Head,0,$id_length) == $id){
		$myfile = TRUE;
	}
}

$ReadOnly = 'false';
if($user_level == 1){
	if($myfile == FALSE){
		$ReadOnly = 'true';
	}
}
//***********************

//*************
if($ReadOnly == 'true'){
	echo 'あなたのファイルではありません（閲覧のみ可能）';
}else{
	echo '
	<ul>
	<li>,（カンマ）"（ダブルクオーテーション）\'（シングルクオーテーション）は使用できません</li>
	<li>データを更新する場合は、必ずファイルに保存してください</li>
	</ul>
	';
}

//*************
print("<h3>【ファイル名：" . $db_Table . "】</h3>");

//db内容をjavascript用に読み込む
$DataString    = csvDatabaseRead($db_Table,1);
//print($DataString);
  

?>

<link rel="stylesheet" href="../css/csvdatabase.css">

<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/csvdatabase.js"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript">

//DataShow Mode Check

<?php
//ReadOnly mode
print("var ReadOnly = " . $ReadOnly . ";" ); 
?>

//DataBase 宣言 ***** php側でデータ読み込み

<?php 
//datadir
print("var DataDir   ='" . $db_Dir  . "';" );
?>
<?php
print("var DataHead  ='" . $db_Head . "';" );
?>
<?php 
print("var DataExt   ='" . $db_Ext  . "';" );
?>
<?php 
//datatable
print("var DataTable ='" . $db_Table . "';" );
?>

<?php
//配列情報の設定（イベントファイル）
print("var DataArray    =" . $DataString  );
?>

//**************
function setOption(){
	return "";
}


/*  使用例
var passFieldName = "user_pw";
var passFieldNo = DataFieldNo(passFieldName);

function setOption(){
	var option = "<br /><br /><input type='button' onclick='setCode()' value='パスワードの暗号化' />";
	option += "　";
	option += "<br /><input type='button' value='パスワードの生成' onclick='makePass()' />";
	option += "<br /><input type='text'   name='keta' id='keta' size='2' value='8' />桁";
	option += "<input type='hidden' name='kazu' id='kazu' size='1' value='1' />";
	option += "<input type='checkbox' name='suuji' id='suuji' checked />数字";
	option += "<input type='checkbox' name='small' id='small' checked />英語小文字";
	option += "<input type='checkbox' name='big'   id='big' />英語大文字";

	return option;
}

function setCode(){
	var emt  = $('#Mydata_' + passFieldNo);
	var pass = emt.val();

	if(pass == ""){
		alert("パスワードが入力されていません");
		return;
	}

	if(confirm(pass + "　パスワードを暗号化します")){
		emt.val(SHA256(pass));		
	}
}

function makePass(){
  	//エラーフラグ
  	err = "off";

  	keta = chgMessHalf($('#keta').val());
  	kazu = chgMessHalf($('#kazu').val());

  	//文字定義
  	moji = "";
  	if($('#suuji').prop('checked')){
    		moji += "0123456789";
  	}
  	if($('#small').prop('checked')){
    		moji += "abcdefghijklmnopqrstuvwxyz";
  	}
  	if($('#big').prop('checked')){
    		moji += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  	}
  	if(err == "off"){
    		pass = "";
    		//パスワード生成
    		for(i=0; i< kazu; i++){
      			for(j=0; j< keta; j++){
        			num = Math.floor(Math.random() * moji.length);
       				pass += moji.charAt(num);
      			}
      			pass += "\n";
    		}
		var emt  = $('#Mydata_' + passFieldNo);
		emt.val(pass);

 	} else {
    		alert("数字を入力してください。");
  	}//end makePass
}

//make random password
//半角数字変換用文字定義
var half = "0123456789";
var full = "０１２３４５６７８９";
function chgMessHalf(VAL){

  	messIn = VAL;
  	messOut = "";

  	for(i=0; i<messIn.length; i++){
    		oneStr = messIn.charAt(i);
    		num = full.indexOf(oneStr,0);
    		oneStr = num >= 0 ? half.charAt(num) : oneStr;
    		messOut += oneStr;
  	}

  	//数字か空かチェック
  	if(isNaN(messOut) || messOut==""){
    		err = "on";
  	}

  	return messOut;
}
*/
</script>


<body onload="ShowData()">

<div id="cont_area">
</div>

<div id="list">
</div>

</BODY>
</HTML>
