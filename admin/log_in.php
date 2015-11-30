<?php
include_once("include.php");

$ThisFile   = "log_in.php";
$NextFile   = $_SESSION["NextJob"];
$ReturnFile = $_SESSION['CallJob'];
$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名

common_header("Log in ");

//①---- Log in ：ＩＤ　パスワードの要求 ----
if($_POST["log_in_flg"]==""){

	print('<form method="POST" action="' . $ThisFile . '">');

	echo '
	★ＩＤとパスワードを入力してください。<br>
	<input type="hidden" name="log_in_flg" value="1" >
	ＩＤ：<br>
	<input type="text"     name="log_in_id"   size=20 ><br>
	パスワード：<br>
	<input type="password" name="log_in_pass" size=20 ><br>
	<input type="submit" value="送信">
	</form>

	<form method="POST" action="' . $ReturnFile . '">
	<input type="hidden" name="mode" value="">
	<input type="submit" value="戻る">
	</form>
	<br>
	<br>
	<input type="button" value="閉じる" onclick="self.close()">
	';

//①のIf Else -----------
}else{
	//②のIf -----------
	if($_POST["log_in_flg"]=="1"){
		//--- セッション情報の格納
		$_SESSION[$USER_session]  = $_POST["log_in_id"];
		$_SESSION[$LEVEL_session] = 3;

		echo '
		<form method="POST" action="' . $ReturnFile . '">
		ログインOK。
		<input type="hidden" name="log_in_flg" value="" >
		<input type="submit" value="確認">
		</form>
		';


		/*****
		//---------------------------
		$db         = mysql_connect($db_host,$db_id,$db_pw);
		$sql_server = mysql_select_db($db_name,$db);

		mysql_query('SET NAMES utf8'); //

		$table_name = "common_user";

            	$sql = "select * from " . $table_name;
		$ret = mysql_query($sql);
            	$rc  = mysql_num_rows($ret);

	 	if($rc==0){
			echo'
		  	<form method="POST" action="' . $Case1File . '">
                  	ユーザ登録数が０です。（管理者処理に移行）
                  	<input type="hidden" name="log_in_flg" value="" >
                  	<input type="submit" value="確認">
              		</form>
			';
			exit();
		}

		$user_id   = $_POST["log_in_id"];
		//$user_pass = $_POST["log_in_pass"];
		//*** ハッシュ暗号化（sha256）
		$user_pass = hash("sha256" , $_POST["log_in_pass"]);

		$sql = "select * from " . $table_name . " where id='" . $user_id . "'";
	        $ret = mysql_query($sql);
		$rc  = mysql_num_rows($ret);

         	if($rc==0){
			echo '
 	          	<form method="POST" action="' . $ThisFile . '">
                  	ユーザ名の登録がありません。<BR>
                  	<input type="hidden" name="log_in_flg" value="" >
                  	<input type="submit" value="確認">
               		</form>
			';

         	}else{
			$id    = trim(mysql_result($ret,0,"id"));
			$pass  = trim(mysql_result($ret,0,"pass"));
			$name  = trim(mysql_result($ret,0,"name"));
			$level = mysql_result($ret,0,"level");

			if(strcmp($user_pass,$pass)==0){
				print("あなたは　" . $id . "（" . $name . "）さんですね。<br>");
		               	//--- セッション情報の格納
                		$_SESSION[$USER_session]  = $id;
		                $_SESSION[$LEVEL_session] = $level;


	
        			$first_login  = trim(mysql_result($ret,0,"first_in"));
				$access_count = trim(mysql_result($ret,0,"count_in"));
				if($access_count == NULL){
					$access_count = 0;
				}

		               	//---ログイン情報の更新
				$add_sql = "";
				if($first_login == ""){
					//---first_login time
					$first_login = date("Y-m-d H:i:s");
					$add_sql = "first_in = '" . $first_login . "' , ";
                  		}

				//---last_login time
				$last_login = date("Y-m-d H:i:s");

				//---access_count
				$access_count++;


				$sql = "UPDATE " . $table_name . " SET " . $add_sql . " last_in = '" . $last_login . "' , count_in =" . $access_count . " where id = '" . $id . "'";
				mysql_query($sql);



				echo '
				<form method="POST" action="' . $ReturnFile . '">
				ログインOK。
				<input type="hidden" name="log_in_flg" value="" >
				<input type="submit" value="確認">
				</form>
				';
			}else{
				echo '
				<form method="POST" action="' . $ThisFile . '">
				パスワードが違います。
				<input type="hidden" name="log_in_flg" value="" >
				<input type="submit" value="確認">
				</form>
				';
			}
		}
		*****/


	}
}

?>

</BODY>
</HTML>