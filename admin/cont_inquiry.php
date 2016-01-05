<?php
include_once("include.php");

$ThisFile   = "cont_inquiry.php";
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
$db_Dir   = "../inquiry";
$db_Head = "inquiry";
$db_Ext  = "csv";
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


common_header("control InquiryTable");

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

<link rel="stylesheet" href="../css/csvdatabase.css">

<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/csvdatabase.js"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<!--googlemaps api-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&sensor=false"></script> 
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

var latFieldName = "lat";
var lngFieldName = "lng";
var addressFieldName = "address";
var latFieldNo = DataFieldNo(latFieldName);
var lngFieldNo = DataFieldNo(lngFieldName);
var addressFieldNo = DataFieldNo(addressFieldName);

var mapCanvas;		//マップ
var now_marker;		//位置取得用マーカー

function locationInit(){
	ShowData();		//データの読み込みと表示
	locationGetLatLng();	//マップの初期設定
}

function setOption(){
	var option  = "<br /><br /><input type='button' onclick='map_visible()' value='マップの表示' />　";

	return option;
}

function locationGetLatLng(){
	//sub/js/main/js　で定義
	//どこかで設定ファイルの作成が必要
	var DEFAULT_LAT = 37.390556;
    	var DEFAULT_LNG = 136.899167;

	showGoogleMap(DEFAULT_LAT,DEFAULT_LNG);
}

function map_visible(){
	//lat,lng に位置情報があった場合、その位置に移動しマップを表示する
	var lat = $('#Mydata_' + latFieldNo).val();
	var lng = $('#Mydata_' + lngFieldNo).val();

	//lat lng の正常性確認が必要
	if(lat != "" && lng != ""){
		var latlng = new google.maps.LatLng(lat , lng);
		now_marker.setPosition(latlng);
		mapCanvas.setCenter(latlng);
	}

	$('#map_area').css('visibility' , 'visible');
}

function map_hidden(){
	$('#map_area').css('visibility' , 'hidden');
}

function mapGetLatLng(){
	var mPoint = now_marker.getPosition();
	var x = mPoint.lng();	//x : lng
	var y = mPoint.lat();	//y : lat

	if(confirm("この位置を設定しますか？" + x +" , "+y)){
		$('#Mydata_' + latFieldNo).val(y);
		$('#Mydata_' + lngFieldNo).val(x);
	}else{
		return false;
	}

	//住所の取得
	var request = {
		location : mPoint
	};

	var geocoder = new google.maps.Geocoder();
	geocoder.geocode(request,function(results,status){
		if(status == google.maps.GeocoderStatus.OK){
			//,の除去
			var address = results[0].formatted_address.replace(","," ");
			if(confirm(address + "　この住所を取得しますか？")){
				$('#Mydata_' + addressFieldNo).val(address);
			}
		}else{
			alert("住所は確認できませんでした");
		}
	});

}


//
function showGoogleMap(initLat, initLng) {
        var latlng = new google.maps.LatLng(initLat, initLng);
        var opts = {
            	zoom: 16,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        //var map = new google.maps.Map(document.getElementById("map_canvas"), opts);
        mapCanvas = new google.maps.Map(document.getElementById("map_canvas"), opts);

        //現在地のピン
        var now_latlng = new google.maps.LatLng(initLat, initLng);
        //var now_marker = new google.maps.Marker({
        now_marker = new google.maps.Marker({
            position:now_latlng,
            title: '位置情報取得用マーカー',
	    draggable : true,

            map: mapCanvas,
        });

	now_marker.setMap(mapCanvas);
}


</script>


<body onload="locationInit();">

<div id="cont_area">
</div>

<div id="list">
</div>

<div id="map_area">
	<div id="map_canvas">
	</div>
	<input type="button" id="map_getlatlng" onClick="mapGetLatLng()" value="位置座標の取得" />
	　
	<input type="button" id="map_hidden" onClick="map_hidden()" value="マップを閉じる" />
</div>


</BODY>
</HTML>

<style>
#map_area{
	visibility : hidden;

	position : fixed;
	top  : 10px;
	left : 200px;

	padding : 5px;
	border : 3px solid #000000;
	background : lightgreen;

	z-index : 10;
}
#map_canvas{
	width : 600px;
	height: 400px;
}

</style>
