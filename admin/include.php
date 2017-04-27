<?php
//フォーム再送メッセージ対策・セッション開始前に実行
session_cache_limiter('none');
//セッションの開始
@session_start();

//ユーザ管理共通のセッション
$USER_session  = "NOTALONE_USER";   //USER IDのセッション用環境変数名
$LEVEL_session = "NOTALONE_LEVEL";  //USER LEVELのセッション用環境変数名

//ユーザ管理共通のクッキー
$USERid_cookie  = "NOTALONE_USER";   //USER IDのセッション用環境変数名
$LEVELlevel_cookie = "NOTALONE_LEVEL";  //USER LEVELのセッション用環境変数名

//アプリ個別のセッション
$JYOUKEN_session = "NOTALONE_JYOUKEN";  //検索条件のセッション用環境変数名
$SORTJKN_session = "NOTALONE_SORTJKN";  //並び替えのセッション用環境変数名

//データのページ表示用の設定条件とセッション
$PAGE_datarow = 50;	//１ページ当りに表示するデータ件数の最大値
//$DATAMAX_session   = "NOTALONE_DATAMAX";	//対象データの全体件数
$PAGESTART_session = "NOTALONE_PAGESTART";	//表示開始のページ番号
//$PAGEEND_session   = "NOTALONE_PAGEEND";	//表示終了のページ番号

//$PAGE_start = 0;


//ini_set( 'display_errors', "1" ); 
//ini_set( 'display_startup_errors', "1" ); 


//このサイトのディレクトリ
$SITE_dir = "notalone/";	//サブディレクトリの場合
//$SITE_dir ="/";			//ルートの場合

//このアプリケーションのルートディレクトリ
$ROOT_dir    = $SITE_dir . "admin/";

//icon画像用ディレクトリ
$ICON_dir   = "../icons/";

//Login User Table
$db_Host      = "../localhost";
$db_UserTable = $db_Host . "/user.csv";


//** notalone cookie name **********
$USERid_cookie    = "notaloneid";
$USERlevel_cookie = "notalonelevel";
//***********************************
//mail master 
//メール返信が可能とする場合は、実メールアドレスを設定すること
//$FROM_addr = "mailmaster@nono1.jp";	//ダミー

//仮登録承認URL
//$USER_url = "http://apli.nono1.jp/nightview/admin/user_comp.php"; 


//session のチェック
if(!isset($_SESSION[$USER_session])){
	$_SESSION[$USER_session] = "";
}


//*************************//
//数値のチェック :32767以下の数値（半角、全角）であれば半角数値を返す。数値以外は""を返す。
function Num_check($temp){
	if(is_numeric($temp)){
		if($temp=="0"){
			return "0";		//0は数値として返せないないため、強制的に文字列"0"として返す
		}else{
       			return (int)$temp;   	//数値
		}
        }else{
		return "";		//数値以外
      	}
}

//個別ページへのアクセス許可否のチェック。
function Access_check($ok_level,$id_disp,$error_disp,$ret_page){
	global $USER_session;
	global $LEVEL_session;
      //kanri_user テーブルと連動。変更する場合は下記も修正のこと。====================================
      //ok_level  :アクセス可能ユーザのレベル 3:システム管理者のみ　2:管理ユーザ以上　１:一般ユーザ以上
      //================================================================================
      //$id_disp   :ﾁｪｯｸしたID、ﾊﾟｽﾜｰﾄﾞの表示  0:表示しない　1:表示する
      //$error_disp:拒否時の警告表示　　　　　 0:表示しない　1:表示する
      //$ret_page  :拒否時の戻りページ

      if($id_disp==1){
         //print("<p id='user_role_info'>利用者情報 :" . $_SESSION[$USER_session] . " :" . $_SESSION[$LEVEL_session] . "</p>"); 
      }
      //kanri_user テーブルと連動。変更する場合は下記も修正のこと。===================================
	$user_level = $_SESSION[$LEVEL_session];
/*
      switch(trim($_SESSION[$LEVEL_session])){
        case "一般ユーザ" :
             	$user_level=1;
		break;
        case "管理ユーザ" :
             	$user_level=2;
		break;
        case "システム管理者" :
             	$user_level=3;
		break;
      }
*/
      //===================================================================
      if($user_level < $ok_level){
         //-----------
         if($error_disp==1){
            	print("<p class='info'>あなたは この処理は実施する権限がありません<BR>この処理を実施したい場合はシステム管理者にお問い合わせください</p>");
            	//print("<form method='POST' action='" . $ret_page . "'>");
            	//print("<input type='submit' value='戻る'>");
            	//print("</form>");

            	exit();
         }
         //-----------
         return  -1;           //:NG -1 を返す
      }else{
         return  $user_level;  //:OK user_level を返す
      }
      	//print("user_level:" . $user_level . "ok_level:" . $ok_level);
}

