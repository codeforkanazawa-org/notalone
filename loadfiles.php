<?php
include_once("admin/include.php");

//error display
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 


//ファイルの一覧を表示する
//$flist   = getFileList("files/");
if(isset($_GET['dir'])){
	$this_dir = $_GET['dir'];
}else{
	print("Access Error!!");
	exit();
}

//**** event / map ファイルの切り分け(class)
//event ファイル　＝　username_evt.csv  -> rename : username_evt_xxxxx.csv
//map 　ファイル　＝　username_map.csv  -> rename : username_map_xxxxx.csv
if(isset($_GET['class'])){
	$file_class = "_" . $_GET['class'];	//接続用 _ 追加
}else{
	$file_class = "";
}
//********************************


//新規作成フィールドファイルの追加
if(isset($_GET['field'])){
	$fields_File = $_GET['field'];
}else{
	$fields_File = "";
} 

//ファイルのタイプ
if(isset($_GET['ftype'])){
	$ftype = $_GET['ftype'];
}else{
	$ftype = "";
}

//filelist sortの追加
if(isset($_GET['fsort'])){
	$fsort = $_GET['fsort'];
}else{
	$fsort = "sort";	//<--> rsort
}

$dir  = $this_dir;
$dir .= "/";
$flist = getFileList($dir , $fsort);
$flength = count($flist);


////////////////

if(isset($_GET['key'])){
	$key = $_GET['key'];
}else{
	$key = "";
}

//ファイル選択時に実行する次のファイル
if(isset($_GET['next'])){
	$next = $_GET['next'];
}else{
	$next = "";
}


//log_in.php からの戻り場所指定
//@$ReturnFile = $_SESSION['CallJob'];
$this_file = "../loadfiles.php";	//admin/からみたファイル位置

//$_SESSION['CallJob'] = $this_file . "?dir=" . $this_dir . "&ftype=" . $ftype . "&key=" . $key . "&fsort=" . $fsort . "&field=" . $fields_File . "&next=" . $next;

$_SESSION['CallJob'] = $this_file . "?" . $_SERVER["QUERY_STRING"];	//log_in.php　からの戻り用


//ファイルの階層固定
$next = "admin/" . $next;
$fields_File = "localhost/" . $fields_File;


//複数ファイルを選択（exifファイルの選択を想定）
if(isset($_GET['multi'])){
	if($_GET['multi'] == 1){
		$multi = 1;
	}else{
		$multi = 0;
	}
}else{
	$multi = 0;
}


$sub_title = "";
switch($this_dir){
	case "uploads/events" :
		$sub_title = "イベント情報一覧";
		break;
	case "events" :
		$sub_title = "イベント情報集計一覧";
		break;
	case "uploads/mapinfo" :
		$sub_title = "公園・施設情報一覧";
		break;
}

//user_header("管理ページ" . $sub_title);

user_header($sub_title , "loadxml_init()");
//common_menu(1);

/*
<!--
<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="Robots" content="ALL" />

<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

<title>ファイルの選択</title>
-->

<!--script type="text/javascript" src="js/myScript.js"></script-->

<!-- jquery ライブラリ -->
 <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
*/?>

<script type="text/javascript">

<?php	

	//ファイル一覧をjavascriptの配列に変換する
	$cr = chr(0x0d) . chr(0x0a);

	$buff = 'var flist = [' . $cr;
	for($i = 0 ; $i < $flength ; $i++){
		$fname = basename($flist[$i]);
		$dname = dirname($flist[$i]);

		//ファイルの更新日時
		$timestamp = date("Y/m/d H:i:s",filemtime($flist[$i]));

		if($ftype == "image"){
			//exifから　lat lng 付加
			$filename = $dname . "/" . $fname;
			$LatLng = getExifLatLng($filename);
			$lat = $LatLng['lat'];
			$lng = $LatLng['lng'];
		}else{
			$lat = "";
			$lng = "";
		}
		//////////////////////

		//$buff .= '{ dir : "' . $dname . '", file : "' . $fname . '", timestamp : "' . $timestamp . '", lat : "' . $lat . '", lng : "' . $lng . '" }';

		//myfile の追加 ************************
		//ファイルリストから自分のファイルのみを表示する
		$id = $_SESSION[$USER_session];

		//先頭のIDと拡張子
		$FstPT = "/^" . $id . ".csv$/i";
		//先頭のIDと_とxxxxxと拡張子
		$SndPT = "/^" . $id . "_[0-9_.-a-z]*.csv$/i";

		if(preg_match($FstPT , $fname) || preg_match($SndPT , $fname)){
			$myfile = 1;
		}else{
			$myfile = 0;
		}
		//**************************************

		$buff .= '{ dir : "' . $dname . '", file : "' . $fname . '", myfile : "' . $myfile . '", timestamp : "' . $timestamp . '", lat : "' . $lat . '", lng : "' . $lng . '" }';


		if($i < ($flength - 1)){
			$buff .= ',';
		}
		$buff .= $cr;
	}
	$buff .= '];';


	//次のjobファイル
	print('var next_job = "' . $next . '";' . $cr );

	print('var this_dir = "' . $this_dir . '";' . $cr );

	//ファイルクラス(種別　event / map)の追加
	print('var file_class = "' . $file_class . '";' . $cr );
	//************

	print('var ftype    = "' . $ftype . '";' . $cr );
	print('var initkey  = "' . $key . '";' . $cr ); 

	print($buff . $cr);
	print('var flength  = ' . $flength . ';' . $cr);

	print('var multi = ' . $multi . ';' . $cr);

	//fields_File追加
	print('var fields_File = "' . $fields_File . '";' . $cr);

