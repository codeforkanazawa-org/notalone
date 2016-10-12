<?php
include_once("include.php");

$ThisFile   = "cont_target.php";
//$NextFile   = $_SESSION["NextJob"];
//$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

//=============================
$acc_level  = 2;			//アクセスレベル

$ReturnFile = $ThisFile;		//戻り先のファイル名
$_SESSION['CallJob'] = $ThisFile;	//log_in.php　からの戻り用
include("log_in_check.php");
//=============================


//使用するテキストDB
$db_Dir   = "../localhost";
$db_Head = "categoryicon";
$db_Ext  = "csv";
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


common_header("control CategoryIconTable");

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
</ul>
';

//*************

//db内容をjavascript用に読み込む
$DataString = csvDatabaseRead($db_Table,1);
//print($DataString);

?>

<link rel="stylesheet" href="../css/csvdatabase2.css">

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


//**************
// 呼び出し側のｐｈｐファイルで定義
//フィールドの幅確保
var Field_etc = new Array();
Field_etc['no']         = 50;
Field_etc['category']   = 100;
Field_etc['default_directory'] = 150;
Field_etc['default_icon']      = 150;
Field_etc['big_directory']     = 150;
Field_etc['big_icon']          = 150;
Field_etc['zoom_level'] = 80;
Field_etc['memo']       = 300;
//**************


var labelFieldName = "target_label";
var idFieldName    = "target_id";
var colorFieldName = "color";
var labelFieldNo = DataFieldNo(labelFieldName);
var idFieldNo    = DataFieldNo(idFieldName);
var colorFieldNo = DataFieldNo(colorFieldName);

var colorCanvas;		//色コード表

function targetInit(){
	init();
	//ShowData();		//データの読み込みと表示
	//setColorCode();		//色コードのセット
}

function setOption(){
	return "";
	/*
	var option  = "<br /><br /><input type='button' onclick='color_visible()' value='色コード表の表示' />　";

	return option;
	*/
}

function setColorCode(){
	var cc  = colorCode;
	var len = cc.length;

	var buff = "";
	for(var i = 0 ; i < len ; i++){
		buff += "<input type='button' value=" + cc[i] + " style='background-color:" + cc[i] + "'>";
	}

	$('#color_canvas').html(buff);
}

function color_visible(){
	$('#color_area').css('visibility' , 'visible');
}

function color_hidden(){
	$('#color_area').css('visibility' , 'hidden');
}

function colorGetCord(){
	var mPoint = now_marker.getPosition();
	var x = mPoint.lng();	//x : lng
	var y = mPoint.lat();	//y : lat

	if(confirm("この位置を設定しますか？" + x +" , "+y)){
		$('#Mydata_' + latFieldNo).val(y);
		$('#Mydata_' + lngFieldNo).val(x);
	}else{
		return false;
	}
}

var colorCode = ['#000000','#FFFFEE','#EEFFFF','#FFEEFF','#FF0000','#00FF00','#0000FF','#000080','#EEEEEE','#FFFFDD','#DDFFFF','#FFDDFF','#EE0000','#00EE00','#0000EE','#0000FF','#DDDDDD','#FFFFCC','#CCFFFF','#FFCCFF','#DD0000','#00DD00','#0000DD','#008000','#CCCCCC','#FFFFBB','#BBFFFF','#FFBBFF','#CC0000','#00CC00','#0000CC','#008080','#BBBBBB','#FFFFAA','#AAFFFF','#FFAAFF','#BB0000','#00BB00','#0000BB','#00FF00','#AAAAAA','#FFFF99','#99FFFF','#FF99FF','#AA0000','#00AA00','#0000AA','#00FFFF','#999999','#FFFF88','#88FFFF','#FF88FF','#990000','#009900','#000099','#800000','#888888','#FFFF77','#77FFFF','#FF77FF','#880000','#008800','#000088','#800080','#777777','#FFFF66','#66FFFF','#FF66FF','#770000','#000770','#000077','#808000','#666666','#FFFF55','#55FFFF','#FF55FF','#660000','#006600','#000066','#808080','#555555','#FFFF44','#44FFFF','#FF44FF','#550000','#005500','#000055','#C0C0C0','#444444','#FFFF33','#33FFFF','#FF33FF','#440000','#004400','#000044','#FF0000','#333333','#FFFF22','#22FFFF','#FF22FF','#330000','#003300','#000033','#FF00FF','#222222','#FFFF11','#11FFFF','#FF11FF','#220000','#002200','#000022','#FFFF00','#111111','#FFFF00','#00FFFF','#FF00FF','#110000','#001100','#000011','#E6FFE9','#CEF9DC','#F3FFD8','#D7EEFF','#D9E5FF','#EAD9FF','#FFD5EC','#FFDBC9','#CBFFD3','#B1F9D0','#EDFFBE','#C2EEFF','#BAD3FF','#DCC2FF','#FFBEDA','#FFC7AF','#AEFFBD','#9BF9CC','#E9FFA5','#A7F1FF','#A4C6FF','#D0B0FF','#FFABCE','#FFAD90','#93FFAB','#86F9C5','#E4FF8D','#8EF1FF','#8EB8FF','#C299FF','#FF97C2','#FF9872','#78FF94','#77F9C3','#DBFF71','#77EEFF','#75A9FF','#B384FF','#FF82B2','#FF8856','#5BFF7F','#64F9C1','#D6FF58','#60EEFF','#5D99FF','#A16EFF','#FF69A3','#FF773E','#43FF6B','#4DF9B9','#D0FF43','#46EEFF','#4689FF','#9057FF','#FF5192','#FF6928','#2DFF57','#30F9B2','#C9FF2F','#32EEFF','#2C7CFF','#7B3CFF','#FF367F','#FF5F17','#1BFF4A','#17F9AD','#BEFF15','#13EEFF','#136FFF','#6927FF','#FF1A6F','#FF570D','#00FF3B','#00F9A9','#B6FF01','#00ECFF','#005FFF','#5507FF','#FF0461','#FF4F02'];

</script>


<body onload="targetInit();">

<div id="cont_area">
</div>

<div id="list">
</div>

<div id="color_area">
	<div id="color_canvas">
	</div>
	<input type="button" id="color_getcode" onClick="colorGetCode()" value="色コードの取得" />
	　
	<input type="button" id="color_hidden" onClick="color_hidden()" value="色コード表を閉じる" />
</div>


</BODY>
</HTML>

<style>
#color_area{
	visibility : hidden;

	position : fixed;
	top  : 10px;
	left : 200px;

	padding : 5px;
	border : 3px solid #000000;
	background : lightgreen;

	z-index : 10;
}
#color_canvas{
	width : 600px;
	height: 400px;
}

</style>

