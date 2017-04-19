<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 


include_once( "include.php");

$_SESSION['CallJob'] = "index.php";

user_header("トップ");
$UserLevel = Access_check(1,0,1,"index.php");


//イベントファイルの検索
//
$dir = "../uploads/events/";

$files = glob(rtrim($dir, '/') . '/*');
$list = array();
foreach ($files as $file) {
	if (is_file($file)) { $list[] = $file; }
}

//ファイル名を昇順にソート
sort($list);

//ファイルリストから自分のファイルのみを表示する
$id = $_SESSION[$USER_session];

//先頭のIDと拡張子
$FstPT = "/^" . $id . ".csv$/i";
//先頭のIDと_とxxxxxと拡張子
$SndPT = "/^" . $id . "_[0-9_.-a-z]*.csv$/i";


/*
print($_SESSION[$USER_session] . "(" . $_SESSION[$LEVEL_session] . ")さんこんにちは！<br />\n");
print("前回ログイン：" . $_SESSION['LastLogin'] . "<br />\n");
print("ログイン数：" . $_SESSION['LoginCount'] . "回<br />\n");
print("この管理ページでは、データの登録・編集・閲覧・ダウンロードなどができます<br />\n");
print("<br />\n");
print('<a href="../loadfiles.php?dir=uploads/events&ftype=text/csv&key=&field=eventfields.csv&next=cont_event2.php">イベント情報</a><br>');
print("\n");
print("自分のデータ<br />\n");

print('<a href="cont_location2.php">イベント開催場所</a><br>');
print("\n");

print("<br />\n");
*/


?>
<p class="info"><?php echo $_SESSION[$USER_session]; ?>さんこんにちは！<?php if($UserLevel >=2){ ?><br>管理者権限でログインしています。<?php } ?></p>
<p>この管理ページでは、データの登録・編集・閲覧・ダウンロードなどが行えます。</p>
<div id="top_btns" class="btns">
	<!--a class="btn" href="../loadfiles.php?dir=uploads/events&ftype=text/csv&key=&field=eventfields.csv&next=cont_event2.php">イベント情報</a><a class="btn" href="../loadfiles.php?dir=uploads/mapinfo&ftype=text/csv&key=&next=cont_mapinfo2.php">公園・施設情報</a-->

	<a class="btn" href="../loadfiles.php?dir=uploads/events&class=evt&ftype=text/csv&key=&field=eventfields.csv&next=cont_event2.php">イベント情報</a><a class="btn" href="../loadfiles.php?dir=uploads/mapinfo&class=map&ftype=text/csv&key=&field=mapfields.csv&next=cont_mapinfo2.php">公園・施設情報</a>

</div>
<div class="sec">
	<h2>自分のデータ</h2>
	<div class="sec_body">
		<ul class="data_list">
		<?php 
		for($i=0 ; $i<count($list) ; $i++){
			$filename = basename($list[$i]);
			if(preg_match($FstPT , $filename) || preg_match($SndPT , $filename)){
				//print(basename($list[$i]));
				print('<li><span>イベント：</span><a href="cont_event2.php?dir=uploads/events&fname=' . $filename . '">' . $filename . '</a></li>');
			}
		}
		?>
		</ul>
	</div>
</div>
<?php if($UserLevel >=2){ ?>
	<btns><a href="index_admin.php">管理者メニュー</a></btns>
<?php } //if($UserLevel >=2) ?>

<?php 
common_menu(2);
?>
<?php include_once 'include_footer.php'; ?>

