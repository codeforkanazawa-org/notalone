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

if(isset($_GET['ftype'])){
	$ftype = $_GET['ftype'];
}else{
	$ftype = "";
}

$dir  = $this_dir;
$dir .= "/";
$flist = getFileList($dir);
$flength = count($flist);

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
$next = "admin/" . $next;


//log_in.php からの戻り場所指定
//@$ReturnFile = $_SESSION['CallJob'];
$this_file = "../loadfiles.php";	//admin/からみたファイル位置
$_SESSION['CallJob'] = $this_file . "?dir=" . $this_dir . "&ftype=" . $ftype . "&key=" . $key;



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


?>
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
<!--script type="text/javascript" src="js/myScript.js"></script-->

<!-- jquery ライブラリ -->
<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>

<script type="text/javascript">

<?php	

	//ファイル一覧をjavascriptの配列に変換する
	$cr = chr(0x0d) . chr(0x0a);

	$buff = 'var flist = [' . $cr;
	for($i = 0 ; $i < $flength ; $i++){
		$fname = basename($flist[$i]);
		$dname = dirname($flist[$i]);

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

		$buff .= '{ dir : "' . $dname . '", file : "' . $fname . '", lat : "' . $lat . '", lng : "' . $lng . '" }';

		if($i < ($flength - 1)){
			$buff .= ',';
		}
		$buff .= $cr;
	}
	$buff .= '];';


	//次のjobファイル
	print('var next_job = "' . $next . '";' . $cr );

	print('var this_dir = "' . $this_dir . '";' . $cr );
	print('var ftype    = "' . $ftype . '";' . $cr );
	print('var initkey  = "' . $key . '";' . $cr ); 

	print($buff . $cr);
	print('var flength  = ' . $flength . ';' . $cr);

	print('var multi = ' . $multi . ';' . $cr);

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

	show_flist(jkn);

//アップロード画像のthumbnailをoutput listに表示する
document.getElementById('upload-form').addEventListener('change', handleFileSelect, false);

}

function show_flist(jkn){
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
		fname = flist[i]['file'];
		dname = flist[i]['dir'];
		lat   = flist[i]['lat'];
		lng   = flist[i]['lng'];

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

		buff += '<a href="' + dname + '/' + fname + '" target="_blank">' + fname + '</a>';

		//画像の場合　座標の有無表示
		if(ftype == 'image' && ( lat != 0 || lng != 0) ){
			buff += ' *';
		}

		buff += '</td><td>';

		//複数の画像ファイル選択
		if(ftype == 'image' && multi){
			buff += ' <input type="checkbox" name="multi_sel" id="multi_sel"  value="' + i + '" />';

		}else{		
			buff += ' <input type="button" onClick="setFile(' + i + ');" value="ファイル選択" />';
		}

		
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
			buff += ' <input type="button" onClick="renFile(' + i + ');" value="名前変更" />';
			buff += ' <input type="button" onClick="delFile(' + i + ');" value="削除" />';
		}

		//buff += '<br />';
		buff += '</td></tr>';
	}
	buff += '</table>';

	document.getElementById("filelist").innerHTML = buff;


	//ログイン状態が変化した場合callBackを実行する
	onChangeValue(window, 'inputName', callBack , 1000);
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

	var ext = nowfile.match(reg)[2];

	var flog =0;

	while(flog == 0){
		var rename = prompt(inputName + "_*****." + ext + "（*****の部分のみ変更）", "");

		//キャンセル　または　文字入力なしで　処理中止
		if((rename == null) || (rename == "")) { return false; }

		var renfile = inputName + "_" + rename + "." + ext;

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
	show_flist(jkn);
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
		if(file[0].type == ftype　|| (ftype == "" && file[0].name.indexOf(".csv"))){
			document.getElementById("Lexcec_bt").style.display = "block";

		}else{
			document.getElementById("Lexcec_bt").style.display = "none";

			alert(ftype + "のファイルを選択してください");
			return false;
		}

  		//FileReaderの作成
  		var reader = new FileReader();
  		//テキスト形式で読み込む
  		reader.readAsText(file[0]);
  
  		//読込終了後の処理
  		reader.onload = function(ev){
    			//テキストエリアに表示する
    			document.test.txt.value = reader.result;
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
	var fhead  = inputName;

	//var fext   = "txt";
	switch(ftype){
		case "text/xml"   : fext = "xml"; break;
		case "text/csv"   : fext = "csv"; break;
	}

	var indata = buff;

	//テキストファイルの保存
	var filename = saveFile( outdir , fhead , fext , indata);

	alert(filename + " をアップロードしました");

	location.reload();
}

function LocalClose(){
	document.getElementById("LocalText").style.display = "none";
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

<style>
  .thumb {
    height: 150px;
    border: 1px solid #000;
    margin: 10px 5px 0 0;
  }
</style>

</head>
<body onload="loadxml_init()">

<!--ID:--><input type="hidden" size="10" id="inputName" value = "" />
<!--PW:--><input type="hidden" size="10" id="inputPass" value = "" />
<input type="button" id="login_bt"  onClick="user_login();"  value="ログイン" />
<input type="button" id="logout_bt" onClick="user_logout();" value="ログアウト" style="visibility:hidden" />
<!--input type="button" id="close_bt"  onClick="window.close();" value="閉じる" /-->
<input type="button" id="close_bt"  onClick="location.href='admin/index.php'" value="indexに戻る" />
<br />

<div id="LocalRead" style="display:none">
	<hr />
	<input type="button" id="localf" onClick="LocalFileLoad()" value="ローカルのファイルをアップロード" />
</div>

<div id="LocalText" style="display:none">

	<input type="hidden" id="lfilename" value="" style="display:none" />
	<input type="hidden" id="lfiletype" value="" style="display:none" />
	<input type="hidden" id="lfilesize" value="" style="display:none" />

	<div id="textform" style="display:none">
	<!-- テキストファイル-->
	<form name="test">
		<input type="file" id="selfile"><br>
		

		<textarea name="txt" id="txt" rows="10" cols="100" readonly></textarea>
	</form>
	<input type="button" id="Lexcec_bt" onClick="LocalExcec()" value="アップロード実行" /><br />
	</div>

	<div id="imageform" style="display:none">
	<!-- 画像ファイル -->
	<form id="upload-form" method="post" enctype="multipart/form-data" onSubmit="return uploader(this);">
		<input type="file" name="upfile[]" id="upfile" multiple /><br />

		<output id="list"></output><br />

		<!-- photouploader への引き継ぎ情報 -->
		<input type="hidden" name="username" id="username" value="" />
		<input type="hidden" name="updir" id="updir" value="" />

		<input type="submit" id="Iexcec_bt" value="アップロード実行" /><br />
	</form>
	</div>

	<input type="button" id="Lclose_bt" onClick="LocalClose()" value="中止／閉じる" />
</div> 

<hr />

　ファイルを選択してください<br />
（アップロード、削除、名前変更は、ログインが必要です）<br />

<input type="text" size="30" id="keyword" value="" />
<input type="button" id="key_bt" onClick="flist_reflesh()" value="and検索" /> 
<input type="button" onClick="document.getElementById('keyword').value=''" value="クリア" />

<div id="filelist">

</div>

</body>
</html>


<?php
function getFileList($dir) {
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

	//ファイル名の降順にソート
	rsort($list);

	return $list;
}




//exif情報から、座標を取得する
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
