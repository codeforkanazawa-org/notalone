<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

include_once("include.php");

	
	//loadfiles.phpからpost送信されるデータ
	$dir    = $_POST['dir'];
	$data   = $_POST['data'];
	
	/*
	//単独テストの時は、下記にエラーデータを設定
	$dir    = "../uploads/events";
	$data   = "
no,eventtitle,where,place,whom,what,who,contact,fee,when,openTime,closeTime,tag1,url
1,抱っこの教室,能登町瑞穂公民館,,妊婦　乳幼児の保護者　興味のある方,本当に気持ちのいい抱っこは何かを感じ、体験し、考える講座,まるまる育児in能登町,https://m.facebook.com/nozomi.ootaka,3500円,2016/11/26,10:00:00,12:00:00,能登町,
2,まるまる育児　赤ちゃんの運動発達講座,能登町瑞穂公民館,１階和室,妊婦　乳幼児の保護者　興味のある方,赤ちゃんの新生児期からお座り時期までの発達などを学びます。,まるまる育児in能登町,https://m.facebook.com/nozomi.ootakaまたはnzm.o_o.rei@docomo.ne.jp,5000,2016/12/17,10:00:00,12:00:00,能登町,
";
	*/	


	//イベントファイルのデータをチェックする
	if($dir == "../uploads/events"){

		//**イベントファイルのフィールド定義
		//localhost/eventfields.csv
		//１行目がフィールド名
		//２行目がフィールド名の日本語表記（オプション）
		//３行目がフィールドの表示長さ（オプション）
		//include.php内に　csvDatabaseRead()関数あり
		//	type = 0 連想配列で読み出す
		//$arrayData = csvDatabaseRead("../localhost/eventfields.csv" , 0);
		//print_r($arrayData);
		/*
		//結果
		Array (
		[0] => Array ( [0] => no [1] => when [2] => eventtitle [3] => openTime [4] => closeTime [5] => where [6] => place [7] => whom [8] => what [9] => who [10] => contact [11] => fee [12] => url [13] => tag1 )
 
		[1] => Array ( [no] => No [when] => 開催日 [eventtitle] => イベントタイトル [openTime] => 開始時間 [closeTime] => 終了時間 [where] => 施設名 [place] => 場所名 [whom] => 対象者 [what] => 内容 [who] => 主催者 [contact] => 連絡先 [fee] => 参加費 [url] => ホームページ [tag1] => 任意区分 )

		[2] => Array ( [no] => 50 [when] => 100 [eventtitle] => 200 [openTime] => 80 [closeTime] => 90 [where] => 150 [place] => 120 [whom] => 150 [what] => 300 [who] => 200 [contact] => 150 [fee] => 50 [url] => 150 [tag1] => 100 ) )
		*/



		//**csvファイルの構造
		//１行目がフィールド名
		//２行目以降がデータ


		//**チェック項目
		//csvファイルのフィールド名が、定義と一致しているか
		//順序は違っていてもOK。欠損していてもOK。
		//定義されていないフィールドがあるとNG。


		//**csvデータは、フィールド名をキーにした連想配列として読み込む

			//when 日付のチェック（必須）
			//日付の表記違いは、修正する
			//空白等のミスデータはエラーとする
			
			//openTime 時間チェック（必須）
			//時間の表記違いは、修正する
			//空白等のミスデータはエラーとする

			//closeTime 時間チェック
			//時間の表記違いは、修正する
			//空白等のミスデータはワーニングとする

		//**結果 をjsonで返す
		//0:エラーなし、1:修正済み、2:ワーニングあり、3:エラーあり
		$flg = 0;

		//メッセージ　１行ごとのチェック内容を示す
		$msg = "エラー、ワーニング内容";

		//処理結果データ
		//$data
		
		$result = array("flg" => $flg , "msg" => $msg , "data" => $data);

		//******
		echo json_encode( $result );	//loadfiles.phpからの呼び出し用
		//print_r( $result );		//単独テスト確認用
		//*******

		exit();
	}
	

	//チェック対象外は flg = false(-1)
	echo json_encode( array( "flg" => -1 ));
?>