?>

function loadxml_init(){
	//ユーザログインのチェック
	//inputName
	UserLevel = 1;

	login_check();

	//ログインユーザーのみの機能
	if(inputName != ""){
		document.getElementById("LocalRead").style.display = "block";
		document.getElementById("username").value = inputName;
	}else{
		document.getElementById("username").value = "_unkhow";
	}

	//photouploader.php用
	document.getElementById("updir").value = this_dir;


	//ファイルの絞り込み
	var jkn = initkey;
	document.getElementById("keyword").value = jkn;

	//自分のファイル
	show_flist(jkn , "1");

	//自分以外のファイル
	show_flist(jkn , "0");


//アップロード画像のthumbnailをoutput listに表示する
document.getElementById('upload-form').addEventListener('change', handleFileSelect, false);

}

function show_flist(jkn , myfile){
	var buff = "";

	//jkn の　and 検索を検出
	var fjkn = jkn.split(" ");
	var fjkn_flg = 0;


	buff += '<table>';

	//image exif 対応
	if(multi && ftype == 'image'){
		buff += '<tr><td></td><td>';
		buff += '<input type="button" onClick="setMultiFile()" value="ファイル選択" />';
		buff += '</td></tr>';
	}

	var delf = 0;
	for(i = 0 ; i < flength ; i++){
		//myfile の切り分け
		if(flist[i]['myfile'] != myfile){
			continue;
		}

		fname = flist[i]['file'];
		dname = flist[i]['dir'];
		lat   = flist[i]['lat'];
		lng   = flist[i]['lng'];
		timestamp = flist[i]['timestamp'];

		/*
		if(fname.indexOf(jkn) != -1) {
			//strにhogeを含む場合の処理
		}else{
			continue;
		}
		*/

		//and検索	
		fjkn_flg = 1;
		for(s = 0 ; s < fjkn.length ; s++){
			if(fname.indexOf(fjkn[s]) != -1) {
				//strにhogeを含む場合の処理
			}else{
				fjkn_flg = 0;
			}
		}
		//条件が１個でもマッチしなければループを抜ける（非表示）
		if(fjkn_flg == 0){
			continue;
		}


		buff += '<tr><td>';

		if(ftype.indexOf("image") != -1){
			buff += '<img src="' + dname + '/' + fname + '" width="30" />';
		}
 
		//buff += '<a href="' + dname + '/' + fname + '" target="_blank">' + fname + '</a>';
		buff += '<a href="#" onClick="setFile(' + i + '); return false;">' + fname + '</a>';
		//buff += '</td><td>';
		//buff += timestamp;

		//downloadボタンの追加　******
		buff += '</td><td>';
		
		buff += '<input class="btn3" type="button" onClick="download(' + i + ');" value="ダウンロード">';

		//*************************


		//画像の場合　座標の有無表示
		if(ftype == 'image' && ( lat != 0 || lng != 0) ){
			buff += ' *';
		}

		//buff += '</td><td>';

		
		//ファイル登録ユーザは、自分のファイルの削除が可能
		//運用管理者以上は、すべてのファイルを削除可能
		delf = 0;
		if((fname.indexOf(inputName) != -1) && (inputName != "")){
			//fnameにinputNameを含む場合の処理
			delf = 1;
		}
		if(UserLevel > 1){
			delf = 1;
		}

		if(delf == 1){
			buff += '<input class="btn_negative" type="button" onClick="delFile(' + i + ');" value="削除" />';
			buff += '<input class="btn2" type="button" onClick="renFile(' + i + ');" value="ファイル名変更" />';
		}

		//複数の画像ファイル選択
		if(ftype == 'image' && multi){
			buff += '<input type="checkbox" name="multi_sel" id="multi_sel"  value="' + i + '" />';

		}else{		
			if(myfile == "1" || UserLevel > 1 ){
				buff += '<input type="button" onClick="setFile(' + i + ');" value="編集" />';
			}else{
				buff += '<input class="btn2" type="button" onClick="setFile(' + i + ');" value="見る" />';
			}
		}


		//buff += '<br />';
		buff += '</td></tr>';
	}
	buff += '</table>';

	if(myfile == 1){
		document.getElementById("myfilelist").innerHTML = buff;
	}else{
		document.getElementById("filelist").innerHTML = buff;
	}


	//ログイン状態が変化した場合callBackを実行する
	onChangeValue(window, 'inputName', callBack , 1000);
}

