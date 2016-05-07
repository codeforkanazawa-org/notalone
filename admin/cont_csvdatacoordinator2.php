<?php
include_once("include.php");

$ThisFile   = "cont_csvdatacoodinator.php";
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
	$files = explode("." , $db_filename);
	$db_Head = $files[0];
	$db_Ext  = $files[1];
}else{
	$db_Head = "openData_prepass";
	$db_Ext  = "csv";
}
$db_Table = $db_Dir . "/" . $db_Head . "." . $db_Ext;


common_header("CsvDataCoodinator");

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

//db内容をPHP配列に読み込む
$DataString = csvDataSouceRead($db_Table,1);
//print($DataArray);


?>


<?php
/*
//fieldName の読み出し　確認
$fcnt = count($DataArray[0]);

print("フィールド数：" . $fcnt ."<br />"); 
for($i = 0 ; $i < $fcnt ; $i++){
	print($DataArray[0][$i] . "<br />");
}

$dcnt = count($DataArray);
print("データ件数：" . $dcnt . "<br />");

for($s = 1 ; $s < $dcnt ; $s++){
	$mcnt = count($DataArray[$s]);
	print("No " . $s . "<br />");

	if($mcnt != $fcnt){
		foreach($DataArray[$s] as $key => $value){
			print($key . " : ");
			print($value . "<br />");
		}
		print("<br />");
	}else{
		//print("この配列数：" . $mcnt . "<br >");
	}
	
}
*/





//csvデータベースのアクセス
//データソースをそのまま読み込み、連想配列で返す
function csvDataSouceRead($filename,$type){
	//type
	//0 : return Array (default)
	//1 : return String

	//csvファイルのパス名
	$path = $filename;
	$cr = chr(0x0d) . chr(0x0a);

	//csvファイルを読み込む
	$cnt = 0;
	$fdata = array();

	$fp = fopen($path, "r");
	while ($sline = fgets($fp)) {

		//文字列から制御コードを除く
		//$line = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $sline); 
		$line = preg_replace('/[\x0D\x0A]/', '', $sline); 

		//print $line . "<br>\n";

		//$data = split(",",$line);
		$data = explode(",",$line);

		//print_r($data);

		if(strpos($data[0],"//") === 0){
			//コメント行は省く
			continue;
		}

		if($cnt == 0){
			//フィールド情報
			$fields  = array();
			$key_cnt = count($data);	//field key 数

			$fbuff = "[ ";
			for($i = 0 ; $i < $key_cnt ; $i++){
				//print($i . "=" .  $data[$i] . "<br />");
				
				//フィールド名からダブルクォーテションを削除
				$data[$i] = str_replace('"' , '' , $data[$i]);
				//フィールド名からシングルクォーテションを削除
				$data[$i] = str_replace("'" , "" , $data[$i]);

				//空白フィールドをチェック
				if($data[$i] == "" || $data[$i]== " " || $data[$i] == "　"){
					$fields[$i] = "addfield_" . $i;
				}else{
					$fields[$i] = $data[$i];
				}

				$fbuff .= '"' . $data[$i] . '"';

				if($i < $key_cnt - 1){
					$fbuff .= ' , ';
				}

				//フィールド情報のチェックが必要か
			} 
			$fbuff .= ' ];';

			//print_r($fields);
			//print("<br /><br />");
		}else{
			$data_cnt = count($data);

			for($i = 0 ; $i < $data_cnt ; $i++){
				//データからダブルクォーテションを削除
				$data[$i] = str_replace('"' , '' , $data[$i]);
				//フィールド名からシングルクォーテションを削除
				$data[$i] = str_replace("'" , "" , $data[$i]);

				/*
				//強制的にutf-8にエンコードする
				$from_code = check_encode($data[$i]);
				if($from_code != "UTF-8"){
					$fdata[$cnt][$fields[$i]] = mb_convert_encoding($data[$i], "UTF-8", $from_code);
				}else{
					$fdata[$cnt][$fields[$i]] = $data[$i];
				}
				*/

				//データの数が、fields数よりオーバーしている場合、仮の名称をつける
				if($i >= $key_cnt){
					$fdata[$cnt]["addfields_" . $i] = $data[$i];
				}else{
					$fdata[$cnt][$fields[$i]] = $data[$i];
				}

				//print $line . "<br>\n";
			}
			//print_r($fdata[$cnt]);
			//print("<br /><br />");
		}
		//有効データ数
		$cnt++;
	}

	//print_r($fdata);
	//print("<br /><br /> cnt=" . $cnt . "<br /><br />");

	fclose($fp);


	//連想配列 の出力
	//配列での出力
	$buffArray = array();

	//0番目にフィールド情報をセット
	$buffArray[0] = $fields;

	//$i = 1 からスタート。0 はフィールドを意味するため
	for($i=1 ; $i < $cnt ; $i++){
		$d_cnt = count($fdata[$i]);
		for($s = 0 ; $s < $d_cnt ; $s++){
		//for($s = 0 ; $s < $key_cnt ; $s++){
			if($s >= $key_cnt){
				$addfield = "addfields_" . $s;
				$buffArray[$i][$addfield] = $fdata[$i][$addfield];				}else{
				$buffArray[$i][$fields[$s]] = $fdata[$i][$fields[$s]];
			}
		}
	}


	//文字列での出力（Javascript用）
	$buff = "";

	//0 はフィールド
	//1からデータ
	$buff = "[ " . $cr;
	for($i=0 ; $i < count($buffArray) ; $i++){
		$buff .= '[';
		$d_cnt = count($buffArray[$i]);

		for($s = 0 ; $s < $d_cnt ; $s++){
		//for($s = 0 ; $s < $key_cnt ; $s++){
			if($i == 0){
				$buff .= "'" . $buffArray[$i][$s] . "'";
			}else{
				//$buff .= "'" . $buffArray[$i][$buffArray[0][$s]] . "'";
				//連想配列から添字で取り出す
				$buff .= "'" . current(array_slice($buffArray[$i],$s,1,true)) . "'";
			}

			//if($s < $key_cnt - 1){
			if($s < $d_cnt - 1){
				$buff .= ' , ';
			}
		}
		$buff .= ' ] ';

		if($i < $cnt -1 ){
			$buff .= ' , ' . $cr;
		}
	}
	$buff .= $cr . ' ];';


	//return type
	if($type!=1){
		return $buffArray;

	}else{
		return $buff;
	}
}




