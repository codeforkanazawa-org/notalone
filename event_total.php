<?php
include_once("admin/include.php");

//error display
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 


//イベントファイルの一覧を表示する
//$flist   = getFileList("uploads/events");

/*if(isset($_GET['dir'])){
	$this_dir = $_GET['dir'];
}else{
	print("Access Error!!");
	exit();
}
*/

if(isset($_GET['ftype'])){
	$ftype = $_GET['ftype'];
}else{
	$ftype = "";
}


$this_dir = "uploads/events";
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
//$next = "" . $next;

$next = "admin/cont_event2.php";


//log_in.php からの戻り場所指定
//@$ReturnFile = $_SESSION['CallJob'];
$this_file = "../event_total.php";	//admin/からみたファイル位置
$_SESSION['CallJob'] = $this_file . "?dir=" . $this_dir . "&ftype=" . $ftype . "&key=" . $key;

//ログインユーザー名
$id = $_SESSION[$USER_session];


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

<title>イベントファイルの集計</title>

<!-- jquery ライブラリ -->
<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>

<script type="text/javascript">

<?php	

	//ファイル一覧をjavascriptの配列に変換する
	$cr = chr(0x0d) . chr(0x0a);

	$default = 1;	//集計対象 1、　非集計 0

	$buff = 'var flist = [' . $cr;
	for($i = 0 ; $i < $flength ; $i++){
		$fname = basename($flist[$i]);
		$dname = dirname($flist[$i]);

		//ファイルの更新日時
		$timestamp = date("Y/m/d H:m:s",filemtime($flist[$i]));

		//$buff .= '{ dir : "' . $dname . '", file : "' . $fname . '", select : ' . $default . ' , user : "' . $id . '" }';
		$buff .= '{ dir : "' . $dname . '", file : "' . $fname . '", timestamp : "' . $timestamp . '", select : "' . $default . '", user : "' . $id . '" }';

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

?>

function init(){
	//ユーザログインのチェック
	//inputName
	//UserLevel = 3;

	login_check();

	//*********
	AccsessLevel = 2;
	if(UserLevel < AccsessLevel){
		alert("あなたは、この処理を実施する権限はありません");
		return false;
	}else{
	//*********

		//コンテンツの表示
		$('#contener').css('display','block');


		//ファイルの絞り込み
		var jkn = initkey;
		document.getElementById("keyword").value = jkn;

		show_flist(jkn);
	}
}

function show_flist(jkn){

	var buff = "";

	//jkn の　and 検索を検出
	var fjkn = jkn.split(" ");
	var fjkn_flg = 0;


	buff += '<table>';

	var delf = 0;
	for(i = 0 ; i < flength ; i++){
		fname  = flist[i]['file'];
		dname  = flist[i]['dir'];
		select = flist[i]['select'];
		timestamp = flist[i]['timestamp'];

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


		//********

		if(select == 1){
			checkflg = 'checked';
		}else{
			checkflg = '';
		}

		buff += '<tr><td>';

		buff += '<input type="checkbox" id="select_' + i + '" onClick="selectChange(' + i + ')" ' + checkflg + ' />';

		//********

		buff += '</td><td>';

		buff += '<a href="' + dname + '/' + fname + '" target="_blank">' + fname + '</a>';

		buff += '</td><td>';
		buff += timestamp;

		buff += '</td><td>';

		buff += ' <input type="button" onClick="setFile(' + i + ');" value="ファイル選択" />';
		
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

//ファイル選択（チェック）関係
function selectChange(no){

	if(flist[no]['select'] == 1){
		flist[no]['select'] = 0;
	}else{
		flist[no]['select'] = 1;
	}
	return;
}

function allCheckOff(){
	for(var i = 0 ; i < flist.length ; i++){
		flist[i]['select'] = 0;
		$('#select_' + i).prop('checked', false);
	}
}

function allCheckOn(){
	for(var i = 0 ; i < flist.length ; i++){
		flist[i]['select'] = 1;
		$('#select_' + i).prop('checked', true);
	}
	return;
}

function eventTotal(){
	var count = 0;
	for(var i = 0 ; i < flist.length ; i++){
		if(flist[i]['select'] == 1){
			count ++;
		}
	}

	//****************

	if(confirm(count + "個のファイルを集計します")){
	}else{
		return false;
	}


	/*
	//var send_data = { dir : flist[valNo]['dir'] , fname : flist[valNo]['file'] };
	var nowdir   = flist[valNo]['dir'];
	var filename = flist[valNo]['file'];
 
	//admin/deletefile.php の位置から見た dir　に補正する
	nowdir = "../" + nowdir;
	var send_data = { dir : nowdir , fname : filename };
	*/

	//配列データをJSON文字列に変換
	var send_data = JSON.stringify(flist);

	//送信処理
	$.ajax({
		url: "admin/event_total_exec.php", // 送信先のPHP
		type: "JSON", // POSTで送る
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

			alert("データーが正常に転送されました");

		} ,

		error : function(xhr, status, error) {
			// 通信失敗時の処理
        		console.log("error");
			console.log("status ="+status);
			console.log("xhr ="+xhr);
			alert("データが転送がエラーとなりました");
		}
	});
	//*****************
}

//シングルファイルの選択
function setFile(valNo){
	var dir   = flist[valNo]['dir'];
	var fname = flist[valNo]['file'];
	var path  = dir + "/" + fname;

	//opener.loadFile( dir , fname );
	location.href = next_job + "?dir=" + dir + "&fname=" + fname;
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

	location.href = 'admin/log_in.php';
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

</head>
<body onload="init()">

<!--ID:--><input type="hidden" size="10" id="inputName" value = "" />
<!--PW:--><input type="hidden" size="10" id="inputPass" value = "" />
<input type="button" id="login_bt"  onClick="user_login();"  value="ログイン" />
<input type="button" id="logout_bt" onClick="user_logout();" value="ログアウト" style="visibility:hidden" />
<!--input type="button" id="close_bt"  onClick="window.close();" value="閉じる" /-->
<input type="button" id="close_bt"  onClick="location.href='admin/index.php'" value="indexに戻る" />
<br />

<hr />

<h3>イベントファイルの集計処理</h3>
（注意）当月以降のイベント集計ファイル（月別）を上書きします<br />
<div id="contener" style="display:none">

<input type="button" onClick="eventTotal()" value="集計を実行する" /><br />
<br />
 集計対象外のファイルは、チェックを外してください<br />
<input type="button" onClick="allCheckOff()" value="すべてのチェックを外す" />
<input type="button" onClick="allCheckOn()"  value="すべてにチェックを付ける" /><br />
<hr />

<input type="text" size="30" id="keyword" value="" />
<input type="button" id="key_bt" onClick="flist_reflesh()" value="and検索" /> 
<input type="button" onClick="document.getElementById('keyword').value=''" value="クリア" />

<div id="filelist">

</div>

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

?>
