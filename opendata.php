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
$this_file = "../opendata.php";	//admin/からみたファイル位置
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

<title>能登ノットアローン｜オープンデータ</title>

<link rel="stylesheet" href="css/notalone.css">

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
	show_flist();
}

function show_flist(){
	var buff = "";

	buff += '<table>';
	for(i = 0 ; i < flength ; i++){
		fname = flist[i]['file'];
		dname = flist[i]['dir'];
		lat   = flist[i]['lat'];
		lng   = flist[i]['lng'];


		buff += '<tr><td>';

		buff += '<a href="' + dname + '/' + fname + '" target="_blank">' + fname + '</a>';
		buff += '</td><td>';

		buff += ' <input type="button" onClick="setFile(' + i + ');" value="内容の確認" />';

		buff += '</td></tr>';
	}

	buff += '</table>';

	document.getElementById("filelist").innerHTML = buff;
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

//** notalone *****
var TopHeight = 30;				//トップメニューの縦幅
	$("#top-menu").height(TopHeight);
	$("#top-menu").css({"line-height" : TopHeight + "px"});


	//****for index.html *******
	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

//topメニュー
function top_index(){
	location.href = "../index.html";
}

//******************

</script>

</head>

<body onload="loadxml_init()">

<div id="top-menu" onClick="top_index()">　</div>
<div id="menu-back"></div>

<div id="info">
<h3>オープンデータ</h3>
<a href="http://creativecommons.jp/licenses/" target="_blank"><img src="/images/by.png"></a>

<hr />
・<a href="sub/map.html" target="_blank">マップ用</a>データファイル
<hr />
<ul>
<li>子育て支援関係の施設、機関等の位置情報を中心にしたデータセットです。</li>
<li>能登地域のデータは、プロジェクトNNA（能登ノットアローン）が独自に収集したものです。</li>
<li>データの収集には、輪島市、珠洲市のご協力をいただいています。</li>
<li>また、<a href="http://www.city.suzu.ishikawa.jp/soumu/opendata_index.html" target="_blank">珠洲市のオープンデータ</a>も一部取り込んでいます。</li>
<li>石川県内のデータは、<a href="http://www.i-oyacomi.net/prepass/page/opendata.php" target="_blank">（公財）いしかわ子育て支援財団のオープンデータ</a>を流用しています。</li>
<li>下記のファイルは、マップ用ファイルを直接公開していますので、ファイル名、ファイル構造など、逐次変更される場合があります。</li>
<li>このデータを活用される方は、ファイル名のリンクからダウンロードしてください。</li>
</ul>
<hr />

</div>

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

?>
