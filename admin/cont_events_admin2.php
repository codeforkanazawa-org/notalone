<?php
include_once("include.php");

$ThisFile   = "cont_events_admin2.php";
//$NextFile   = $_SESSION["NextJob"];
//$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

//=============================
$acc_level  = 3;			//アクセスレベル

$ReturnFile = $ThisFile;		//戻り先のファイル名
//$_SESSION['CallJob'] = $ThisFile;	//log_in.php　からの戻り用
$_SESSION['CallJob'] = $ThisFile . "?" . $_SERVER["QUERY_STRING"];	//log_in.php　からの戻り用
include("log_in_check.php");
//=============================


//使用するテキストDB
if(isset($_GET['dir'])){
	$db_Dir   = "../" . trim($_GET['dir']);
}else{
	$db_Dir   = "../events";
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

common_header("control EventsAdminTable");

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

	//***** Data Check ******
	echo '
	<input type="button" onClick="DataCheck()" value="イベントデータの簡易チェック">
	<div id="errorArea" style="position:fixed; top:180px; left:10px;">
		<textarea id="errorResult" rows="10" cols="100" style="background:lightyellow;">
		</textarea>
		<input type="button" onClick="errorAreaClose()" value="閉じる">
	</div>
	';	
	//***********************


//*************
print("<h3>【ファイル名：" . $db_Table . "】</h3>");

//db内容をjavascript用に読み込む
$DataString = csvDatabaseRead($db_Table,1);
//print($DataString);

?>

<link rel="stylesheet" href="../css/csvdatabase2.css">

<link rel="stylesheet" href="../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">

<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/csvdatabase2.js"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript" src="../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

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
Field_etc['no']           = 50;
Field_etc['eventtitle'] = 200;
Field_etc['where']      = 150;
Field_etc['whom']       = 150;
Field_etc['what']       = 300;
Field_etc['who']        = 200;
Field_etc['contact']    = 150;
Field_etc['fee']        = 50;
Field_etc['openTime']   = 80;
Field_etc['closeTime']  = 80;
Field_etc['tag1']       = 100;
Field_etc['url']        = 150;
//


//**************
var whenFieldName = "when";
var whenFieldNo   = DataFieldNo(whenFieldName);
var inputId = "#Mydate";

function setOption(){
	var buff = '　<input type="button" onClick="inputSupportOpen()" value="入力補助を表示する"/>';
	
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



//イベントデータの簡易チェック
//check field ***************
var souceField = "soucefile";
var titleField = "eventtitle";

var whereField = "where";
var whenField  = "when";
var openField  = "openTime";
var closeField = "closeTime";
var tagField   = "tag1";

var tagId   = "target_id";
var tagName = "target_label";
var locId   = "location_name";

var CR = "\n"; 
//***************************

function DataCheck(){

	var buff ="";
	var fieldNo;
	var eventdate;
	var mdata;
	var data;
	var msg;

	//配列情報の設定（イベントファイル）
	//0 はフィールド名、1 からスタート
	for(var i = 1; i < DataArray.length ; i++){
		msg = "";

		//*****
		fieldNo = DataFieldNo(whenField);
		mdata = DataArray[i][fieldNo];
		eventdate = mdata;
		data = DataArray[i][fieldNo].split("/");
		if(data.length != 3){
			msg += whenField + "(" + mdata + ")のデータエラー　/　";
		}else{
			if(data[0].length != 4){
				msg += whenField + "(" + data[0] + ")年の桁数エラー /　";
			}
			if(data[1].length != 2){
				msg += whenField + "(" + data[1] + ")月の桁数エラー /　";
			}
			if(data[2].length != 2){
				msg += whenField + "(" + data[2] + ")日の桁数エラー /　";
			}
		}


		//*****
		fieldNo = DataFieldNo(openField);
		mdata = DataArray[i][fieldNo];
		data = DataArray[i][fieldNo].split(":");
		if(data.length < 3 ){
			msg += openField + "(" + mdata + ")のデータエラー　/　";
		}else{
			if(data[0].length != 2){
				msg += openField + "(" + data[0] + ")時の桁数エラー /　";
			}
			if(data[1].length != 2){
				msg += openField + "(" + data[1] + ")分の桁数エラー /　";
			}
		}

		//*****
		fieldNo = DataFieldNo(closeField);
		mdata = DataArray[i][fieldNo];
		data = DataArray[i][fieldNo].split(":");
		if(data.length < 3 ){
			msg += closeField + "(" + mdata + ")のデータエラー　/　";
		}else{
			if(data[0].length != 2){
				msg += closeField + "(" + data[0] + ")時の桁数エラー /　";
			}
			if(data[1].length != 2){
				msg += closeField + "(" + data[1] + ")分の桁数エラー /　";
			}
		}

		//******
		fieldNo = DataFieldNo(tagField);
		data = DataArray[i][fieldNo];
		if(data != ""){
			var find = 0;
			for(var s = 0 ; s < targetArray.length ; s++){
				if(targetArray[s][tagId] == data){
					find = 1;
					break;
				}
			}
			if(find == 0){
				msg += tagField + "(" + data + ")と一致するラベルがありません / ";
			}
		}

		//******
		fieldNo = DataFieldNo(whereField);
		data = DataArray[i][fieldNo];
		var find = 0;
		for(var s = 0 ; s < locationArray.length ; s++){
			if(locationArray[s][locId] == data){
				find = 1;
				break;
			}
		}
		if(find == 0){
			msg += whereField + "(" + data + ")と一致する名前がありません / ";
		}


		//*****************
		if(msg != ""){
			//fieldNo = DataFieldNo("no");
			//buff += i + "行目 no:" + DataArray[i][fieldNo] + "　=　";

			fieldNo = DataFieldNo(titleField);
			buff += i + "行目 :" + DataArray[i][fieldNo] + "[" + eventdate + "]　=　";

			buff += msg; 

			//提供元ファイル名を追加
			fieldNo = DataFieldNo(souceField);
			buff += "【データ元：" + DataArray[i][fieldNo] + "】";

			buff += CR;
		}
	}

	$("#errorArea").css("display" , "block");
	$("#errorResult").html(buff);
}

function errorAreaClose(){
	$("#errorArea").css("display" , "none");
}


//*****************
var targetTable   = "../localhost/target.csv";
var locationTable = "../localhost/location.csv";

var targetArray   = new Array();
var locationArray = new Array();

$(function() {
	//連想配列の設定
	setRensouArray(targetTable , targetArray , function(){
		//入力補助機能
		var ren = targetArray;
		for(var i = 0 ; i < ren.length ; i++){
			$("#taglist").append($("<option>").val(ren[i][tagId]).html(ren[i][tagName] + "(" + ren[i][tagId] + ")" ));
		}
	});

	setRensouArray(locationTable , locationArray , function(){
		//入力補助機能
		var ren = locationArray;
		for(var i = 0 ; i < ren.length ; i++){
			$("#wherelist").append($("<option>").val(ren[i][locId]).html(ren[i][locId]));
		}
	});


});

//csvデータを読み込み、連想配列に設定する
function setRensouArray(table , rensouArray , cr){
	csvToArray( table , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			rensouArray.push(rensou);
		}
		cr();
	});	
}

//CSVファイルの読み込み
function csvToArray(filename, cb) {
	//キャッシュしない
	$.ajaxSetup({
		cache: false
	});

	$.get(filename, function(csvdata) {
		//CSVのパース作業
		//CRの解析ミスがあった箇所を修正しました。
		//以前のコードだとCRが残ったままになります。
		// var csvdata = csvdata.replace("\r/gm", ""),
		csvdata = csvdata.replace(/\r/gm, "");

		var line = csvdata.split("\n"),
		ret = [];
		for (var i in line) {
        		//空行はスルーする。
        		if (line[i].length == 0) continue;

        		var row = line[i].split(",");
        		ret.push(row);
      		}
      		cb(ret);
	});
}


</script>


<!--body onload="ShowData()"-->
<body onload="init()">

<div id="cont_area">
</div>

<div id="inputSupport">
	<input type="text" id="Mydate" size="12" value="" readonly="readonly"><br />
	<input type="button" id="MydateSet" onClick="setData('when')" value="日付をwhenに設定する" />
	<br />
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

#errorArea{
	display : none;
}

</style>