//Body につける用のclassを出力
function body_class($str = NULL,$return = false){
	$dir = $_SERVER["SCRIPT_NAME"];
	$body_class = "";
	if($dir!=""){ $body_class = str_replace(array("/",".php",".html"), " ", $dir); }
	if($str){ $body_class .= " ".$str; }
	return " class='$body_class'";
};

//Header Body 統一用のサブルーチン2
function user_header($title,$onload=NULL){
	global $USER_session;

	if($_SESSION[$USER_session] == ""){
		$include_dir = dirname($_SERVER["SCRIPT_NAME"])."/";
		//$file_name = basename($_SERVER['PHP_SELF']);
		if($include_dir==="//" || $include_dir==="/" || $include_dir==="/notalone/"){$css_dir = "admin/";}else{$css_dir = "./";}
		//print("ログインしていません<br>");
		//print("<hr>");
		header('Location:'.$css_dir.'log_in.php');
		exit;
	}
	common_header($title,$onload);
}


//Header Body 統一用のサブルーチン
function common_header($title,$onload=NULL){
	//title:ページのタイトル名 
       	//color=#FF(red)FF(green)FF(blue)
	global $ROOT_dir;
	global $USER_session;
	global $ThisFile;
	global $LEVEL_session;
	$body_onload = ($onload) ? " onload='$onload'" : "";
	
	 ?>
<!DOCTYPE HTML>
<html>
<head>
<meta HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-cache'>
<title>管理ページ ｜ <?php echo $title; ?></title>
<?php include_once('include_header.php'); ?>
</head>
<body<?php echo $body_onload; ?><?php echo  body_class(); ?>>
<div id="body_inner">
<header id="header">
	<h1 id='page_title'><span>管理ページ</span><?php echo $title; ?></h1>
	<?php if(isset($_SESSION[$LEVEL_session]) && $_SESSION[$LEVEL_session]>=2){ ?>
		 <div id="header_admin">管理者</div>
	<?php } ?>
	<a id="header_back" href="#" onClick="history.back(); return false;"></a>
</header>

<div id="container">
	 <?php 
	if($_SESSION[$USER_session] == ""){
		//print("ログインしていません<br>");
		//print("<hr>");
	}else{
		//print("<a href='log_out.php'>LogOut</a>　　");
/*
		if($ThisFile != ""){
			print("<div class='btns'><a class='btn btn_modoru' href='index.php'>管理ページトップへ戻る</a></div>");
		}
		//print("<hr>");
*/
	}
}

