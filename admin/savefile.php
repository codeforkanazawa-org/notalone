<?php
ini_set( 'display_errors', "1" ); 
ini_set( 'display_startup_errors', "1" ); 

	$data   = $_POST['data'];
	$dir    = $_POST['dir'];
	$ext    = $_POST['ext']; 
	$header = $_POST['header'];

	if($header == ""){
		$header = "_unknow";
	}

	//改行コード
	$lfcr = chr(0x0d) . chr(0x0a);

	$fldn = "";	//フィールド名
	$buff = "";	//データ

	/*  ファイル名を自動的に変更する場合
	//投稿日時 データに設定 ********
	$cont_date = date("Y/m/d H:i:s");

	//日時取得しファイル名にする
	$save_fname = $header . "_" . date('Ymd') ."_" . date('His') . "." . $ext;
	$file_name = $dir . "/" . $save_fname;
	//**************************
	*/

	//ファイル名変更しない場合
	$save_fname = $header . "." . $ext;
	$file_name = $dir . "/" . $save_fname;

	// ファイル保存のおまじない
	file_put_contents($file_name , $data);

	//文字エンコードをUTF-8に調整　元の文字コードをうまく検出できない
	//file_put_contents($file_name , mb_convert_encoding($data, "UTF-8", "auto"));


	//echo $file_name;
	echo $save_fname;

?>
