<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

include_once("admin/include.php");


//引き継ぎ情報の設定
if(isset($_GET['db'])){
	$db = $_GET['db'];
}else{
	print("Access Error!!");
	exit();
}

if(isset($_GET['gp'])){
	$gp = $_GET['gp'];
}else{
	print("Access Error!!");
	exit();
}

if(isset($_GET['dir'])){
	$dir = $_GET['dir'];
	$dir .= "/";
}else{
	print("Access Error!!");
	exit();
}

if(isset($_GET['csv'])){
	$csv  = $_GET['csv'];
}else{
	print("Access Error!!");
	exit();
}


//データベースの状態を確認
//データ件数のチェック
$dbs        = mysqli_connect($db_host,$db_id,$db_pw,$db_name);
//$sql_server = mysql_select_db($db_name,$dbs);

mysqli_query($dbs,'SET NAMES utf8'); //
$Table_name = $db;
$result = mysqli_query($dbs ,"select * from " . $Table_name . " where id != 'DELETE' ;");

//有効データ数（DELETE以外）
$db_rec = mysqli_num_rows($result);
if($db_rec < 1){
	$db_rec  = 0;
	$max_rec = 0;
}else{

	//最大レコード番号のチェック
	$sql     = "SELECT MAX(no) as mx FROM " . $Table_name . ";";
	$result2 = mysqli_query($dbs , $sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	$rec     = mysqli_fetch_assoc($result2);
	$max_rec = $rec['mx'];
}
mysqli_close($dbs);


//csvファイルのパス名
$path = $dir . $csv;

$cr = chr(0x0d) . chr(0x0a);
//$cr = '<br />';

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

				$fields[$i] = $data[$i];

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

			for($i = 0 ; $i < count($fields) ; $i++){
				//データからダブルクォーテションを削除
				$data[$i] = str_replace('"' , '' , $data[$i]);

				/*
				//強制的にutf-8にエンコードする
				$from_code = check_encode($data[$i]);
				if($from_code != "UTF-8"){
					$fdata[$cnt][$fields[$i]] = mb_convert_encoding($data[$i], "UTF-8", $from_code);
				}else{
					$fdata[$cnt][$fields[$i]] = $data[$i];
				}
				*/
				$fdata[$cnt][$fields[$i]] = $data[$i];

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
	$buff = "";
	//$i = 1 からスタート。0 はフィールドを意味するため
	$buff = "[ " . $cr;
	for($i=1 ; $i < $cnt ; $i++){
		$buff .= '{';
		for($s = 0 ; $s < $key_cnt ; $s++){
			$buff .= '"' . $fields[$s] . '" : ';
			$buff .= '"' . $fdata[$i][$fields[$s]] . '"';

			if($s < $key_cnt - 1){
				$buff .= ' , ';
			}
		}
		$buff .= ' } ';
		if($i < $cnt -1 ){
			$buff .= ' , ' . $cr;
		}
	}
	$buff .= $cr . ' ];';


//文字エンコードチェック関数（未使用）
function check_encode($str){
	foreach(array('UTF-8','SJIS','EUC-JP','ASCII','JIS','ANSI') as $charcode){
		if(mb_convert_encoding($str, $charcode, $charcode) == $str){
			return $charcode;
		}
	}

	return null;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="Robots" content="ALL">

<!--meta name="viewport" content="initial-scale=1.0, user-scalable=no" /-->
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0" />

<title>csv読み込み｜まちの灯りー野々市</title>
<link rel="stylesheet" href="css/mapStyle.css" type="text/css" />

<!-- jquery ライブラリ -->
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>

<!-- googlemaps 描画用ライブラリ & 距離や面積の計算用ライブラリ -->
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&sensor=false"></script>

<script type="text/javascript" src="js/myScript.js"></script>
<script type="text/javascript" src="js/wisteriahill/googlemaps.js"></script>
<script type="text/javascript" src="js/wisteriahill/streetview.js"></script>
	
<script type="text/javascript">

<?php
	//javascript化
	//database
	print('var db  ="' . $db  . '";' . $cr );
	print('var gp  ="' . $gp  . '";' . $cr );

	print('var db_rec  =' . $db_rec  . ';' .$cr );
	print('var max_rec =' . $max_rec . ';' .$cr );

	//csv
	print('var csv ="' . $csv . '";' . $cr ); 

	//fileds data
	print("var key_cnt = " . $key_cnt . ";" . $cr );
	print("var fields  = " . $fbuff . $cr );

	//recode data
	print("var rec_cnt = " . ($cnt - 1) . ";" . $cr );
	print("var recdata = " . $buff . $cr );

?>

function csvload_init(){

	getData("<?php print($db); ?>" , "no");

	var header = '<h3>グループ管理簿：　' + gp + '　(' + db + ')　</h3>';
	if(db_rec == 0){
		header += '　現在、有効データがありません<br />';
		header += '<input type="hidden" id="press"   />';
		header += '<input type="hidden" id="delete"  />';
	}else{
		header += '　現在' + db_rec + '件の有効データがあります（最終no：' + max_rec + '）<br />';
		if(max_rec - db_rec > 0){
			header += '　<input type="checkbox" id="press"  checked />' + (max_rec - db_rec) +  '件の空き番号を詰める<br />';
		}else{
			header += '<input type="hidden" id="press" />';
		}

		header += '　<input type="checkbox" id="delete" />' + db_rec + '件の有効データを全て削除する<br />';
	}

	header += '<hr />';

	header += '　' + csv + ' から ' + rec_cnt + ' 件のデータをアップロードします';
	header += '<br /><br />';

	document.getElementById("header").innerHTML  = header;

	csvdatalist(0);
}


function csvdatalist(sw){

	var Llng = lightData.length;

	//読み込み側csvをベースにして、既存データのチェックを行う
	var buff = "";

	//tableの準備
	buff += '<table border="1" CELLSPACING="0" CELLPADDING="0">';
	buff += '<tr>';
	buff += '<th>順</th><th>状態</th><th>no</th><th>Act</th><th>位置</th><th>識別：番号等</th><th>区分</th><th>種別</th><th>管理</th><th>担当</th><th>記事</th><th>写真</th><th>投稿・更新者</th><th>投稿日時</th>';

	buff += '</tr>';

	var flg = false;
	for(i = 0 ; i < rec_cnt ; i++){

		Remt = recdata[i];
		buff += '<tr>';

		if(!flg){
			buff += '<td align="right">' + (i+1) + '</td>';
			if(sw == 0){
				buff += '<td nowrap>UP前</td>';
			}else{
				buff += '<td nowrap>UP後</td>';
			}
			buff += '<td nowrap align="right">' + Remt.no          + '</td>';
			buff += '<td nowrap align="center">' + Remt.activ       + '</td>';
			buff += '<td nowrap>' + Remt.latitude    + ',' + Remt.longitude + '</td>';
			buff += '<td nowrap>' + Remt.ident_label + ':' + Remt.ident_no  + '</td>';

			buff += '<td nowrap>' + Remt.divi_id     + ' ' + Remt.divi_name  + '</td>';

			buff += '<td nowrap>' + Remt.class_id    + ' ' + Remt.class_name + '</td>';

			buff += '<td nowrap>' + Remt.mgr_id      + ' ' + Remt.mgr_name   + '</td>';

			buff += '<td nowrap>' + Remt.admin_id    + ' ' + Remt.admin_name + '</td>';

			buff += '<td nowrap>' + Remt.memo        + '</td>';
			buff += '<td nowrap>' + Remt.photo       + '</td>';
			buff += '<td nowrap>' + Remt.contributor + '</td>';
			buff += '<td nowrap>' + Remt.cont_date   + '</td>';

		}
		flg = false;

		buff += '</tr>';
	}
	buff += '</table>';

	document.getElementById("csvdata").innerHTML = buff;	
}

var USER = "";
function precheck(){
	
	//Ident_label
	var gpn   = opener.GroupDataNo;
	var label = opener.GroupData[gpn].ident;
	//alert(label);

	USER = opener.inputName;
	//alert(USER);

	if(document.getElementById("delete").checked){
		new_no = 1;
	}else if(document.getElementById("press").checked){
		new_no = db_rec + 1;
	}else{
		new_no = max_rec + 1;
	}

	//
	var Demt = opener.LightDivi;
	var Cemt = opener.LightClass;
	var Memt = opener.Manager;
	var Aemt = opener.MgrName;

	for(var i = 0 ; i < rec_cnt ; i++){

		var Rec = recdata[i];	

		//新しい番号
		Rec.no = new_no++;
	
		//識別と投稿者を設定
		Rec.ident_label = label;
		Rec.contributor = USER;

		//投稿日を設定
		Rec.cont_date = "(real time)";

		//区分、種別、管理、担当　をコード側に合わせる
		if(Rec.divi_id == "null"){
			Rec.divi_id = "";
		}
		Rec.divi_name = "----";

		for( s = 0 ; s < Demt.length ; s++){
			if(Rec.divi_id == Demt[s].id){
				Rec.divi_name  = Demt[s].division;
			}
		}

		if(Rec.class_id == "null"){
			Rec.divi_id = "";
		}
		Rec.class_name = "----";

		for( s = 0 ; s < Cemt.length ; s++){
			if(Rec.class_id == Cemt[s].id){
				Rec.class_name  = Cemt[s].class;
			}
		}


		if(Rec.mgr_id == "null"){
			Rec.mgr_id = "";
		}
		Rec.mgr_name = "----";

		for( s = 0 ; s < Memt.length ; s++){
			if(Rec.mgr_id == Memt[s].id){
				Rec.mgr_name  = Memt[s].name;
			}
		}


		if(Rec.admin_id == "null"){
			Rec.admin_id = "";
		}
		Rec.admin_name = "----";

		for( s = 0 ; s < Aemt.length ; s++){
			if(Rec.admin_id == Aemt[s].id){
				Rec.admin_name  = Aemt[s].name;
			}
		}

	}

	//csvload_init();
	csvdatalist(1);
}


//アップロード後、アップロードボタンを非表示にする
function excec_after(){
	document.getElementById("excec").style.visibility = "hidden";

	//ファイル選択ウィンドウを閉じる
	//window.opener.close();

	//自ウィンドウを閉じる
	window.close();
}

function cancel(){
	window.close();
}

//アップロード時の手順
function excec_cont(){
	//既存データの整理
	if(document.getElementById("delete").checked){
		befor_data("ALL");
	}else if(document.getElementById("press").checked){
		befor_data("DELETE");
	}

	//csvアップロード
	excec();
	//ボタンの非表示
	excec_after();
}

function befor_data(type){
	var table = db;

	// json の要素・データは "" でくくること。
	//var buff = '[';	//json形式に整形
	var buff = '[{ "table" : "' + table + '" ,';	//target table 設定
	buff += ' "erase" : "' + type + '" , ';
	buff += ' "admin" : "' + USER + '" , ';
	buff += ' "memo"  : "CSVアップロードの前処理" } ';
	buff += ']';

	//alert(buff);

	//**************************
	//json データをサーバーへ送信する
	// 送るデータ形式はJSONでなければ、PHP側でエラーが出る.のでJSON.stringify()でJSON形式に変換
	//send_data= JSON.stringify(buff);
	//send_data = JSON.parse(buff);

	send_data = buff;

	//alert(send_data);

        // 送信処理
	$.ajax({
		url: "erasedata.php", // 送信先のPHP
		type: "POST", // POSTで送る
		//contentType: "Content-Type: application/json; charset=UTF-8",    
		//必須ではなさそうだが、サーバ側との整合のために明示しておいた方がよい。
		//dataType: 'json',
		//受信形式 必須ではなさそうだがサーバ側との整合のために明示しておいた方がよい。
		data:send_data ,

		async : false,	//同期通信

		success : function(data, status, xhr) {
 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			//saveExec = true;
			//alert(data + "既存データの整理が正常に実行されました");
			//通信OK後、データベース処理でエラーとなった場合の確認処理が必要。

		} ,

		error : function(xhr, status, error) {
			// 通信失敗時の処理
        		console.log("error");
			console.log("status ="+status);
			console.log("xhr ="+xhr);
			alert("既存データの整理がエラーとなりました");
		}
	});

	return true;
}


//function saveData(table) の改造
function excec(){
	var table = db;

	// json の要素・データは "" でくくること。
	//var buff = '[';	//json形式に整形
	var buff = '[{ "table" : "' + table + '" },';	//target table 設定

	var len = recdata.length;

	for(var i=0 ; i < len ; i++){
		var Data = recdata[i];

		buff += '{';
		buff += '"id" : "' + (i+1) + '",';
		buff += '"latitude"  : "' + Data.latitude  + '",';
		buff += '"longitude" : "' + Data.longitude + '",';
	
		buff += '"divi_name" : "' + Data.divi_name     + '",';
		buff += '"divi_id"   : "' + Data.divi_id       + '",';
		buff += '"class_name": "' + Data.class_name    + '",';
		buff += '"class_id"  : "' + Data.class_id      + '",';
		buff += '"mgr_name"  : "' + Data.mgr_name      + '",';
		buff += '"mgr_id"    : "' + Data.mgr_id        + '",';
		buff += '"admin_name"  : "' + Data.admin_name  + '",';
		buff += '"admin_id"    : "' + Data.admin_id    + '",';
		//buff += '"ontime"    : "' + Data.ontime      + '",';
		//buff += '"ontime_id" : "' + Data.ontime_id   + '",';
		buff += '"memo"      : "' + Data.memo          + '",';

		buff += '"photo"     : "' + Data.photo         + '",';

		buff += '"ident_label" : "' + Data.ident_label + '",';
		buff += '"ident_no"    : "' + Data.ident_no    + '",';

		buff += '"activ"       : "' + Data.activ       + '",';
		
		//contributorの追加（セーブ時）
		buff += '"contributor" : "' + Data.contributor + '"'; //last , 削除している

		buff += '}';

		if(i < len -1){
			buff += ',';
		}
	}
	buff += ']';

	//alert(buff);

	//**************************
	//json データをサーバーへ送信する
	// 送るデータ形式はJSONでなければ、PHP側でエラーが出る.のでJSON.stringify()でJSON形式に変換
	//send_data= JSON.stringify(buff);
	//send_data = JSON.parse(buff);

	send_data = buff;

	//alert(send_data);

        // 送信処理
	$.ajax({
		url: "savedata.php", // 送信先のPHP
		type: "POST", // POSTで送る
		//contentType: "Content-Type: application/json; charset=UTF-8",    
		//必須ではなさそうだが、サーバ側との整合のために明示しておいた方がよい。
		//dataType: 'json',
		//受信形式 必須ではなさそうだがサーバ側との整合のために明示しておいた方がよい。
		data:send_data ,

		async : false,	//同期通信

		success : function(data, status, xhr) {
 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			saveExec = true;
			alert(len + "件のデータが正常に転送されました");
			//通信OK後、データベース処理でエラーとなった場合の確認処理が必要。

		} ,

		error : function(xhr, status, error) {
			// 通信失敗時の処理
        		console.log("error");
			console.log("status ="+status);
			console.log("xhr ="+xhr);
			alert("データが転送がエラーとなりました");
		}
	});

	return true;
}


</script>
</head>
<body onload="csvload_init()">
<div id="header">
</div>

<div id="info">
　・表示の順に　新しい番号（no）が振られて追加されます<br />
　・識別は、当該管理簿のものに置き換わります（番号等は変わりません）<br />
　・区分、種別、管理、担当は、コードに対応した名称に整理されます<br />
　・投稿・更新者、投稿日時は、更新されます<br />
　・<font color="red">アップロード実行後は、必ず「グループの選択／切り換え」から再度実施してください</font><br />
<br />
<input type="button" id="precheck"  onClick="precheck()"  value="実行前の確認" />
<hr />
<input type="button" id="excec"  onClick="excec_cont()"  value="アップロード実行" />
<input type="button" id="cancel" onClick="cancel()" value="閉じる" />
<br /><br />
</div>

<div id="csvdata">
</div>



</body>
</html>