?>




<link rel="stylesheet" href="../css/csvdatabase2.css">

<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/csvdatabase2.js"></script>
<script type="text/javascript" src="../js/sha256.js"></script>

<script type="text/javascript" src="../js/setting.js"></script>

<!--googlemaps api-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&sensor=false"></script> 
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

//*************
// 呼び出し側のｐｈｐファイルで定義
//フィールドの幅確保
var Field_etc = new Array();
Field_etc['no']         = 50;

//*************

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


//
function Init(){
	var menu = "<input type='button' onClick='fieldsModify()' value='フィールドの調整'/>";
	menu += "<input type='button' onClick='locationInit()' value='データ一覧' />";
	menu += "<hr />";

	$('#list').html("");
	$('#menu').html(menu);
}

function fieldsModify(){
	var ary = DataArray[0];
	var buff = "<table border='1'>";
		buff += "<tr><td>Up</td><td>Dw</td>";
		buff += "<th>No</th><th>現在のフィールド名</th><th>新しいフィールド名</th></tr>";
	for(var i = 0 ; i < ary.length ; i++){
		buff += "<tr>";

		if(i == 0){
			buff += "<td></td><td><input type='button' onClick='fieldDw(" + i + ")' value ='↓' /></td>";
		}else if(i == ary.length - 1){
			buff += "<td><input type='button' onClick='fieldUp(" + i + ")' value ='↑' /></td><td></td>";
		}else{
			buff += "<td><input type='button' onClick='fieldUp(" + i + ")' value ='↑' /></td>";
			buff += "<td><input type='button' onClick='fieldDw(" + i + ")' value ='↓' /></td>";
		}

		buff += "<td>";
		buff += (i+1);
		buff += "</td>";
		buff += "<td>";
		buff += ary[i];
		buff += "</td>";
		buff += "<td>";
		buff += "<input type='text' id='fieldName_" + i + "' value='' />";
		buff += "</td>";
		buff += "</tr>"; 
	}
	buff += "</table>";

	buff += "・No<input type='text' id='addFieldNo' value='' size='3' />の後に";
	buff += "<input type='text' id='addFieldName' value='' />";
	buff += "<input type='button' onClick='addField()' value='フィールドを追加する' />";
	buff += "（先頭に追加する場合は 0 ）<br />";
	buff += "　初期値：<input type='text' id='default_data' size='10' value='' />";
	buff += "　数値の場合：加減数<input type='text' id='step_count'  size='5' value='0' />";
	buff += "<hr />";

	buff += "・No<input type='text' id='deleteFieldNo' value='' size='3' />";
	buff += "<input type='button' onClick='deleteField()' value='フィールドを削除する' />";
	buff += "<br />（注意）フィールドの削除は、データの調整が完了した後に実施してください";
	buff += "<br /><br />";
	buff += "<input type='button' onClick='fieldsNameChange()' value='フィールド名を変更する' />";
	buff += "<input type='button' onClick='Init()' value='閉じる' />";
	buff += "<hr />";
	buff += "（注意）データを保存しなければ、フィールドの調整内容はファイルに保存されません";

	$('#list').html(buff);	
}

