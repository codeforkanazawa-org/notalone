<?php
//ini_set( 'display_errors', "1" ); 
//ini_set( 'display_startup_errors', "1" ); 


//使用するＤＢ名
//$db         = mysql_connect($db_host,$db_id,$db_pw);
//$sql_server = mysql_select_db($db_name,$db);
$db_host = "localhost";		//db folder name
$db_id   = "nna_admin_db";	//admin user name
$db_pw   = "udc20151114";	//admin password
$db_name = "notonotalone";	//csv folder name
//

//このサイトのディレクトリ
$SITE_dir = "kosodate/";

//このアプリケーションのルートディレクトリ
$ROOT_dir    = $SITE_dir . "admin/";

//icon画像用ディレクトリ
$ICON_dir   = "../icons/";

//** cookie name ******************
$USERid_cookie    = "kosodate_id";
$USERlevel_cookie = "kosodate_level";
//*********************************

/* ここではメールでのユーザ確認未使用
//mail master 
//メール返信が可能とする場合は、実メールアドレスを設定すること
$FROM_addr = "mailmaster@nono1.jp";	//ダミー
//仮登録承認URL
$USER_url = "http://apli.nono1.jp/nightview/admin/user_comp.php"; 
*/


//フォーム再送メッセージ対策・セッション開始前に実行
session_cache_limiter('none');

//セッションの開始
@session_start();

//ユーザ管理共通のセッション
$USER_session  = "CSVDB_USER";   //USER IDのセッション用環境変数名
$LEVEL_session = "CSVDB_LEVEL";  //USER LEVELのセッション用環境変数名

//アプリ個別のセッション
$JYOUKEN_session = "CSVDB_JYOUKEN";  //検索条件のセッション用環境変数名
$SORTJKN_session = "CSVDB_SORTJKN";  //並び替えのセッション用環境変数名

//データのページ表示用の設定条件とセッション
$PAGE_datarow = 50;	//１ページ当りに表示するデータ件数の最大値
//$DATAMAX_session   = "CSVDB_DATAMAX";		//対象データの全体件数
$PAGESTART_session   = "CSVDB_PAGESTART";	//表示開始のページ番号
//$PAGEEND_session   = "CSVDB_PAGEEND";		//表示終了のページ番号

//$PAGE_start = 0;

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
      //kanri_user テーブルと連動。変更する場合は下記も修正のこと。===================
      //ok_level  :アクセス可能ユーザのレベル 3:システム管理者のみ　2:管理ユーザ以上　１:一般ユーザ以上
      //=====================================================================
      //$id_disp   :ﾁｪｯｸしたID、ﾊﾟｽﾜｰﾄﾞの表示  0:表示しない　1:表示する
      //$error_disp:拒否時の警告表示　　　　　 0:表示しない　1:表示する
      //$ret_page  :拒否時の戻りページ

      if($id_disp==1){
         print("<<利用者情報 :" . $_SESSION[$USER_session] . " :" . $_SESSION[$LEVEL_session] . ">><br> "); 
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
            	print("【警告】<BR>");
            	print("あなたは、この処理は実施できません！！<BR>");

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



//Header Body 統一用のサブルーチン
function user_header($title){
	//title:ページのタイトル名 
       	//color=#FF(red)FF(green)FF(blue)
	global $ROOT_dir;
	global $USER_session;
	global $ThisFile;

       	print("<HTML lang='ja'>");
       	print("<HEAD>");
       	print("<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>");
	print("<TITLE>" . $title . "</TITLE>");
       	print("</HEAD>");
       	print("<BODY bgcolor='#C2FF80' background='' text=''>");
       	print("<center>");
       	print("<table>");
       	print("<tr><td><center>");
		//print("<img src='" . $ROOT_dir . "title.gif'>");
		print("</center></td>");
            print("<td><font color='#FF0000' size=4 >　" . $title . "</font></td></tr>");
       	print("</table>");
       	print("</center>");
       	print("<hr>");  


	if($_SESSION[$USER_session] == ""){
		print("ログインしていません<br>");
		print("<hr>");
	}else{
		print("<a href='log_out.php' target='_parent'>LogOut</a>　　");

		if($ThisFile != ""){
			print("<a href='index.php' target='_parent'>index</a>");
		}
		print("<hr>");
	}

}


//Header Body 統一用のサブルーチン
function common_header($title){
	//title:ページのタイトル名 
       	//color=#FF(red)FF(green)FF(blue)
	global $ROOT_dir;
	global $USER_session;
	global $ThisFile;


       	print("<HTML lang='ja'>");
       	print("<HEAD>");
       	print("<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>");
	print("<TITLE>" . $title . "</TITLE>");
       	print("</HEAD>");
       	print("<BODY bgcolor='#C2FF80' background='' text=''>");
       	print("<center>");
       	print("<table>");
       	print("<tr><td><center>");
		//print("<img src='" . $ROOT_dir . "title.gif'>");
		print("</center></td>");
            print("<td><font color='#FF0000' size=4 >　" . $title . "</font></td></tr>");
       	print("</table>");
       	print("</center>");
       	print("<hr>"); 


	if($_SESSION[$USER_session] == ""){
		print("ログインしていません<br>");
		print("<hr>");
	}else{
		print("<a href='log_out.php'>LogOut</a>　　");

		if($ThisFile != ""){
			print("<a href='index.php'>index</a>");
		}
		print("<hr>");
	}

}


//フィーフドデータの自動入力（オプション）
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
					$db         = mysql_connect($db_host,$db_id,$db_pw);
					$sql_server = mysql_select_db($db_name,$db);

                    			print("自動<br>採番");
        			    	$result = mysql_query("select max( " . $Table_struc[$Key_row][0] . " ) from " . $Table_name ,$db);
					$max    = mysql_result($result, 0, 0);

					if($max >= 1){
           					$recode_no = $max + 1;
        			    	}else{
           					$recode_no = 1;
        			    	}

					mysql_close();

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

	$db         = mysql_connect($db_host,$db_id,$db_pw);
	$sql_server = mysql_select_db($db_name,$db);

    	for($s=0 ; $s < $Counter_row ; $s++){
	    	$access_table = $Counter[$s][0];

		$dbc = mysql_query("select * from " . $access_table . " order by no " ,$db);
		$rc  = mysql_num_rows($dbc);

	    	$Counter[$s][1] = $rc;
	    	for($i=0 ; $i < $Counter[$s][1] ; $i++){
			mysql_data_seek($dbc,$i);
			$now_data = mysql_fetch_assoc($dbc);

	       		$Mem_buffer[$s][$i][0] = trim($now_data['label_data']);
	       		$Mem_buffer[$s][$i][1] = trim($now_data['label_show']);
			$Mem_buffer[$s][$i][2] = trim($now_data['label_memo']);

	       		//print($Mem_buffer[$s][$i][0] ."," . $Mem_buffer[$s][$i][1] .",". $Mem_buffer[$s][$i][2]."<br>");

	    	}
    	}

    	mysql_close();
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

?>
