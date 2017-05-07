<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

include_once("include.php");

		
	//loadfiles.phpからpost送信されるデータ
	$dir    = $_POST['dir'];
	$data   = $_POST['data'];
	//改行文字列
	$cr = chr(0x0d) . chr(0x0a);
	
/*	
	//単独テストの時は、下記にエラーデータを設定
	$dir    = "../uploads/events";

	$data   = "no,eventtitle,where,place,whom,what,who,contact,fee,when,openTime,closeTime,tag1,url
1,抱っこの教室,能登町瑞穂公民館,,妊婦　乳幼児の保護者　興味のある方,本当に気持ちのいい抱っこは何かを感じ、体験し、考える講座,まるまる育児in能登町,https://m.facebook.com/nozomi.ootaka,3500円,2016/1/9,10:00,12:0:00,能登町,
2,まるまる育児　赤ちゃんの運動発達講座,能登町瑞穂公民館,１階和室,妊婦　乳幼児の保護者　興味のある方,赤ちゃんの新生児期からお座り時期までの発達などを学びます。,まるまる育児in能登町,https://m.facebook.com/nozomi.ootakaまたはnzm.o_o.rei@docomo.ne.jp,5000,2011/10/1,19:9:9,13:1:1,能登町,";

$data = "no,when,eventtitle,openTime,closeTime,where,place,whom,what,who,phone,email,contact,fee,url,tag1,uid1,2016/11/5,イベント名,13:30:00,16:00:00,珠洲市総合病院,,妊婦　乳幼児とその保護者,内容について,主催者,,,連絡先,0,,,2,2017/3/16,育児サークル　すまいりんはぐ,10:30:00,12:00:00,珠洲市総合病院,２階　スマイルルーム,乳幼児とそのお母さん　どなたでも,お母さんのおしゃべりの場です　助産師・看護師も参加します,育児サークル　すまいるはぐ,,,0768826652 珠洲市総合病院2F東病棟,0,,,3,2017/4/20,育児サークル　すまいりんはぐ,10:30:00,12:00:00,珠洲市総合病院,２階　スマイルルーム,乳幼児とそのお母さん　どなたでも,お母さんのおしゃべりの場です　助産師・看護師も参加します,育児サークル　すまいるはぐ,,,0768826652 珠洲市総合病院2F東病棟,0,,,evt58ef847a4114,2017/4/25,育児サークル　すまいりんはぐ,10:30:00,12:00:00,珠洲市総合病院,２階　スマイルルーム,乳幼児とそのお母さん　どなたでも,お母さんのおしゃべりの場です　助産師・看護師も参加します,育児サークル　すまいるはぐ,,,0768826652 珠洲市総合病院2F東病棟,0,,,evt58ef847a411";

	$cr = "<br />";
	
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
		$fieldsData = array();
		$fieldsData = csvDatabaseRead("../localhost/eventfields.csv" , 0);


		//print_r($fieldsData);
		//print("<br />arrayData:<br />");

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

		//**csvデータは、フィールド名をキーにした連想配列に変換する
		$dataArray = array();
		$dataArray = csvTextToArray($data);

		//print_r($dataArray);		

		//初期化
		$error = 0;	//エラー数
		$warning = 0;	//ワーニング数
		$edit = 0;	//修正数
		$msg = "";	//エラー等の内容

		//0:エラーなし、1:修正済み、2:ワーニングあり、3:エラーあり
		//3:致命的な問題がありデータ処理が不可能なもの
		//2:問題はあるがデータ処理が可能なもの
		//1:問題箇所を修正できるもの

 		for($s = 0 ; $s < count($dataArray[0]) ; $s++){
			//**チェック項目
			//csvファイルのフィールド名が、定義と一致しているか
			//順序は違っていてもOK。欠損していてもOK。
			//定義されていないフィールドがあるとNG。

			$check = 0;
			$dkey = $dataArray[0][$s];

			for($i = 0; $i < count($fieldsData[0]) ; $i++){
				$fkey = $fieldsData[0][$i];

				if($dkey === $fkey){
					$check = 1;
					//print($dvalue . ":hit<br />");
					break;
				}
			}

			if($check == 0){
				//print($dvalue . "：フィールド名が不正です<br />");
				$msg .= "エラー：" . $dataArray[0][$s] . " のフィールド名が不正です" . $cr;
				$error++;
			}
		}

		//フィールドにエラーが無い場合、データのチェックを実行
		if($error == 0){

		//csvデータは　１から
		for($i = 1 ; $i < count($dataArray) ; $i++){

			//開催日のチェック
			$fkey = 'when';
			$fname = $fieldsData[1][$fkey];
			$souce = $dataArray[$i][$fkey];
			list($dataArray[$i][$fkey],$ierror,$iwarning,$iedit,$imsg) = checkDateFormat(strval($i) , $fname , $souce);
			$error = $error + $ierror;
			$warning = $warning + $iwarning;
			$edit  = $edit + $iedit;
			$msg .= $imsg;
		

			//開催時間のチェック
			$ofkey = 'openTime';
			$ofname = $fieldsData[1][$ofkey];
			$souce = $dataArray[$i][$ofkey];
			list($dataArray[$i][$ofkey],$ierror,$iwarning,$iedit,$imsg) = checkTimeFormat(strval($i) , $fname , $souce);
			$error = $error + $ierror;
			$warning = $warning + $iwarning;
			$edit  = $edit + $iedit;
			$msg .= $imsg;

			$openTime = "";
			if($ierror == 0 && $iwarning == 0){
				$openTime = $dataArray[$i][$ofkey];
			}

			//終了時間のチェック
			$cfkey = 'closeTime';
			$cfname = $fieldsData[1][$cfkey];
			$souce = $dataArray[$i][$cfkey];
			list($dataArray[$i][$cfkey],$ierror,$iwarning,$iedit,$imsg) = checkTimeFormat(strval($i) , $fname , $souce);
			$error = $error + $ierror;
			$warning = $warning + $iwarning;
			$edit  = $edit + $iedit;
			$msg .= $imsg;

			$closeTime = "";
			if($ierror == 0 && $iwarning == 0){
				$closeTime = $dataArray[$i][$cfkey];
			}

			//openTime > closeTimeのチェック
			if($openTime != "" && $closeTime != ""){
				if(strtotime($openTime) > strtotime($closeTime)){
					$error++;
					$msg .= "エラー：" . strval($i) . "件目の"
					 . $ofname . "(" . $openTime . ")と"
					 . $cfname . "(" . $closeTime . ")が"
					 . "矛盾しています" . $cr;
				}
			}

			//uidのチェック
			$fkey = 'uid';
			$fname = $fieldsData[1][$fkey];


			//uid フィールドが存在する場合
			if(isset($dataArray[$i][$fkey])){
				$souce = $dataArray[$i][$fkey];
				if($souce == ""){
					//新しいUIDを発行
					$dataArray[$i][$fkey] = makeUID("evt" , 3);
					$msg .= "修正：" . strval($i) . "件目の"
						. $fname . "を(" . $dataArray[$i][$fkey] 
						. ")に設定しました". $cr;
					$edit++;	
				}else{
					//UIDの重複チェック（重複していた場合、自分を修正する）
					for($s = 1 ; $s < count($dataArray) ; $s++){
						if($i == $s) continue;
						if($souce == $dataArray[$s][$fkey]){
							//新しいUIDを発行
							$dataArray[$i][$fkey] = makeUID("evt" , 3);
							$msg .= "修正：" . strval($i) . "件目の"
								. $fname . "を(" . $dataArray[$i][$fkey]
								. ")に変更しました" .$cr;
							$edit++;
							break;
						}
					}
				}
			}

		}

		}	//end if


		//**結果 をjsonで返す
		$flg = 0;
		if($error > 0){
			$flg = 3;
		}else if($warning > 0){
			$flg = 2;
		}else if($edit > 0){
			$flg = 1;
		}

		$retmsg = "";
		if($error > 0){
			$retmsg .= strval($error) . "箇所のエラーがあります" .$cr;
		}
		if($warning > 0){
			$retmsg .= strval($warning) . "箇所のエラーがあります" .$cr;
		}
		if($edit > 0){
			$retmsg .= strval($edit) . "箇所の修正があります" .$cr;
		}

		$msg = $retmsg . $msg;

		
		//0:エラーなし、1:修正済み、2:ワーニングあり、3:エラーあり
		//$flg = 0;

		//メッセージ　１行ごとのチェック内容を示す
		//$msg = "エラー、ワーニング内容";

		//処理結果データ
		//$data
		//連想配列をｃｓｖテキストに変換
		$data = arrayToCsvText($dataArray);

		//$flg=3;	//デバッグ用　強制的にメッセージを表示させる

		$result = array("flg" => $flg , "msg" => $msg , "data" => $data);

		//******
		echo json_encode( $result );	//loadfiles.phpからの呼び出し用
		//print_r( $result );		//単独テスト確認用
		//*******

		exit();
	}
	

	//チェック対象外は flg = false(-1)
	echo json_encode( array( "flg" => -1 ));




//uidの作成
//var uidHead , var uidClass 
//uidをランダム発生
function makeUID($class , $keta){
	//$class : evt / map
	//$keta  : 付加するランダム英数の桁数
	//uidの標準形式 = class + 16進の現在年月日 + ランダム英数

  	//文字定義
  	$moji  = "0123456789";
  	//$moji .= "abcdefghijklmnopqrstuvwxyz";
  	//$moji .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

	$array_moji = str_split($moji);
 
    	$uid = "";
    	//ランダムIDの生成
      	for($j = 0 ; $j < $keta ; $j++){
       		$uid .= $array_moji[rand(0 , count($array_moji) -1)];
      	}

	//UID の形式
	$timestamp = time();
	$uidHead = dechex($timestamp);

	$uid = $class . $uidHead . $uid;

	return $uid;
}


//function chckdate()	php標準関数

//日付の形式を整える
function checkDateFormat($line , $fname , $souce){
	//global $error;
	//global $warning;
	//global $edit;
	//global $msg;

	$error = 0;
	$warning = 0;
	$edit = 0;
	$msg = "";

	global $cr;


	//when 日付のチェック（必須）
	//日付の表記違いは、修正する
	//空白等のミスデータはエラーとする
 
	if(isset($souce)){

		//半角のみ許可（数値と/）
		if (preg_match("/^[\/0-9]+$/", $souce)){

			$buff = explode("/", $souce);
			if(count($buff) == 3){
				//checkdate 月,日,年　の並び
				if (checkdate($buff[1], $buff[2], $buff[0]) === true) {
					//$buff[0]
					if(strlen($buff[1]) == 1){
						$buff[1] = "0" . $buff[1];
						$msg .= "修正：" . $line . "件目の"
 							. $fname . "(" . $souce . ")"
							. "の月を２桁(" . $buff[1]
							. ")にしました" . $cr;
						$edit++;
					}
					if(strlen($buff[2]) == 1){
						$buff[2] = "0" . $buff[2];
						$msg .= "修正：" . $line . "件目の"
							. $fname . "(" . $souce . ")"
							. "の日を２桁(" . $buff[2]
							. ")にしました" . $cr;
						$edit++;
					}
					//チェック後のデータを元配列に戻す
					$souce = $buff[0] . "/" . $buff[1] . "/" . $buff[2];

				}else {
					$error++;
					$msg .= "エラー：" . $line . "件目の"
						. $fname . "の日付(" . $souce
						. ")は存在しません" . $cr;
				}
			}else{
				$error++;
				$msg .= "エラー：" . $line . "件目の"
					. $fname . "の日付(" . $souce
					. ")は不正な形式です" . $cr;
			}


		}else{
			$error++;
			$msg .= "エラー：" . $line . "件目の"
				. $fname . "の日付(" . $souce
				. ")は不正な文字列です" . $cr;
		}
	}else{
		$error++;
		$msg .= "エラー：" . $line . "件目に"
			. $fname . "データがありません" . $cr;
	}

	//return $souce;
	return array($souce,$error,$warning,$edit,$msg);
}



//時刻のチェック
function checktime($hour, $min, $sec) {
     if ($hour < 0 || $hour > 23 || !is_numeric($hour)) {
         return false;
     }
     if ($min < 0 || $min > 59 || !is_numeric($min)) {
         return false;
     }
     if ($sec < 0 || $sec > 59 || !is_numeric($sec)) {
         return false;
     }
     return true;
}

//時間の形式を整える
function checkTimeFormat($line , $fname , $souce){
	//global $error;
	//global $warning;
	//global $edit;
	//global $msg;

	$error = 0;
	$warning = 0;
	$edit = 0;
	$msg = "";

	global $cr;


	//openTime 時間チェック（必須）
	//時間の表記違いは、修正する
	//空白等のミスデータはエラーとする

	if(isset($souce)){

		//半角のみ許可（数値と:）
		if (preg_match("/^[:0-9]+$/", $souce)){

			$buff = explode(":", $souce);
			if(count($buff) >= 2){
				if(!isset($buff[2])){
					$buff[2] = "00";
				}

				//checktime
				if (checktime($buff[0], $buff[1], $buff[2]) === true) {
					if(strlen($buff[0]) == 1){
						$buff[0] = "0" . $buff[0];
						$msg .= "修正：" . $line . "件目の"
							. $fname . "(" . $souce . ")"
							. "の時を２桁(" . $buff[0] . ")にしました" . $cr;
						$edit++;
			
					}
					if(strlen($buff[1]) == 1){
						$buff[1] = "0" . $buff[1];
						$msg .= "修正：" . $line . "件目の"
							. $fname . "(" . $souce . ")"
							. "の分を２桁(" . $buff[1] . ")にしました" . $cr;
						$edit++;
					}
					if(strlen($buff[2]) == 1){
						$buff[2] = "0" . $buff[2];
						$msg .= "修正：" . $line . "件目の"
							. $fname . "(" . $souce . ")"
							. "の秒を２桁(" . $buff[2] . ")にしました" . $cr;
						$edit++;
					}
					//チェック後のデータを元配列に戻す
					$souce = $buff[0] . ":" . $buff[1] . ":" . $buff[2];

				}else {
					$error++;
					$msg .= "エラー：" . $line . "件目の"
						. $fname . "(" . $souce . ")"
						. "の時刻は存在しません" . $cr;
				}
			}else{
				$error++;
				$msg .= "エラー：" . $line . "件目の"
					. $fname . "(" . $souse . ")"
					. "の時刻は不正な形式です" . $cr;
			}


		}else{
			$error++;
			$msg .= "エラー：" . $line . "件目の"
				. $fname . "(" . $souce . ")"
				. "の時刻は不正な文字列です" . $cr;
		}
	}else{
		$error++;
		$msg .= "エラー：" . $line . "件目に"
			. $fname . "(" . $souce . ")"
			. "の時刻データがありません" . $cr;
	}

	//return $souce;
	return array($souce,$error,$warning,$edit,$msg);
}


//uidのチェック
function checkUid($line , $fname , $souce){
	$error = 0;
	$warning = 0;
	$edit = 0;
	$msg = "";

	global $cr;


	//uidの有無を確認
	//重複の確認（このファイルのデータ内のみの限界あり）
	//空白、重複の場合は適正な値を設定する
	//正規のuid class(3桁) + 現在時間(16進数) + ランダム値(3桁) : javascript側と整合させること
 
	if(isset($souce)){

		//半角のみ許可（数値と/）
		if (preg_match("/^[\/0-9]+$/", $souce)){

			$buff = explode("/", $souce);
			if(count($buff) == 3){
				//checkdate 月,日,年　の並び
				if (checkdate($buff[1], $buff[2], $buff[0]) === true) {
					//$buff[0]
					if(strlen($buff[1]) == 1){
						$buff[1] = "0" . $buff[1];
						$msg .= "修正：" . $line . "件目の"
 							. $fname . "(" . $souce . ")"
							. "の月を２桁(" . $buff[1]
							. ")にしました" . $cr;
						$edit++;
					}
					if(strlen($buff[2]) == 1){
						$buff[2] = "0" . $buff[2];
						$msg .= "修正：" . $line . "件目の"
							. $fname . "(" . $souce . ")"
							. "の日を２桁(" . $buff[2]
							. ")にしました" . $cr;
						$edit++;
					}
					//チェック後のデータを元配列に戻す
					$souce = $buff[0] . "/" . $buff[1] . "/" . $buff[2];

				}else {
					$error++;
					$msg .= "エラー：" . $line . "件目の"
						. $fname . "の日付(" . $souce
						. ")は存在しません" . $cr;
				}
			}else{
				$error++;
				$msg .= "エラー：" . $line . "件目の"
					. $fname . "の日付(" . $souce
					. ")は不正な形式です" . $cr;
			}


		}else{
			$error++;
			$msg .= "エラー：" . $line . "件目の"
				. $fname . "の日付(" . $souce
				. ")は不正な文字列です" . $cr;
		}
	}else{
		$error++;
		$msg .= "エラー：" . $line . "件目に"
			. $fname . "データがありません" . $cr;
	}

	//return $souce;
	return array($souce,$error,$warning,$edit,$msg);
}


//csvテキスト文字列（変数）を連想配列に変換
function csvTextToArray($val){

	//改行文字列
	//$cr = chr(0x0d) . chr(0x0a);
	//$cr = chr(0x0d);
	//$cr = chr(0x0a);
	//$cr = "\n";
	
	//改行コードを統一する
	$val = str_replace(array("\r\n","\r","\n"), "\n", $val);

	//csvファイルを読み込む
	$cnt = 0;
	$fdata = array();

	$sline = explode("\n",$val);
	//$sline = explode($cr ,$val);

	for($m = 0 ; $m < count($sline) ; $m++){ 
		$buff = explode(",",$sline[$m]);

		//if(strpos($buff[0],"//") === 0){
		if(strpos($buff[0],"//") === 0 || strlen($sline[$m]) === 0){
			//コメント行と改行のみの行は省く
			continue;
		}

		//print_r($buff);
		//print("data:<br /><br />");

		if($cnt == 0){
			//フィールド情報
			$fields  = array();
			$key_cnt = count($buff);	//field key 数

			for($i = 0 ; $i < $key_cnt ; $i++){
				//print($i . "=" .  $buff[$i] . "<br />");
				
				//フィールド名からダブルクォーテションを削除
				$buff[$i] = str_replace('"' , '' , $buff[$i]);
				$fields[$i] = $buff[$i];
			} 

			//print_r($fields);
			//print("fields:<br /><br />");
		}else{
			for($i = 0 ; $i < count($fields) ; $i++){
				if(isset($buff[$i])){
					//データからダブルクォーテションを削除
					$buff[$i] = str_replace('"' , '' , $buff[$i]);
					$fdata[$cnt][$fields[$i]] = $buff[$i];
				}else{
					$fdata[$cnt][$fields[$i]] = "";
				}
			}
			//print_r($fdata[$cnt]);
			//print("fdata:<br /><br />");
		}
		//有効データ数
		$cnt++;
	}

	//print_r($fdata);
	//print("<br /><br /> cnt=" . $cnt . "<br /><br />");


	//連想配列 の出力
	//配列での出力
	$buffArray = array();

	//0番目にフィールド情報をセット
	$buffArray[0] = $fields;

	//$i = 1 からスタート。0 はフィールドを意味するため
	for($i=1 ; $i < $cnt ; $i++){
		for($s = 0 ; $s < $key_cnt ; $s++){
			$buffArray[$i][$fields[$s]] = $fdata[$i][$fields[$s]];
		}
	}

	//print_r($buffArray);
	return $buffArray;
}

//連想配列をcsvテキスト文字列に変換
function arrayToCsvText($val){

	//改行文字列
	//$cr = chr(0x0d) . chr(0x0a);
	//$cr = chr(0x0d);
	//$cr = chr(0x0a);
	$cr = "\n";
	
	//改行コードを統一する
	//$val = str_replace(array("\r\n","\r","\n"), "\n", $val);

	//csvファイルを読み込む
	//$cnt = 0;
	//$fdata = array();

	//$sline = explode("\n",$val);

	$buff = "";
	for($m = 0 ; $m < count($val) ; $m++){
		if($m == 0){
			//フィールド情報
			$fields  = array();
			$key_cnt = count($val[0]);	//field key 数

			for($i = 0 ; $i < $key_cnt ; $i++){
				$fields[$i] = $val[0][$i];
				$buff .= $fields[$i];

				if($i == ($key_cnt - 1)){
					$buff .= $cr;
				}else{
					$buff .=",";
				}
			}
		}else{
			for($i = 0 ; $i < count($fields) ; $i++){
				$buff .= $val[$m][$fields[$i]];

				if($i == ($key_cnt - 1)){
					$buff .= $cr;
				}else{
					$buff .=",";
				}
			}
		}
	}

	//print($bufff);
	//print("<br /><br />");

	return $buff;
}

?>