function fieldsNameChange(){
	var ary = DataArray[0];

	for(var i = 0 ; i < ary.length ; i++){
		var field = "#fieldName_" + i;
		if($(field).val() != ""){
			ary[i] = $(field).val();
		}
	}

	fieldsModify();
}

function addField(){
	var allary = DataArray;
	var ary    = DataArray[0];
	var f_len  = ary.length;

	var fieldNo   = $('#addFieldNo').val();
	var fieldName = $('#addFieldName').val();

	if(fieldNo  == "" || fieldNo < 0 || fieldNo > f_len){
		alert("フィールド番号が不正です");
		return false;
	}
	if(fieldName == "" || fieldName.match(/,/) || fieldName.match(/\'|\"/)){
		alert("フィールド名が不正です");
		return false;
	}

	var data;
	var d_len;
	var spliceNo = parseInt(fieldNo);

	var default_data  = ($('#default_data').val()).trim();
	var setp_count    = ($('#step_count').val()).trim();
	var d_data;
	var step;

	numflg = 0;
	if(default_data == "" ){
		d_data = "";
	}else if(isNaN(default_data)==false){
		numflg = 1;
		d_data = parseInt(default_data);

		if(isNaN(setp_count)==false){
			step = parseInt(setp_count);
		}else{
			numflg  = 0;
			d_data  = default_data;
		}
	}else{
		d_data = default_data;
	}

	for(var i = 0 ; i < allary.length ; i++){
		d_len = allary[i].length;

		if(i == 0){
			data = fieldName;			
		}else{
			data = d_data;

			if(numflg == 1){
				d_data += step;
			}
		}	

		if(fieldNo == 0){
			//先頭に追加
			allary[i].unshift(data);
		}else if(fieldNo == d_len){
			//最後に追加
			allary[i].push(data);
		}else if(fieldNo < f_len){
			//指定場所に追加（フィールド数より長いデータ列は無視）
			allary[i].splice(spliceNo , 0 , data);
		}else{
			//何もしない
		}
	}

	fieldsModify();	
}

function deleteField(){
	var allary = DataArray;
	var ary    = DataArray[0];
	var f_len  = ary.length;

	var fieldNo   = $('#deleteFieldNo').val();
	var fieldName = $('#deleteFieldName').val();

	if(fieldNo  == "" || fieldNo < 1 || fieldNo > f_len){
		alert("フィールド番号が不正です");
		return false;
	}

	var data;
	var d_len;
	var spliceNo = parseInt(fieldNo) - 1;
 
	for(var i = 0 ; i < allary.length ; i++){
		d_len = allary[i].length;

		if(fieldNo == 1){
			//先頭を削除
			allary[i].shift();
		}else if(fieldNo == f_len){
			//最後を削除
			allary[i].pop();
		}else if(fieldNo < d_len){
			//指定場所を削除
			allary[i].splice(spliceNo , 1 );
		}else{
			//何もしない
		}
	}

	fieldsModify();	
}


function fieldUp(no){
	var index = parseInt(no) - 1;
	var ary = DataArray;

	for(var i = 0 ; i < ary.length ; i++){ 
		ary[i].splice( index ,2 , ary[i][index +1 ],ary[i][index]);
	}

	fieldsModify();
}

function fieldDw(no){
	var index = parseInt(no);
	var ary = DataArray;

	for(var i = 0 ; i < ary.length ; i++){
		ary[i].splice( index ,2 , ary[i][index +1 ],ary[i][index]);
	}

	fieldsModify();
}
</script>


<!--body onload="locationInit();"-->
<body onload="Init();">

<div id="cont_area">
</div>

<div id="menu">
</div>

<div id="list">
</div>

<div id="map_area">
	<div id="map_canvas">
	</div>
	<input type="button" id="map_getlatlng" onClick="mapGetLatLng()" value="位置座標を取得する" />
	　
	<input type="text" id="searchAddr" value="" />
	<input type="button" id="map_hidden" onClick="map_AddrToSearch()" value="住所で検索する" />
	　
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
