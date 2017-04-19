<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

include_once("include.php");


// json_encode()関数が存在しないなら
if (!function_exists('json_encode')) {
	// JSON.phpを読み込んで
	require_once '../include/JSON.php';
	// json_encode()関数を定義する
	function json_encode($value) {
		$s = new Services_JSON();
		return $s->encodeUnsafe($value);
	}
	// json_decode()関数を定義する
	function json_decode($json, $assoc = false) {
		$s = new Services_JSON($assoc ? SERVICES_JSON_LOOSE_TYPE : 0);
		return $s->decode($json);
	}
}


//*********************************

	//-JSONデータの受信処理---------------------------------------------------//
	// file_get_contents()で送信データを受信(JSONの場合はここがミソ。らしい。)
	$json = file_get_contents("php://input");
	//$json = file_get_contents("data/sample.txt");

	//文字化け対策
	$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

	print($json);

	// JSON形式データをPHPの配列型に変換
	$eventdata = json_decode($json,true);

	//print_r($data);

	//**********************
	//イベント集計ファイルの雛形を読み出す
	$path = "../localhost/eventfields.csv";

	$fp = fopen($path, "r");
	$sline = fgets($fp);

	//文字列から制御コードを除く
	$line = preg_replace('/[\x0D\x0A]/', '', $sline); 
	$data = explode(",",$line);

	//フィールド情報
	$fields  = array();
	$key_cnt = count($data);	//field key 数

	for($i = 0 ; $i < $key_cnt ; $i++){
		//フィールド名からダブルクォーテションを削除
		$data[$i] = str_replace('"' , '' , $data[$i]);
		$fields[$i] = $data[$i];
	} 

	//集計フィールドの追加
	$fields[$i] = 'timestamp';
	$key_cnt++;

	$i++;
	$fields[$i] = 'soucefile';
	$key_cnt++;

	$i++;
	$fields[$i] = 'totaluser';
	$key_cnt++;
	//*****************


	print_r($fields);
	fclose($fp);
	
	//指定のeventファイルからデータを読み出す
	if(count($eventdata) == 0){
		print("データがありません");
		exit();		
	}


	//イベントデータをひとつの配列にまとめる
	$eventArray = array();
	$total = 0;	//イベントデータの通算数
	$timestamp = date("Y/m/d H:i:s");

	for($i = 0 ; $i < count($eventdata) ; $i++){
		if($eventdata[$i]['select'] != 1 ){
			//選択されたファイルのみ実施
			continue;
		}

		$evt_dir  = $eventdata[$i]['dir'];
		$evt_file = $eventdata[$i]['file'];
		$path = "../" . $evt_dir . "/" . $evt_file;

		$evt_user = $eventdata[$i]['user'];

		//イベントデータを配列で読み出す
		$buff = csvDatabaseRead($path,0);

		//配列の0番目は、フィールド情報のため　1 からスタート
		for($s = 1 ; $s < count($buff) ; $s++){
			for($m = 0 ; $m < count($fields) ; $m++){
				if (array_key_exists($fields[$m] , $buff[$s])){
					$eventArray[$total][$fields[$m]] = $buff[$s][$fields[$m]];
				}else{
					$eventArray[$total][$fields[$m]] = "";
				}
			}
			//タイムスタンプ、参照ファイル、ユーザー名を追加
			$eventArray[$total]['timestamp'] = $timestamp;
			$eventArray[$total]['soucefile'] = $evt_file;
			$eventArray[$total]['totaluser'] = $evt_user;

			$total ++;
		}
	}


	//連想配列を日付順にソート
	sortArrayByKey( $eventArray , 'when', SORT_ASC );

	print_r($eventArray);
	print("<br />");


	//出力ディレクトリ
	$dir = "../events";

	//集計データ配列から、今月分のデータ以降を月別にファイルに出力する
	$startYear  = (int)date("Y");
	$startMonth = (int)date("m");

	//最大月数
	$maxMonth = 14;
	$nowMonth = array();
	for($i = 0 ; $i < $maxMonth ; $i++){
		$strMonth = "0" . (string)$startMonth;
		$nowMonth[$i] = (string)$startYear . "/" . substr($strMonth,-2);
		
		$startMonth++;
		if($startMonth > 12){
			$startMonth = 1;
			$startYear  ++;
		}
 	}

	print_r($nowMonth);
	print("nowMonth=<br />");	

	//最大月数分配列をループ
	for($i = 0 ; $i < count($nowMonth) ; $i++){
		$buff = array(array());
		$bcnt = 0; 
		for($s = 0 ; $s < count($eventArray) ; $s++){
			$thisMonth = substr($eventArray[$s]['when'],0,7);

print("thisMonth=" . $thisMonth . "<br />");

			if($thisMonth == $nowMonth[$i]){

				//eventData抽出
				//0行目にフィールドを挿入
				if($bcnt == 0){
					for($m = 0 ; $m < count($fields) ; $m++){
						$buff[$bcnt][$m] = $fields[$m];
					}
				}
				for($m = 0 ; $m < count($fields) ; $m++){
					$buff[$bcnt+1][$fields[$m]] = $eventArray[$s][$fields[$m]];
				}
				$bcnt++;
			}
		}
		if($bcnt > 0){
			print_r($buff);
			//ファイルを出力する
			$filename = $dir . "/" . str_replace("/","",$nowMonth[$i]) . ".csv";
			csvDatabaseWrite($filename , $buff);
		}
	}

exit();


//連想配列のソート
function sortArrayByKey( &$array, $sortKey, $sortType = SORT_ASC ) {

    $tmpArray = array();
    foreach ( $array as $key => $row ) {
        $tmpArray[$key] = $row[$sortKey];
    }
    array_multisort( $tmpArray, $sortType, $array );
    unset( $tmpArray );
}

?>
