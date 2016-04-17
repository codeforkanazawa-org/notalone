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

<link rel="stylesheet" href="../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">

<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/csvdatabase.js"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript" src="../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

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
var whenFieldName = "when";
var whenFieldNo   = DataFieldNo(whenFieldName);
var inputId = "#Mydate";

function setOption(){
	var buff = '<br /><br /><input type="button" onClick="inputSupportOpen()" value="入力補助を表示する"/>';
	
	return buff;
}

function setData(field){
	if(field == 'when'){
		var getdate = $(inputId).val();
		$('#Mydata_' + whenFieldNo).val(getdate);
	}
}

function inputSupportOpen(){
	$('#inputSupport').css('visibility' , 'visible');
}

function inputSupportClose(){
	$('#inputSupport').css('visibility' , 'hidden');
}

//カレンダー（detepicker）による誕生日入力
$(function() {
	var idname = inputId;

	$(idname).datepicker({
		//showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy/MM/dd'
	});

	$(idname).datepicker("option", "showOn", 'button');
	$(idname).datepicker("option", "buttonImageOnly", true);
	$(idname).datepicker("option", "buttonImage", '../images/ico_calendar.png');

	$(idname).datepicker("option", "showButtonPanel", true);

});

//datepicker　日本語化オプション
$(function($){
    $.datepicker.regional['ja'] = {
        closeText: '閉じる',
        prevText: '<前',
        nextText: '次>',
        currentText: '今日',
        monthNames: ['01','02','03','04','05','06',
        '07','08','09','10','11','12'],
        monthNamesShort: ['1月','2月','3月','4月','5月','6月',
        '7月','8月','9月','10月','11月','12月'],
        dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
        dayNamesShort: ['日','月','火','水','木','金','土'],
        dayNamesMin: ['日','月','火','水','木','金','土'],
        weekHeader: '週',
        dateFormat: 'yy/mm/dd',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: '年'};
    $.datepicker.setDefaults($.datepicker.regional['ja']);
});

</script>


<body onload="ShowData()">

<div id="cont_area">
</div>

<div id="inputSupport">
	<input type="text" id="Mydate" size="12" value="" readonly="readonly"><br />
	<input type="button" id="MydateSet" onClick="setData('when')" value="日付をwhenに設定する" />
	<br />
	*where tag1 は検討中<br />
	<input type="button" onClick="inputSupportClose()" value="閉じる" />
</div>

<div id="list">
</div>

</BODY>
</HTML>

<style>
#inputSupport{
	visibility : hidden;

	position : fixed;
	top  : 400px;
	left : 350px;

	padding : 5px;
	border : 3px solid #000000;
	background : lightgreen;

	z-index : 10;
}

</style>

