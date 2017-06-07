<?php
include_once("include.php");

$ThisFile   = "cont_user2.php";
//$NextFile   = $_SESSION["NextJob"];
//$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

//=============================
$acc_level  = 3;			//アクセスレベル

$ReturnFile = $ThisFile;		//戻り先のファイル名
$_SESSION['CallJob'] = $ThisFile;	//log_in.php　からの戻り用
include("log_in_check.php");
//=============================


//使用するテキストDB
$db_Dir  = "../localhost";
$db_Head = "user";
$db_Ext  = "csv";
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


common_header("control UserTable");

$user_level = Access_check( $acc_level ,1,1,$ReturnFile);
print('レベル　＝　1:一般ユーザ　2:管理ユーザ　3:システム管理者<br>');

//ファイルの存在確認
if(!file_exists($db_Table)){
	print("データベースファイルがありません");
	exit();
}

//*************
echo '
<ul>
<li>,（カンマ）"（ダブルクオーテーション）\'（シングルクオーテーション）は使用できません</li>
<li>データを更新する場合は、必ずファイルに保存してください</li>
<li>パスワードは暗号化しないとログインできません</li>
</ul>
';

//*************

//db内容をjavascript用に読み込む
$DataString = csvDatabaseRead($db_Table,1);
//print($DataString);

?>

<?php //<link rel="stylesheet" href="../css/csvdatabase2.css"> ?>

<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/csvdatabase2.js"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript">

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
//配列情報の設定
print("var DataArray =" . $DataString  );
?>

//*************
// 呼び出し側のｐｈｐファイルで定義
//フィールドの幅確保
var Field_etc = new Array();
Field_etc['no']          = 50;
Field_etc['active']      = 50;
Field_etc['user_id']     = 150;
Field_etc['user_pw']     = 300;
Field_etc['user_level']  = 80;
Field_etc['real_name']   = 150;
Field_etc['e_mail']      = 150;
Field_etc['login_count'] = 100;
Field_etc['last_login']  = 150;
//**************

//**************

var passFieldName = "user_pw";
var passFieldNo = DataFieldNo(passFieldName);

function setOption(){
	var option = "<div class='optbox btns' style='display:none;'><input type='button' onclick='setCode()' value='パスワードの暗号化' />";
	option += "　";
	option += "<br /><input type='button' value='パスワードの生成' onclick='makePass()' />";
	option += "<br /><input type='text'   name='keta' id='keta' size='2' value='8' />桁";
	option += "<input type='hidden' name='kazu' id='kazu' size='1' value='1' />";
	option += "<input type='checkbox' name='suuji' id='suuji' checked />数字";
	option += "<input type='checkbox' name='small' id='small' checked />英語小文字";
	option += "<input type='checkbox' name='big'   id='big' />英語大文字";
	option += "</div>";

	//return option;
	return "";
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

</script>


<!--body onload="ShowData()"-->
<?php //<body onload="init()">  ?>
<script> window.onload = function() { init(); } </script>

<div id="cont_area">
</div>

<div id="list">
</div>

<?php include_once 'include_footer.php'; ?>
