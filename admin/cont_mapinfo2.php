<?php
include_once("include.php");

$ThisFile   = "cont_mapinfo2.php";
//$NextFile   = $_SESSION["NextJob"];
//$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

//=============================
$acc_level  = 1;			//アクセスレベル

$ReturnFile = $ThisFile;		//戻り先のファイル名
$_SESSION['CallJob'] = $ThisFile;	//log_in.php　からの戻り用
include("log_in_check.php");
//=============================


//使用するテキストDB
if(isset($_GET['dir'])){
	$db_Dir   = "../" . trim($_GET['dir']);
}else{
	$db_Dir   = "../uploads/mapinfo";
}
if(isset($_GET['fname'])){
	$db_filename = trim($_GET['fname']);

	/*
	$files = explode("." , $db_filename);
	$db_Head = $files[0];
	$db_Ext  = $files[1];
	*/

	//ファイル名(filename)に　. が入っても拡張子(extension)抽出可能
	$filepath = pathinfo($db_filename);
	$db_Head = $filepath['filename'];
	$db_Ext  = $filepath['extension'];

	//print($db_Head . " / " . $db_Ext . "<br />");
}else{
	$db_Head = "201512";
	$db_Ext  = "csv";
}
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


//common_header("control MapInfoTable");

$user_level = Access_check( $acc_level ,1,1,$ReturnFile);
//print('レベル　＝　1:一般ユーザ　2:管理ユーザ　3:システム管理者<br>');

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
$falename = $db_filename;

if($ReadOnly == 'true'){
	//echo 'あなたのファイルではありません（閲覧のみ可能）';
	common_header("公園・施設情報の閲覧<span class='sub_title'>$falename</span>");
}else{
	common_header("公園・施設情報の編集<span class='sub_title'>$falename</span>");
	/*
	echo '
	<ul>
	<li>,（カンマ）"（ダブルクオーテーション）\'（シングルクオーテーション）は使用できません</li>
	<li>データを更新する場合は、必ずファイルに保存してください</li>
	</ul>
	';
	*/
}

//*************
//print("<h3>【ファイル名：" . $db_Table . "】</h3>");

//db内容をjavascript用に読み込む
$DataString = csvDatabaseRead($db_Table,1);
//print($DataString);

?>

<?php //<link rel="stylesheet" href="../css/csvdatabase2.css"> ?>

<?php //<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script> ?>
<script type="text/javascript" src="../js/csvdatabase2.js?ver=160120"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript" src="../js/setting.js"></script>

<!--googlemaps api-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<!-- Include googlemaps api -->
<script type="text/javascript" src="../js/googlemap_api.js"></script>


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
//配列情報の設定
print("var DataArray =" . $DataString  );
?>

<?php

//マップファイルのフィールド定義を読み出す
//個別に手動で定義することも可能
$fields_File = "../localhost/mapfields.csv";
$result = fieldDataRead($fields_File);
echo $result;

?>

/*
//*************
// 呼び出し側のｐｈｐファイルで定義
//フィールドの幅確保
var Field_etc = new Array();
Field_etc['no']         = 50;
Field_etc['display']    = 50;
Field_etc['list_no']    = 50;
Field_etc['category']   = 100;
Field_etc['name']       = 150;
Field_etc['address']    = 200;
Field_etc['lat']        = 150;
Field_etc['lng']        = 150;
Field_etc['memo']       = 200;
Field_etc['memo1']      = 200;
Field_etc['memo2']      = 200;
Field_etc['phone']      = 100;
Field_etc['phone1']      = 100;
Field_etc['phone2']      = 100;
//**************
*/

//フィールドの入力タイプ
var Ftype = new Array();
Ftype['address'] = "text";
Ftype['memo1']   = "text";
Ftype['memo2']   = "text";


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
	init();
	//ShowData();		//データの読み込みと表示
	locationGetLatLng();	//マップの初期設定
}

function setOption(){
	var option  = "<br /><br /><input type='button' onclick='map_visible()' value='マップの表示' />　";

	return option;
}

function locationGetLatLng(){
	//setting.js　で定義
	//var DEFAULT_LAT = 37.390556;
    	//var DEFAULT_LNG = 136.899167;

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

	
	//if(confirm("この位置を設定しますか？" + x +" , "+y)){
		$('#Mydata_' + latFieldNo).val(y);
		$('#Mydata_' + lngFieldNo).val(x);
		add_dialog("緯度・経度を設定しました");
	//}else{
	//	return false;
	//}

	//住所の取得
	var request = {
		location : mPoint
	};

	var geocoder = new google.maps.Geocoder();
	geocoder.geocode(request,function(results,status){
		if(status == google.maps.GeocoderStatus.OK){
			//,の除去
			var address = results[0].formatted_address.replace(","," ");
			if(confirm(address + "　この位置の住所を設定しますか？")){
				$('#Mydata_' + addressFieldNo).val(address);
				add_dialog("住所をを設定しました");
			}
		}else{
			alert("この位置の住所は確認できませんでした");
		}
	});

}

function map_AddrToSearch(){
	var place = $('#searchAddr').val();
	if(place == ""){
		alert("検索する住所を入力してください");
		return false;
	}

	// ジオコーダのコンストラクタ
	var geocoder = new google.maps.Geocoder();

	// geocodeリクエストを実行。
	// 第１引数はGeocoderRequest。住所⇒緯度経度座標の変換時はaddressプロパティを入れればOK。
	// 第２引数はコールバック関数。
	geocoder.geocode({
		address: place
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			// 結果の表示範囲。結果が１つとは限らないので、LatLngBoundsで用意。
			var bounds = new google.maps.LatLngBounds();

			for (var i in results) {
				if (results[i].geometry) {
					// 緯度経度を取得
					var latlng = results[i].geometry.location;
				        // 住所を取得(日本の場合だけ「日本, 」を削除)
          				var address = results[i].formatted_address.replace(/^日本, /, '');
					// 検索結果地が含まれるように範囲を拡大
					bounds.extend(latlng);

					/*
          				// あとはご自由に・・・。
          				new google.maps.InfoWindow({
            					content: address + "<br>(Lat, Lng) = " + latlng.toString()
          				}).open(map, new google.maps.Marker({
            					position: latlng,
            					map: map
          				}));
					*/
        			}
      			}
			// 範囲を移動
			mapCanvas.fitBounds(bounds);
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

	//クリックした位置に現在ピンが移動する
	google.maps.event.addListener(mapCanvas, 'click', function(event){
		now_marker.setPosition(event.latLng);
	});   
}


</script>


<body onload="locationInit();">

<div id="cont_area">
</div>

<div id="list">
</div>

<div id="map_area">
	<div id="map_area_inner">
		<div id="map_canvas">
		</div>
		<div class="btns">
			<input type="button" id="map_getlatlng" onClick="mapGetLatLng()" value="この位置の緯度・経度を設定する" />
			<span class="input_btn_set"><input type="text" id="searchAddr" value="" />
			<input type="button" id="map_hidden" onClick="map_AddrToSearch()" value="住所で検索する" />
			</span>
			<input type="button" id="map_hidden" onClick="map_hidden()" value="キャンセル" />
		</div>
	</div>
</div>

<?php common_menu(1); ?>
<?php include_once 'include_footer.php'; ?>