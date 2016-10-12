<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 


include_once( "include.php");


user_header("のとノットアローン index");


?>
<br />
<a href="../loadfiles.php?dir=uploads/events&ftype=text/csv&key=&next=cont_event2.php">イベントファイル</a><br>
<a href="../loadfiles.php?dir=uploads/events&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">イベントファイルの調整</a><br>
　　<a href="../event_total.php">イベントファイルの集計処理</a><br>
<br />
<a href="../loadfiles.php?dir=events&ftype=text/csv&key=&next=cont_events_admin2.php">イベント集計ファイル</a><br>
<a href="../loadfiles.php?dir=events&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">イベント集計ファイルの調整</a><br>
<br />
<a href="cont_location2.php">イベント開催場所</a><br>
<br />
タイトル色別用<br />
　　<a href="cont_area2.php">地域ファイル</a>（通常）<br>
　　<a href="cont_target2.php">任意ファイル</a>（優先）<br>
<br />
<hr />
<a href="../loadfiles.php?dir=uploads/mapinfo&ftype=text/csv&key=&next=cont_mapinfo2.php">マップファイル</a><br>
<a href="../loadfiles.php?dir=uploads/mapinfo&ftype=text/csv&key=&next=cont_csvdatacoordinator2.php">マップファイルの調整</a><br>
<br />
<!-- a href="../opendata.php?dir=uploads/mapinfo&ftype=text/csv&key=&next=open_mapinfo.php">マップファイル（オープンデータ）</a><br -->
<br />
<br />
<a href="cont_categoryicon2.php">カテゴリ別アイコン</a><br>
<hr />
<a href="cont_inquiry2.php">相談窓口</a><br>
<br />
<hr />
<a href="cont_user2.php">ユーザ管理</a><br>
<br />
<a href="cont_setting2.php">システム設定</a><br>
<br />

<br>
<br>

<a href="../index.html">終了</a><br>
<br>

</body>
</html>