//csvファイルのダウンロード
function download(valNo){
	var dir   = flist[valNo]['dir'];
	var fname = flist[valNo]['file'];
	var path  = dir + "/" + fname;

	var link = "download.php?dir=" + dir + "&file=" + fname;

	//if($('input[name=fencode]:checked').val() === 'shiftjis'){
	if($('[name=fencode]').val() === 'shiftjis'){
  		link += "&enc=sjis";
	}
	//default : utf8
	
	location.href = link;
	//alert(link);
}

//シングルファイルの選択
function setFile(valNo){
	if(ftype == "image"){
		var buff = [];

		buff[0] = [];
		buff[0]['dir']   = flist[valNo]['dir'];
		buff[0]['fname'] = flist[valNo]['file'];
		buff[0]['lat']   = flist[valNo]['lat'];
		buff[0]['lng']   = flist[valNo]['lng'];

		if(!window.opener || window.opener.closed){
			//親ウィンドウが存在しない
　　　　			window.close();
		}else{
			if (typeof opener.loadMultiFile == "function")  {
				parent.opener.focus();
				opener.loadMultiFile( buff );

				window.close();
			}else{
				//関数が存在しない
				window.close();
			}
		}

	}else{
		var dir   = flist[valNo]['dir'];
		var fname = flist[valNo]['file'];
		var path  = dir + "/" + fname;

		//opener.loadFile( dir , fname );
		location.href = next_job + "?dir=" + dir + "&fname=" + fname;
  

		/*******
		if(!window.opener || window.opener.closed){
			//親ウィンドウが存在しない
　　　　			window.close();
		}else{
			if (typeof opener.loadFile == "function")  {
				parent.opener.focus();
				opener.loadFile( dir , fname );
			}else{
				//関数が存在しない
				window.close();
			}
		}
		*********/
	}
}

//複数ファイルの選択
function setMultiFile(){
	var emt = document.getElementsByName("multi_sel");

	var buff = [];

	var s = 0;
	for(var i = 0 ; i < emt.length ; i++){
		if(emt[i].checked){
			emtval = emt[i].value;

			buff[s] = [];
			buff[s]['dir']   = flist[emtval]['dir'];
			buff[s]['fname'] = flist[emtval]['file'];
			buff[s]['lat']   = flist[emtval]['lat'];
			buff[s]['lng']   = flist[emtval]['lng'];

			s++;
		}
	}

	if(s == 0){
		alert("ファイルが選択されていません");
		return;
	}

	if(!window.opener || window.opener.closed){
		//親ウィンドウが存在しない
　　　　		window.close();
	}else{
		if (typeof opener.loadMultiFile == "function")  {
			parent.opener.focus();
			opener.loadMultiFile( buff );

			window.close();
		}else{
			//関数が存在しない
			window.close();
		}
	}
}