function common_menu($ptn){
	global $SITE_dir;
	print('<div id="footer_btns">');
	switch($ptn){
		case 2 : 
			print("
			<a class='btn btn_modoru' href='/" . $SITE_dir . "index.html'>のとノットアローントップへ</a><a class='btn btn_negative' href='/" . $SITE_dir . "admin/log_out.php'>ログアウト</a>
			");
			break;
		case 1 :
			print("<a class='btn btn_modoru' href='/" . $SITE_dir . "admin/index.php'>管理ページトップへ戻る</a>　");
	}
	print('</div>');
}

//フィールドデータの自動入力（オプション）
function Auto_Input($no,$mode,$sw,$now_data){
	global $Table_struc;
	global $Key_row;
	global $Table_name;
	global $db_host;
	global $db_id;
	global $db_pw;
	global $db_name;

  	switch($sw){
		case 0 :
			//自動採番処理
			switch($mode){
        			case "edit" :
                  			print(trim($now_data[$Table_struc[$no][0]]));
                     		 	print("<input type='hidden' name='" . $Table_struc[$no][0] . "' size= " . $Table_struc[$no][6] . " value='" . trim($now_data[$Table_struc[$no][0]]) . "'>");
					break;
				case "copy"   :
            			case "append" :
					$db         = mysqli_connect($db_host,$db_id,$db_pw,$db_name);
					//$sql_server = mysql_select_db($db_name,$db);

                    			print("自動<br>採番");
        			    	$result = mysqli_query($db,"select max( " . $Table_struc[$Key_row][0] . " ) from " . $Table_name);

					//$max    = mysqli_result($result, 0, 0);
					mysqli_data_seek($result,0);
					$row = mysqli_fetch_row($result);
					$max  = $row[0]; 

					if($max >= 1){
           					$recode_no = $max + 1;
        			    	}else{
           					$recode_no = 1;
        			    	}

					mysqli_close($db);

					//no  に　自動採番（recode_no） 設定
					print("<input type='hidden' name='" . $Table_struc[$no][0] . "' size= " . $Table_struc[$no][6] . " value= '" . $recode_no . "'>");
					break;
				case "select" :
					print("<input type='text'   name='" . $Table_struc[$no][0] . "' size= " . $Table_struc[$no][6] . " value='' >");
					break;
         		}
  	}
}


//管理データを使用する場合
//使用時は　使用フィールドを指定して、下記Sub を　kanri_data_get()　から呼び出す
function kanri_data_get_bak(){
	global $db_host;
	global $db_id;
	global $db_pw;
	global $db_name;
	global $Counter_row;
	global $Counter;
	global $Mem_buffer;

	$db         = mysqli_connect($db_host,$db_id,$db_pw,$db_name);
	//$sql_server = mysql_select_db($db_name,$db);

    	for($s=0 ; $s < $Counter_row ; $s++){
	    	$access_table = $Counter[$s][0];

		$dbc = mysqli_query($db,"select * from " . $access_table . " order by no ");
		$rc  = mysqli_num_rows($dbc);

	    	$Counter[$s][1] = $rc;
	    	for($i=0 ; $i < $Counter[$s][1] ; $i++){
			mysqli_data_seek($dbc,$i);
			$now_data = mysqli_fetch_assoc($dbc);

	       		$Mem_buffer[$s][$i][0] = trim($now_data['label_data']);
	       		$Mem_buffer[$s][$i][1] = trim($now_data['label_show']);
			$Mem_buffer[$s][$i][2] = trim($now_data['label_memo']);

	       		//print($Mem_buffer[$s][$i][0] ."," . $Mem_buffer[$s][$i][1] .",". $Mem_buffer[$s][$i][2]."<br>");

	    	}
    	}

    	mysqli_close($db);
}


/**
 * ランダム文字列生成 (英数字)
 * $length: 生成する文字数
 */
function makeRandStr($length) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $length; $i++) {
        $r_str .= $str[rand(0, count($str))];
    }
    return $r_str;
}


