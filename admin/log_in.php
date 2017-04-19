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
	common_header("ログイン");

	 ?>
	<form method="POST" name="log_in_form" id="log_in_form" action="<?php echo $ThisFile; ?>">
		<p>ログインしてください。</p>
		<div>
			<input type="hidden"      name="log_in_flg"  id="log_in_flg"  value="1" >
			<label>ＩＤ</label>
			<input type="text"     name="log_in_id"      id="log_in_id">
			<label>パスワード</label>
			<input type="password" name="log_in_pass"    id="log_in_pass">
		</div>
		<div class="btns">
			<input type="submit"   name="log_in_submit"  id="log_in_submit" value="ログイン">
		</div>
	</form>
	<div id="footer_btns">
		<form method="POST"  name="return_form"   id="return_form" action="../index.html">
		<input type="hidden" name="mode"          id="mode" value="">
		<input class="btn_modoru" type="submit" name="return_submit" id="return_submit" value="のとノットアローントップへ">
		</form>
	</div>
	<?php 

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
			common_header("ログイン");
			echo'
		  	<form method="POST"  name="log_in_form"   id="log_in_form" action="' . $Case1File . '">
                  	<p class="info">ユーザ登録数が０です<p>
					<div class="btns">
						<input type="hidden" name="log_in_flg"    id="log_in_flg"    value="" >
						<input type="submit" name="log_in_submit" id="log_in_submit" value="管理者処理に移行">
					</div>
              	</form>
			';
			exit();
		}


		$user_id   = trim($_POST["log_in_id"]);
		$user_pass = trim($_POST["log_in_pass"]);

		//id,pwブランクの対策
		if($user_id == "" || $user_pass == ""){
			common_header("ログイン：エラー");
			echo '
			<form method="POST"  name="log_in_form"   id="log_in_form" action="' . $ThisFile . '">
					<p class="info">IDまたはパスワードが入力されていません<p>
					<div class="btns">
						<input type="hidden" name="log_in_flg"    id="log_in_flg"    value="" >
						<input type="submit" name="log_in_submit" id="log_in_submit" value="戻る">
					</div>
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
			common_header("ログイン：エラー");
			echo '
 	          	<form method="POST"  name="log_in_form"   id="log_in_form" action="' . $ThisFile . '">
                  	<p class="info">IDまたはパスワードが違います<p>
					<div class="btns">
						<input type="hidden" name="log_in_flg"    id="log_in_flg"    value="" >
						<input type="submit" name="log_in_submit" id="log_in_submit" value="戻る">
					</div>
			';
			exit();
		}

		$id     = trim($UserArray[$i]['user_id']);
		$pass   = trim($UserArray[$i]['user_pw']);
		$active = trim($UserArray[$i]['active']); 
		$name   = trim($UserArray[$i]['real_name']);
		$level  = $UserArray[$i]['user_level'];

		if($active != 1){
			common_header("ログイン：エラー");
			echo '
 	          	<form method="POST"  name="log_in_form"   id="log_in_form" action="' . $ThisFile . '">
                  	<p class="info">IDまたはパスワードが違います<p>
					<div class="btns">
						<input type="hidden" name="log_in_flg"    id="log_in_flg"    value="" >
						<input type="submit" name="log_in_submit" id="log_in_submit" value="戻る">
					</div>
				</form>
			';
			exit();
		}

		if(strcmp($user_pass,$pass)==0){

			//cookieの書き込み（有効期限　1年間）
			setcookie($USERid_cookie    , $id    , time()+60*60*24*365, "/"); 
			setcookie($USERlevel_cookie , $level , time()+60*60*24*365, "/"); 

		        //--- セッション情報の格納
                	$_SESSION[$USER_session]  = $id;
			$_SESSION[$LEVEL_session] = $level;


        		$last_login   = trim($UserArray[$i]['last_login']);
			$access_count = trim($UserArray[$i]['login_count']);	

			if($access_count == NULL){
				$access_count = 0;
			}


			//common_header("管理ページ｜ログイン");

			//print("あなたは　" . $id . "（" . $name . "）さんですね。<br>");
			//print("Last Login : " . $last_login . " / Login Count : " . $access_count . "<br />");
			$_SESSION['LastLogin']  = $last_login;
			$_SESSION['LoginCount'] = $access_count;



		        //---ログイン情報の更新
			//---last_login time
			$last_login = date("Y/m/d H:i:s");
			$UserArray[$i]['last_login'] = $last_login;

			//---access_count
			$access_count++;
			$UserArray[$i]['login_count'] = $access_count;

			//*********
			$UserArray = csvDatabaseWrite($db_UserTable , $UserArray);
			//*********

			//ログイン完了時　ReturnFile　へ戻る
			if($ReturnFile){
				header('location:' . $ReturnFile);
			}else{
				header('location:' . "./index.php");
			}

			/*
			echo '
			<form method="POST"  name="log_in_form"   id="log_in_form" action="' . $ReturnFile . '">
			' . $id . '（' . $name . '）さん　こんにちは！<br>
			最終ログイン : ' . $last_login . '<br />
			ログイン回数 : ' . $access_count . '<br />
			<input type="hidden" name="log_in_flg"    id="log_in_flg"    value="" >
			<input type="submit" name="log_in_submit" id="log_in_submit" value="確認">
			</form>
			';

			common_menu();
			*/

		}else{
			common_header("ログイン：エラー");
			//print("あなたは　" . $id . "（" . $name . "）さんですね。<br>");
			echo '
			<form method="POST"  name="log_in_form"   id="log_in_form" action="' . $ThisFile . '">
				<p class="info">IDまたはパスワードが違います<p>
				<div class="btns">
					<input type="hidden" name="log_in_flg"    id="log_in_flg" value="" >
					<input type="submit" name="log_in_submit" id="log_in_submit" value="戻る">
				</div>
			</form>
			';
		}
	}
}

?>

<?php include_once 'include_footer.php'; ?>