//ファイル名の変更
function renFile(valNo){
	//現在のファイル情報
	var nowdir  = flist[valNo]['dir'];
	var nowfile = flist[valNo]['file'];

	//拡張子の取り出し
	var reg = /(.*)(?:\.([^.]+$))/;
	//file_name.match(reg)[0];	//demon_uploader.jpg
	//file_name.match(reg)[1];	//demon_uploader
	//file_name.match(reg)[2];	//jpg

	var fhead = nowfile.match(reg)[1];
	var ext = nowfile.match(reg)[2];


	//管理者の場合　旧ファイル名をベースにするか、自分のファイル名にベースにするか確認する。
	if( UserLevel >= 2){
		if(confirm("現在のファイル名をベースにしますか？")){
			var newname = fhead;
		}else{
			var newname = inputName + file_class;
		}
	}else{
		var newname = inputName + file_class; 
	}



	var flog =0;

	while(flog == 0){
		//var rename = prompt(inputName + "_*****." + ext + "（*****の部分のみ変更）", "");
		//var rename = prompt(inputName + file_class + "_*****." + ext + "（*****の部分のみ変更）", "");

		if(UserLevel == 1){
			var rename = prompt(newname  + "_*****." + ext + "（*****の部分のみ変更）", "");
			//キャンセル　または　文字入力なしで　処理中止
			if((rename == null) || (rename == "")) { return false; }
			//var renfile = inputName + file_class + "_" + rename + "." + ext;
			var renfile = newname + "_" + rename + "." + ext;
		}else{
			var newname = prompt(nowfile + "：管理者は自由に名前を変更できます", newname );
			//キャンセル　または　文字入力なしで　処理中止
			if((newname == null) || (newname == "")) { return false; }
			//var renfile = inputName + file_class + "_" + rename + "." + ext;
			var renfile = newname + "." + ext;

		}



		//ファイル名のチェック
		if(renfile.match( /^.*[(\\|/|:|\*|?|\"|<|>|\|)].*$/ )){
			//使えない文字が入っている
			alert("ファイル名に使えない文字が入っています");
		}else{
			//ループを抜ける
			flog = 1;
		}
	}

	if(confirm("本当に" + renfile + "に変更しますか？")){
	}else{
		return false;
	}


	//admin/renamefile.php の位置から見た dir　に補正する
	nowdir = "../" + nowdir;

	var send_data = { dir : nowdir , fname : nowfile , rname : renfile };

	//送信処理
	$.ajax({
		url: "admin/renamefile.php", // 送信先のPHP
		type: "POST", // POSTで送る
		async : false,	//同期通信

		data:send_data ,

		success : function(data, status, xhr) {

 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);


			//通信OK後、データベース処理でエラーとなった場合の確認処理が必要。
			if(data == renfile){
				//alert("ファイル名を　" + data + " に変更しました");
			}else if(data == "exists"){
				alert("同一のファイル名が存在します");
			}else{
				alert("名前の変更に失敗しました");
			}

			//リロード
			location.reload();

		} ,

		error : function(xhr, status, error) {
			// 通信失敗時の処理
        		console.log("error");
			console.log("status ="+status);
			console.log("xhr ="+xhr);
			alert("データが転送がエラーとなりました");
		}

	});

}

//ファイル削除
function delFile(valNo){

	if(confirm("本当に " + flist[valNo]['file'] + "ファイルを削除しますか？")){
	}else{
		return false;
	}


	//var send_data = { dir : flist[valNo]['dir'] , fname : flist[valNo]['file'] };
	var nowdir   = flist[valNo]['dir'];
	var filename = flist[valNo]['file'];
 
	//admin/deletefile.php の位置から見た dir　に補正する
	nowdir = "../" + nowdir;
	var send_data = { dir : nowdir , fname : filename };


	//送信処理
	$.ajax({
		url: "admin/deletefile.php", // 送信先のPHP
		type: "POST", // POSTで送る
		async : false,	//同期通信

		data:send_data ,

		success : function(data, status, xhr) {

 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			//通信OK後、データベース処理でエラーとなった場合の確認処理が必要。
			//alert(flist[valNo]['file'] + " ファイルを削除しました");

			//リロード
			location.reload();

		} ,

		error : function(xhr, status, error) {
			// 通信失敗時の処理
        		console.log("error");
			console.log("status ="+status);
			console.log("xhr ="+xhr);
			alert("データが転送がエラーとなりました");
		}
	});
}


//変数の変化を監視する関数
function onChangeValue(obj, nam, cbf, _tm){
  if(!_tm) _tm = 100;
  var f = function (o, n){
    var v = o[n];
    var t = setTimeout(
      function (){
        clearTimeout(t);
        if(v != o[n]){
          if(cbf(o, n)){
            f(o, n);
          }
        }else{
          f(o, n);
        }
      },
      _tm
    );
  }
  f(obj, nam);
}
//コールバック関数
function callBack(o, n){
	//alert(o[n]);

	//show_flist();
	//return true;
	location.reload();
} 

function flist_reflesh(){
	var jkn = document.getElementById("keyword").value;
	show_flist(jkn , "1");
	show_flist(jkn , "0");
}

//ファイルの新規作成
function NewFileMake(){

	//var fields_File の分解
	var pathinfo = fields_File.split('/');
	var filename = pathinfo[1].split('.');

	var fdir  = pathinfo[0];
	var basename = filename[0];
	var extname  = filename[1]; 

	//alert(fdir + " / " + basename + " / " + extname);

	//フィールドファイルの確認
	var result = checkFile( fdir , basename , extname);
	if(result){
		//alert(fields_File + "　が見つかりました");
	}else{
		alert(fields_File + "　がありません");
		return;			
	}


	//新規作成ファイルの確認（上書き）
	var outdir = this_dir;
	//var fhead  = inputName;
	var fhead  = inputName + file_class;

	switch(ftype){
		case "text/xml"   : fext = "xml"; break;
		case "text/csv"   : fext = "csv"; break;
	}


	//管理者の場合　自分のファイルを作成するか、フリーネームのファイルを作成するか確認する。
	if( UserLevel >= 2){
		var flg = 1
		var newname = fhead;
		while(flg == 1){
			newname = prompt("管理者はファイル名を自由に設定できます",newname);
			if(newname == null || newname ==""){ return false; }

			if(confirm(newname + "." + fext + " これでいいですか")){
				//alert(newname + "." + fext);
				fhead = newname;
				flg = -1;
			}
		}
	}
	

	var fname  = fhead + "." + fext;
	var result = checkFile( outdir , fhead , fext );

	if(result){
		if(confirm(fname + "は既に存在します。上書きしてよろしいですか？")){
		}else{
			//キャンセルして戻る
			alert("新規作成をキャンセルしました");
			return;			
		}
	}else{
		if(confirm(fname + "　を新規作成します")){
		}else{
			//キャンセルして戻る
			alert("新規作成をキャンセルしました");
			return;			
		}
	}


	//フィールドファイルのデータ読み込み・保存
	var data;
  	$.get(fields_File , function(data){
		//alert(fields_File + "\n" + data);
		//１行目のフィールドデータのみ抽出（正規表現の改行で分割）
		fieldData = data.split(/\r\n|\r|\n/);

		//テキストファイルの保存（１行目のみ）
		//var filename = saveFile( outdir , fhead , fext , data);
		var filename = saveFile( outdir , fhead , fext , fieldData[0]);
		alert(filename + " を新規に作成しました");

		location.reload();

	});
}

function LocalFileLoad(){

	if ( ftype.indexOf("text") != -1) {
		document.getElementById("textform").style.display  = "block";
		document.getElementById("imageform").style.display = "none";
		document.getElementById("Lexcec_bt").style.display = "none";
	}

	if ( ftype.indexOf("image") != -1) {
		document.getElementById("textform").style.display  = "none";
		document.getElementById("imageform").style.display = "block";
		document.getElementById("Iexcec_bt").style.display = "none";
	}

	document.getElementById("LocalText").style.display = "block";

	//************
	var obj1 = document.getElementById("selfile");

	//ダイアログでファイルが選択された時
	obj1.addEventListener("change",function(evt){

  		var file = evt.target.files;


		//選択ファイル情報
		document.getElementById("lfilename").value = file[0].name;
		document.getElementById("lfiletype").value = file[0].type;
		document.getElementById("lfilesize").value = file[0].size / 1024;

		//alert("ftype=" + file[0].type + " / " + ftype);

		//if(file[0].type == ftype　|| ftype == "" || file[0].name.indexOf(".csv")){
		if(file[0].type == ftype　|| (ftype == "" && file[0].name.indexOf(".csv")) || (file[0].type == "application/vnd.ms-excel" && file[0].name.indexOf(".csv"))){
			document.getElementById("Lexcec_bt").style.display = "block";

		}else{
			document.getElementById("Lexcec_bt").style.display = "none";

			alert(file[0].type + "です　" + ftype + "のファイルを選択してください");
			return false;
		}

  		//FileReaderの作成
  		var reader = new FileReader();

  		//テキスト形式で読み込む
		//readAsText 文字エンコード機能あり　デフォルトはUTF8
		//if($('input[name=fencode]:checked').val() === 'shiftjis'){
		if($('[name=upfencode]').val() === 'shiftjis'){
  			reader.readAsText(file[0],"Shift-JIS");
		}else{
  			reader.readAsText(file[0]);
		}
  
  		//読込終了後の処理
  		reader.onload = function(ev){
    			//テキストエリアに表示する
    			//document.test.txt.value = reader.result;

			//（データチェック）***********
			var ret = checkData( this_dir , reader.result );
			var obj = JSON.parse(ret);

			switch(obj.flg){
				case -1 ://チェック対象外は、そのまま読み込む
		    			document.test.txt.value = reader.result;
					break;
				case 0 ://エラーなしの場合、戻ったデータを読み込む
				case 1 ://修正済みの場合、戻ったデータを読み込む
    					document.test.txt.value = obj.data;
					break;
				case 2 ://ワーニングの場合、メッセージをつけて読み込む
				case 3 ://エラーの場合、メッセージをつけて読み込む
	    				document.test.txt.value = obj.msg + "\n" + obj.data;
					//アップロードボタンを表示しない	
					document.getElementById("Lexcec_bt").style.display = "none";

			}

			//alert(ret);

			//*********************			
  		}
	},false);



	//************
	var obj2 = document.getElementById("upfile");

	//ダイアログでファイルが選択された時
	obj2.addEventListener("change",function(evt){

  		var file = evt.target.files;


		//選択ファイル情報
		document.getElementById("lfilename").value = file[0].name;
		document.getElementById("lfiletype").value = file[0].type;
		document.getElementById("lfilesize").value = file[0].size / 1024;

		if(file[0].type.indexOf(ftype) != -1){
			document.getElementById("Iexcec_bt").style.display = "block";

		}else{
			document.getElementById("Iexcec_bt").style.display = "none";

			alert(ftype + "のファイルを選択してください");
			return false;
		}

	},false);
}

function LocalExcec(){
	var buff = document.getElementById("txt").value;

	var outdir = this_dir;
	//var fhead  = inputName;
	var fhead  = inputName + file_class;

	//var fext   = "txt";
	switch(ftype){
		case "text/xml"   : fext = "xml"; break;
		case "text/csv"   : fext = "csv"; break;
	}


	//管理者の場合　自分のファイルを作成するか、フリーネームのファイルを作成するか確認する。
	if( UserLevel >= 2){
		var flg = 1
		var newname = fhead;
		while(flg == 1){
			newname = prompt("管理者はファイル名を自由に設定できます",newname);
			if(newname == null || newname ==""){ return false; }

			if(confirm(newname + "." + fext + " これでいいですか")){
				//alert(newname + "." + fext);
				fhead = newname;
				flg = -1;
			}
		}
	}


	var indata = buff;


	//ファイルの存在確認
	var fname  = fhead + "." + fext;
	var result = checkFile( outdir , fhead , fext );

	if(result){
		if(confirm(fname + "は既に存在します。上書きして保存してよろしいですか？")){
		}else{
			//キャンセルして戻る
			return;			
		}
	}

	//テキストファイルの保存
	var filename = saveFile( outdir , fhead , fext , indata);
	alert(filename + " で保存しました");

	location.reload();
}

function LocalClose(cb){
	document.getElementById("LocalText").style.display = "none";

	if(cb){
		setTimeout(function(){
			cb();
		},500);
	}
}
//***************



//*****************
function handleFileSelect(evt) {
	var files = evt.target.files; // FileList object

	// Loop through the FileList and render image files as thumbnails.
	for (var i = 0, f; f = files[i]; i++) {

		// Only process image files.
		if (!f.type.match('image.*')) {
			continue;
		}

		var reader = new FileReader();

		// Closure to capture the file information.
		reader.onload = (function(theFile) {
			return function(e) {
				// Render thumbnail.
				var span = document.createElement('span');
				span.innerHTML = ['<img class="thumb" src="', e.target.result,
				'" title="', escape(theFile.name), '"/>'].join('');
				document.getElementById('list').insertBefore(span, null);
			};
		})(f);

		// Read in the image file as a data URL.
		reader.readAsDataURL(f);
	}
}


function uploader(form) {
	//upload中の表示
	document.getElementById("list").innerHTML = "";

	$form = $('#upload-form');
	fd = new FormData($form[0]);
	//fd = new FormData($form);
	$.ajax({
		url: 'photouploader2.php',
		type: 'post',

		async : false,	//同期通信
		processData: false,
		contentType: false,
		data: fd
		//dataType: 'json'
	}).done(function(data){
		console.log(data);

		//UpdataSet(data);
		data = data.replace("[","");
		data = data.replace("]","");

		var obj = JSON.parse(data);

		alert( obj.fname + "をアップロードしました");

	}).fail(function(XMLHttpRequest, textStatus, errorThrown) {
		alert( "ERROR" );
		alert( textStatus );
		alert( errorThrown );
	});

	//サブミット後にページをリロードしない
	location.reload();

	return false;
}


//ログインの確認
function login_check(){
	var loginId;
	var Idlevel;

	$.ajax({
		type: "POST",
		url: "admin/user_login_check.php",
		cache: false,
		async : false,	//同期通信
		//data: "sampleVal=test",
		success: function(ret){
			data = ret.split(",");
			loginId = data[0];
			Idlevel = data[1];
		}
	});

	if(loginId == null || loginId == ""){
		document.getElementById("inputName").value = "";
		inputName = "";
		UserLevel = 0;

		document.getElementById("login_bt").value = "ログイン";
		//document.getElementById("login_bt").style.visibility  = "visible";
		document.getElementById("logout_bt").style.visibility = "hidden";

		return false;
	}else{
		document.getElementById("inputName").value = loginId;
		inputName = loginId;
		UserLevel = Idlevel;

		document.getElementById("login_bt").value = inputName + " (" + UserLevel + ")";
		//document.getElementById("login_bt").style.visibility  = "visible";
		document.getElementById("logout_bt").style.visibility   = "visible";

		return true;
	}
}

function user_login(){
	if(inputName != ""){
		alert(inputName + "：ログイン済みです");
		return false;
	}



	//window.open('admin/log_in.php','log_in');
	location.href = 'admin/log_in.php';

	//document.getElementById("login_bt").value = inputName;
	//document.getElementById("logout_bt").style.visibility = "visible";
}

function user_logout(){
	if(inputName == ""){
		alert("ログインしていません");
		return false;
	}

	$.ajax({
		type: "POST",
		url: "admin/user_logout.php",
		async : false,	//同期通信
		cache: false,
		//data: "sampleVal=test",
		success: function(html){
			//samplefunc(html);
		}
	});
	

	alert(inputName + "：ログアウトしました");

	document.getElementById("inputName").value = "";
	inputName = "";

	document.getElementById("login_bt").value = "ログイン";
	document.getElementById("logout_bt").style.visibility = "hidden";

}


//テキストデータの個別エラーチェック
function checkData( outdir , indata ){

	//admin/checkdata.php の位置から見た dir　に補正する
	outdir = "../" + outdir;

	//**************************
	send_data = { dir : outdir , data : indata };

	//alert(outdir);
	//alert(indata);

	var result;
        // 送信処理
	$.ajax({
		url: "admin/checkdata.php", // 送信先のPHP
		type: "POST", // POSTで送る
		async : false,	//同期通信
		data:send_data ,
		success : function(data, status, xhr) {
 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			//処理した結果を返す
			result = data;

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

	return result;
}


//テキストファイルの存在チェック
function checkFile( outdir , fhead , fext){

	//admin/checkfile.php の位置から見た dir　に補正する
	outdir = "../" + outdir;

	//**************************
	//xml データをサーバーへ送信する
	send_data = { dir : outdir , header : fhead , ext : fext };

	//alert(send_data);

	var result;

        // 送信処理
	$.ajax({
		url: "admin/checkfile.php", // 送信先のPHP
		type: "POST", // POSTで送る
		async : false,	//同期通信
		data:send_data ,
		success : function(data, status, xhr) {
 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			//処理した結果を返す
			result = data;

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

	return result;

}

//テキストファイルの保存
function saveFile( outdir , fhead , fext , indata){

	//admin/savefile.php の位置から見た dir　に補正する
	outdir = "../" + outdir;

	//**************************
	//xml データをサーバーへ送信する
	send_data = { dir : outdir , header : fhead , ext : fext , data : indata };

	//alert(send_data);

	var sfilename = "";

        // 送信処理
	$.ajax({
		url: "admin/savefile.php", // 送信先のPHP
		type: "POST", // POSTで送る
		async : false,	//同期通信

		data:send_data ,

		success : function(data, status, xhr) {
 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			//保存したファイル名を返す
			sfilename = data;

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

	return sfilename;
}


</script>

<!--
</head>
<body onload="loadxml_init()">
-->

<!--ID:--><input type="hidden" size="10" id="inputName" value = "" />
<!--PW:--><input type="hidden" size="10" id="inputPass" value = "" />

<div style="display:none">
<input type="button" id="login_bt"  onClick="user_login();"  value="ログイン" />
<input type="button" id="logout_bt" onClick="user_logout();" value="ログアウト" style="visibility:hidden" />
</div>

<!--input type="button" id="close_bt"  onClick="window.close();" value="閉じる" /-->

<!--
<input type="button" id="close_bt"  onClick="location.href='admin/index.php'" value="indexに戻る" />
-->
<div id="data_create" class="sec">
	<h2>新しくデータを作成</h2>
	<div id="LocalRead" style="display:none">
		<?php //<input type="button" id="localf" onClick="LocalFileLoad()" value="csvファイルをアップロード" /> ?>
		<label class="btn btn2" for="selfile" id="localf" onClick="LocalFileLoad()">csvファイルをアップロード</label>

		<!--input type="button" id="makefile" onClick="NewFileMake()" value="新規作成" /--> 
		<input type="button" id="makefile" onClick="LocalClose(NewFileMake)" value="新規作成" /> 
	</div>

	<div id="LocalText" style="display:none"><div>
		<input type="hidden" id="lfilename" value="" style="display:none" />
		<input type="hidden" id="lfiletype" value="" style="display:none" />
		<input type="hidden" id="lfilesize" value="" style="display:none" />

		<div id="textform" style="display:none">
		<!-- テキストファイル-->
		<form name="test">
			<p class="info">ファイルを選択して、内容を確認してください</p>	
			<textarea name="txt" id="txt" rows="10" cols="100" wrap="off" readonly></textarea>
			<p id="upload_code"><span>文字化けする場合は、文字コードを変更してください。</span><label>文字コード<select id="upfencode" name="upfencode">
					<!--option value="auto" selected>自動</option-->
					<option value="utf8" selected>utf-8</option> 
					<option value="shiftjis">shift_jis</option> 
				</select>
			</label>
			</p>
			<div class="btns">
			<input class="btn btn_negative" type="reset" id="Lclose_bt" onClick="LocalClose()" value="キャンセル" />
			<label class="btn btn2"><span><span class="forPC">ファイルを</span>再選択</span><input type="file" id="selfile"></label>
			<!--input type="reset" onClick="LocalFileLoad()" value="クリア"-->
			<input type="button" id="Lexcec_bt" onClick="LocalExcec()" value="保存" />
			</div>
		</form>


		</div>

		<div id="imageform" style="display:none">
		<!-- 画像ファイル -->
		<form id="upload-form" method="post" enctype="multipart/form-data" onSubmit="return uploader(this);">

			<input type="button" id="Lclose_bt" onClick="LocalClose()" value="キャンセル" />
			<input type="file" name="upfile[]" id="upfile" multiple /><br />

			<output id="list"></output><br />

			<!-- photouploader への引き継ぎ情報 -->
			<input type="hidden" name="username" id="username" value="" />
			<input type="hidden" name="updir" id="updir" value="" />

			<input type="submit" id="Iexcec_bt" value="アップロード実行" /><br />
		</form>
		</div>

		<!--input type="button" id="Lclose_bt" onClick="LocalClose()" value="キャンセル" /-->
	</div></div> <!-- /#LocalText -->
</div>

<div id="data_lists" class="sec">
	<h2>データの編集・閲覧・ダウンロード</h2>

	<div id="data_actions">
		<div id="search_box">
			<input type="text" size="30" id="keyword" value="" placeholder="データ名でファイルを検索" />
			<input type="button" id="key_bt" onClick="flist_reflesh()" value="検索" /> 
			<?php //<input type="button" onClick="document.getElementById('keyword').value=''; flist_reflesh();" value="クリア" /><br /> ?>
		</div>
		<div id="code_box">
			<label>ダウンロードデータの文字コード</label>
			<select id="fencode" name="fencode">
				<option value="utf8">utf-8</option> 
				<option value="shiftjis" selected>shift_jis</option> 
			</select>
		</div>
	</div>
	<!--
	<input type="radio" id="fencode" name="fencode" value="utf8" >utf-8 
	<input type="radio" id="fencode" name="fencode" value="shiftjis" checked>shift_jis 
	-->
	<div>
		<h3>自分のデータ</h3>
		<div id="myfilelist" class="sec_body">
		</div>
	</div>
	<div>
		<h3>他のデータ</h3>
		<div id="filelist" class="sec_body">
		</div>
	</div>
</div>
<?php
common_menu(1);
?>
<?php include_once './admin/include_footer.php'; ?>

<?php
function getFileList($dir , $sort) {
	$files = glob(rtrim($dir, '/') . '/*');
	$list = array();
	foreach ($files as $file) {
		if (is_file($file)) {
			$list[] = $file;
		}
		if (is_dir($file)) {
			$list = array_merge($list, getFileList($file));
		}
	}

	//ファイル名のソート
	if($sort == "rsort"){
		rsort($list);
	}else{
		sort($list);
	}

	return $list;
}




//exif情報から、座標を取得する（未使用）
function getExifLatLng($filename){

	$latlng = array();

	//現在のレンタルサーバーでexifライブラリが有効になっていないため
	//$exif_data = exif_read_data($filename);

	return false;

	//**** 以下不動作 *****

        @$emake  = $exif_data['Make'];
        @$emodel = $exif_data['Model'];
        @$eexposuretime = $exif_data['ExposureTime'];
        @$efnumber = $exif_data['FNumber'];
        @$eiso  = $exif_data['ISOSpeedRatings'];
        @$edate = $exif_data['DateTime'];

	@$elat  = $exif_data['GPSLatitude'][0];
	@$elng  = $exif_data['GPSLongitude'][0];

	@$elat  = $exif_data['GPSLatitude'][0];
	@$elng  = $exif_data['GPSLongitude'][0];


	//print_r($exif_data['GPSLatitude']);
	//print("<br />");
	//print_r($exif_data['GPSLongitude']);

	$lat = 0;
	$lng = 0;

	if($elat != "" && $elng != ""){
		//緯度を60進数から10進数に変換
		//wgs84 世界測地系、ｇｐｓカメラ　exifを前提
		//例　度[0]: 36/1  分[1]:46/1  秒[2]:3666/100 or 36666/1000
		//$lat = $exif_data['GPSLatitude'][0] + ($exif_data['GPSLatitude'][1]/60) + (($exif_data['GPSLatitude'][2]/100)/3600);  //それぞれを分解
		$lat_do = array();	$lat_fun = array();	$lat_byou = array();
		$lat_do   = explode("/",$exif_data['GPSLatitude'][0]);
		if(count($lat_do) > 1){
			$lat = $lat_do[0] / $lat_do[1];
		}else{
			$lat = $lat_do[0];
		} 

		$lat_fun  = explode("/",$exif_data['GPSLatitude'][1]);
		if(count($lat_fun) > 1){
			$lat = $lat + ($lat_fun[0] / $lat_fun[1])/60;
		}else{
			$lat = $lat + $lat_fun[0]/60;
		}

		$lat_byou = explode("/",$exif_data['GPSLatitude'][2]);
		if(count($lat_byou) > 1){
			$lat = $lat + ($lat_byou[0] / $lat_byou[1])/3600;
		}else{
			$lat = $lat + $lat_byou[0]/3600;
		} 

 
		//南緯の場合はマイナスにする
		if($exif_data['GPSLatitudeRef']=='S'){ $lat *= -1; }
 
		//経度を60進数から10進数に変換
		//$lng = $exif_data['GPSLongitude'][0] + ($exif_data['GPSLongitude'][1]/60) + (($exif_data['GPSLongitude'][2]/100)/3600);

		$lng_do = array();	$lng_fun = array();	$lng_byou = array();
		$lng_do   = explode("/",$exif_data['GPSLongitude'][0]);
		if(count($lng_do) > 1){
			$lng = $lng_do[0] / $lng_do[1];
		}else{
			$lng = $lng_do[0];
		} 

		$lng_fun  = explode("/",$exif_data['GPSLongitude'][1]);
		if(count($lng_fun) > 1){
			$lng = $lng + ($lng_fun[0] / $lng_fun[1])/60;
		}else{
			$lng = $lng + $lng_fun[0]/60;
		}

		$lng_byou = explode("/",$exif_data['GPSLongitude'][2]);
		if(count($lng_byou) > 1){
			$lng = $lng + ($lng_byou[0] / $lng_byou[1])/3600;
		}else{
			$lng = $lng + $lat_byou[0]/3600;
		} 
 
		//西経の場合はマイナスにする
		if($exif_data['GPSLongitudeRef']=='W'){ $lng *= -1; }
 
		//出力
		//echo "{$lat},{$lng}";
	}

	$latlng['lat'] = $lat;
	$latlng['lng'] = $lng;

	return $latlng;
}
?>