//csvデータベースのアクセス
//データを読み込み、連想配列で返す
function csvDatabaseRead($filename,$type){
	//type
	//0 : return Array (default)
	//1 : return String

	//csvファイルのパス名
	$path = $filename;
	$cr = chr(0x0d) . chr(0x0a);

	//csvファイルを読み込む
	$cnt = 0;
	$fdata = array();

	$fp = fopen($path, "r");
	while ($sline = fgets($fp)) {

		//文字列から制御コードを除く
		//$line = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $sline); 
		$line = preg_replace('/[\x0D\x0A]/', '', $sline); 

		//print $line . "<br>\n";

		//$data = split(",",$line);
		$data = explode(",",$line);

		//print_r($data);

		if(strpos($data[0],"//") === 0){
			//コメント行は省く
			continue;
		}

		if($cnt == 0){
			//フィールド情報
			$fields  = array();
			$key_cnt = count($data);	//field key 数

			$fbuff = "[ ";
			for($i = 0 ; $i < $key_cnt ; $i++){
				//print($i . "=" .  $data[$i] . "<br />");
				
				//フィールド名からダブルクォーテションを削除
				$data[$i] = str_replace('"' , '' , $data[$i]);
				$fields[$i] = $data[$i];
				$fbuff .= '"' . $data[$i] . '"';

				if($i < $key_cnt - 1){
					$fbuff .= ' , ';
				}

				//フィールド情報のチェックが必要か
			} 
			$fbuff .= ' ];';

			//print_r($fields);
			//print("<br /><br />");
		}else{
			for($i = 0 ; $i < count($fields) ; $i++){
				//データからダブルクォーテションを削除
				$data[$i] = str_replace('"' , '' , $data[$i]);

				/*
				//強制的にutf-8にエンコードする
				$from_code = check_encode($data[$i]);
				if($from_code != "UTF-8"){
					$fdata[$cnt][$fields[$i]] = mb_convert_encoding($data[$i], "UTF-8", $from_code);
				}else{
					$fdata[$cnt][$fields[$i]] = $data[$i];
				}
				*/
				$fdata[$cnt][$fields[$i]] = $data[$i];

				//print $line . "<br>\n";
			}
			//print_r($fdata[$cnt]);
			//print("<br /><br />");
		}
		//有効データ数
		$cnt++;
	}

	//print_r($fdata);
	//print("<br /><br /> cnt=" . $cnt . "<br /><br />");

	fclose($fp);


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


	//文字列での出力（Javascript用）
	$buff = "";

	//0 はフィールド
	//1からデータ
	$buff = "[ " . $cr;
	for($i=0 ; $i < count($buffArray) ; $i++){
		$buff .= '[';
		for($s = 0 ; $s < $key_cnt ; $s++){
			if($i == 0){
				$buff .= "'" . $buffArray[$i][$s] . "'";
			}else{
				$buff .= "'" . $buffArray[$i][$buffArray[0][$s]] . "'";
			}

			if($s < $key_cnt - 1){
				$buff .= ' , ';
			}
		}
		$buff .= ' ] ';

		if($i < $cnt -1 ){
			$buff .= ' , ' . $cr;
		}
	}
	$buff .= $cr . ' ];';


	//return type
	if($type!=1){
		return $buffArray;

	}else{
		return $buff;
	}
}

//配列データを読み込み、ファイルを更新（上書き）する
function csvDatabaseWrite($filename , $data){
	//$dataは配列　　0行：フィールド名　１行以降：データ

	//csvファイルのパス名
	$path = $filename;
	$cr = chr(0x0d) . chr(0x0a);

	//csvファイルを開く（上書きモード）
	$fp = fopen($path, "w");

	//データ数
	$cnt = count($data);
	for($i = 0 ; $i < $cnt ; $i++){
		$buff = "";

		//フィールド数
		$fcnt = count($data[$i]);

		for($s = 0 ; $s < $fcnt ; $s++){
			if($i == 0){
				$buff .= $data[$i][$s]; 
			}else{
				$buff .= $data[$i][$data[0][$s]];
			}

			if($s == ($fcnt - 1)){
				$buff .= $cr;
			}else{
				$buff .= ",";
			}
		}
		fputs($fp,$buff);
	}
	fclose($fp);
}


//フィールド定義ファイルを読み込み、定義をjavascript用に文字列で返す
function fieldDataRead($fname){
	$type = 0;	//配列で返す
	$buff = csvDatabaseRead($fname , $type);
	$ret  = "";

	//fieldname
	$ret .= "var Fno = [";
	for($i = 0 ; $i < count($buff[0]) ; $i++){
		$ret .= "'";
		$ret .= $buff[0][$i];
		$ret .= "'";
		if($i < count($buff[0])-1){
			$ret .= ",";
		}
	}
	$ret .= "];\n";

	//fieldlabel
	$ret .= "var Flabel = {";
	for($i = 0 ; $i < count($buff[1]) ; $i++){
		$ret .= "'";
		$ret .= $buff[0][$i];
		$ret .= "':";
		$ret .= "'" . $buff[1][$buff[0][$i]] . "'";
		if($i < count($buff[1])-1){
			$ret .= ",";
		}
	}
	$ret .= "};\n";

	//fieldwidth
	$ret .= "var Field_etc = {";
	for($i = 0 ; $i < count($buff[2]) ; $i++){
		$ret .= "'";
		$ret .= $buff[0][$i];
		$ret .= "':";
		$ret .= $buff[2][$buff[0][$i]];
		if($i < count($buff[2])-1){
			$ret .= ",";
		}
	}
	$ret .= "};\n";

	return $ret;
}


//文字エンコードチェック関数（未使用）
function check_encode($str){
	foreach(array('UTF-8','SJIS','EUC-JP','ASCII','JIS','ANSI') as $charcode){
		if(mb_convert_encoding($str, $charcode, $charcode) == $str){
			return $charcode;
		}
	}

	return null;
}

?>
