<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 


include_once( "include.php");

$_SESSION['CallJob'] = "index_admin.php";
user_header("管理者メニュー" , "");

$UserLevel = Access_check(1,1,1,"index_admin.php");

if($UserLevel >= 1){
	print('<hr />[一般ユーザー　処理]<br />');
	print('<br />');

	print('<a href="../loadfiles.php?dir=uploads/events&class=evt&ftype=text/csv&key=&next=cont_event2.php">イベントファイル</a><br>');
	print('<a href="cont_location2.php">イベント開催場所</a><br>');
	print('<br />');
	print('<a href="../loadfiles.php?dir=uploads/mapinfo&class=map&ftype=text/csv&key=&next=cont_mapinfo2.php">マップファイル</a><br>');
	print('<br />');
}

if($UserLevel >=2){
	print('<hr />[管理ユーザー　処理]<br />');
	print('<br />');

	print('<a href="../event_total.php">イベントファイルの集計処理</a><br>');
	print('<br />');
	print('<a href="../loadfiles.php?dir=events&ftype=text/csv&fsort=rsort&key=&next=cont_events_admin2.php">イベント集計ファイル</a><br>');
	print('<br />');
	print('<br />');
	print('タイトル色別用<br />');
	print('<a href="cont_area2.php">地域ファイル</a>（通常）<br>');
	print('<a href="cont_target2.php">任意ファイル</a>（優先）<br>');
	print('<br />');
	print('<a href="../loadfiles.php?dir=mapinfo&class=map&ftype=text/csv&key=&next=cont_mapinfo2.php">マップファイル</a><br>');
	print('<br />');
	print('<a href="cont_categoryicon2.php">カテゴリ別アイコン</a><br>');
	print('<br />');
	print('<a href="cont_inquiry2.php">相談窓口</a><br>');
	print('<br />');
}

if($UserLevel >=3){
	print('<hr />[システム管理者　処理]<br />');
	print('<br />');
	print('個別ファイルのフィールド調整<br />');
	print('<a href="../loadfiles.php?dir=uploads/events&class=evt&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">イベントファイルの調整</a><br>');
	print('<a href="../loadfiles.php?dir=events&class=evt&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">イベント集計ファイルの調整</a><br>');
	print('<a href="../loadfiles.php?dir=mapinfo&class=map&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">マップファイルの調整</a><br>');
	//print('<a href="../opendata.php?dir=uploads/mapinfo&ftype=text/csv&key=&next=open_mapinfo.php">マップファイル（オープンデータ）</a><br>');
	print('<br />');

	print('<a href="../loadfiles.php?dir=localhost&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">共通ファイルのフィールド調整</a><br>');
	print('<br />');


	print('<a href="cont_user2.php">ユーザ管理</a><br>');
	print('<br />');
	print('<a href="cont_setting2.php">システム設定</a><br>');
}

common_menu(1);

?>
<?php include_once 'include_footer.php'; ?>

