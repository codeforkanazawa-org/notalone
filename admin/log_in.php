<?php
include_once("include.php");

$ThisFile   = "log_in.php";
@$NextFile   = $_SESSION["NextJob"];
@$ReturnFile = $_SESSION['CallJob'];
//$Case1File  = "common_user_append.php";  	//ユーザ登録なしの場合のアクセスファイル名


if(!isset($_POST["log_in_flg"])){
	$_POST["log_in_flg"] = "";
}

//①---- Log in ：ＩＤ　パスワードの要求 ----
if($_POST["log_in_flg"]==""){
	common_header("Log in ");

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

	<!--input type="button" value="閉じる" onclick="self.close()"-->

	<form method="POST" action="../admin/index.php">
	<input type="hidden" name="mode" value="">
	<input type="submit" value="indexに戻る">
	</form>
	';

//①のIf Else -----------
}else{
	//②のIf -----------
	if($_POST["log_in_flg"]=="1"){

		//---------------------------
		
		//db内容を配列に読み込む
		$UserArray = array();
		$type = 0;
		$UserArray = csvDatabaseRead($db_UserTable,$type);

		$rc = count($UserArray);

	 	if($rc==0){
			common_header("Log in ");

			echo'
		  	<form method="POST" action="' . $Case1File . '">
                  	ユーザ登録数が０です。（管理者処理に移行）
                  	<input type="hidden" name="log_in_flg" value="" >
                  	<input type="submit" value="確認">
              		</form>
			';
			exit();
		}


		$user_id   = trim($_POST["log_in_id"]);
		$user_pass = trim($_POST["log_in_pass"]);

		//id,pwブランクの対策
		if($user_id == "" || $user_pass == ""){
			common_header("Log in ");

			echo '
			<form method="POST" action="' . $ThisFile . '">
			ID、またはパスワードが入力されていません
			<input type="hidden" name="log_in_flg" value="" >
			<br />
			<input type="submit" value="確認">
			</form>
			';
			exit();
		}

		//*** ハッシュ暗号化（sha256）
		$user_pass = hash("sha256" , $_POST["log_in_pass"]);

		$rcflg = false;
		//データは１から
		for($i = 1 ; $i < $rc ; $i++){
			if($UserArray[$i]['user_id'] == $user_id){
				$rcflg = true;
				break;
			}
		}


         	if($rcflg==false){
			common_header("Log in ");

			echo '
 	          	<form method="POST" action="' . $ThisFile . '">
                  	ID、またはパスワードが違います<BR>
                  	<input type="hidden" name="log_in_flg" value="" >
			<br />
                  	<input type="submit" value="確認">
               		</form>
			';
			exit();
		}

		$id     = trim($UserArray[$i]['user_id']);
		$pass   = trim($UserArray[$i]['user_pw']);
		$active = trim($UserArray[$i]['active']); 
		$name   = trim($UserArray[$i]['real_name']);
		$level  = $UserArray[$i]['user_level'];

		if($active != 1){
			common_header("Log in ");

			echo '
 	          	<form method="POST" action="' . $ThisFile . '">
                  	ID、またはパスワードが違います<BR>
                  	<input type="hidden" name="log_in_flg" value="" >
			<br />
                  	<input type="submit" value="確認">
               		</form>
			';
			exit();
		}

		if(strcmp($user_pass,$pass)==0){

			//cookieの書き込み
			setcookie($USERid_cookie    , $id    , time()+60*60*24, "/"); 
			setcookie($USERlevel_cookie , $level , time()+60*60*24, "/"); 

		        //--- セッション情報の格納
                	$_SESSION[$USER_session]  = $id;
			$_SESSION[$LEVEL_session] = $level;


        		$last_login   = trim($UserArray[$i]['last_login']);
			$access_count = trim($UserArray[$i]['login_count']);	

			if($access_count == NULL){
				$access_count = 0;
			}


			common_header("Log in ");

			print("あなたは　" . $id . "（" . $name . "）さんですね。<br>");

			print("Last Login : " . $last_login . " / Login Count : " . $access_count . "<br />");

		        //---ログイン情報の更新
			//---last_login time
			$last_login = date("Y-m-d H:i:s");
			$UserArray[$i]['last_login'] = $last_login;

			//---access_count
			$access_count++;
			$UserArray[$i]['login_count'] = $access_count;

			//*********
			$UserArray = csvDatabaseWrite($db_UserTable , $UserArray);
			//*********


			echo '
			<form method="POST" action="' . $ReturnFile . '">
			ログインOK。
			<input type="hidden" name="log_in_flg" value="" >
			<input type="submit" value="確認">
			</form>
			';
	print("戻りファイル：" . $ReturnFile);


		}else{
			common_header("Log in ");

			print("あなたは　" . $id . "（" . $name . "）さんですね。<br>");
			echo '
			<form method="POST" action="' . $ThisFile . '">
			ID、またはパスワードが違います
			<input type="hidden" name="log_in_flg" value="" >
			<br />
			<input type="submit" value="確認">
			</form>
			';
		}
	}
}

?>

</BODY>
</HTML>