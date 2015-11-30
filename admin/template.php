<?php
//ini_set( 'display_errors', "1" ); 
//ini_set( 'display_startup_errors', "1" ); 

if(!isset($_GET['db']) || $_GET['db'] == "" ){
	print("処理が指定されていません");
	exit();
}

include_once("include.php");

//処理テーブル//
$db_file = $_GET['db'];
switch($db_file){
	case 'common_user' :
		$kanri_file = 'kanri_common_user_info.php';
		$menu_title = 'ユーザ管理';
		$acc_level  = 3;
		$memo = "";
		break;
	case 'fgroup' :
		$kanri_file = 'kanri_fgroup_info.php';
		$menu_title = 'グループ管理';
		$acc_level  = 2;
		$memo = "";
		break;
	case 'facilities' :
		$kanri_file = 'kanri_facilities_info.php';
		//グループ管理簿対応
		if(isset($_GET['sub'])){
			$sub_db = $_GET['sub'];
		}
		if(isset($_GET['name'])){
			$sub_name = $_GET['name']; 
		}

		$menu_title = 'グループ管理簿';
		$acc_level  = 2;
		$memo = "";
		break;
	case 'report' :
		$kanri_file = 'kanri_report_info.php';
		$menu_title = 'レポート管理';
		$acc_level  = 2;
		$memo = "";
		break;
	case 'manager' :
		$kanri_file = 'kanri_manager_info.php';
		$menu_title = '管理担当';
		$acc_level  = 2;
		$memo = "";
		break;
	case 'lightdivi' :
		$kanri_file = 'kanri_lightdivi_info.php';
		$menu_title = '設備の区分';
		$acc_level  = 2;
		$memo = "";
		break;
	case 'lightclass' :
		$kanri_file = 'kanri_lightclass_info.php';
		$menu_title = '灯りの種別';
		$acc_level  = 2;
		$memo = "";
		break;
}
//////////////

//============================= 
$ThisFile   = "template.php?db=" . $db_file;	//このファイル名
//グループ管理簿対応 **
if(isset($sub_db)){
	$ThisFile .= "&sub=" . $sub_db;
}
if(isset($sub_name)){
	$ThisFile .= "&name=" . $sub_name;
}
//*****************

$ReturnFile = $ThisFile;			//戻り先のファイル名
//==============================

//=============================
$_SESSION['CallJob'] = $ThisFile;		//log_in.php　からの戻り用
include("log_in_check.php");	//check off中
//=============================

include($kanri_file);
include("csv_db_access.php");




//このＰＲＯＣ名
$Proc_name = $ThisFile;

//******** ここよりメインＰＲＯＣ ****************************
common_header($menu_title);

$user_level = Access_check( $acc_level ,1,1,$ReturnFile);

print('レベル　＝　1:一般ユーザ　2:管理ユーザ　3:システム管理者<br>');
print( $memo );


//テーブル別の補助ルーチン
if($db_file == "common_user"){ change_sha256(); }
if($db_file == "report")     { report_manage(); }
if($db_file == "lightclass") { set_imagefile($ICON_dir); }

if($db_file == "fgroup")     { set_fctable();   }
if($db_file == "facilities") { set_dbname($sub_db,$sub_name); }


//データ共通処理ルーチン
kanri_DB_access();




?>

<!-- jquery ライブラリ -->
<script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>

<script type="text/javascript">

//select 対応
function pre_execPost(action,data){
	var emt = document.getElementById("pagelist");

	for(var i = 0 ; i < emt.length ; i++){
		if(emt.options[i].selected){
			var page = emt.options[i].value;
		}
	}

	data['page_no'] = page;
	execPost(action,data);
}

//edit copy delete etc key action
function execPost(action, data) {
 // フォームの生成
 var form = document.createElement("form");
 form.setAttribute("action", action);
 form.setAttribute("method", "post");
 form.style.display = "none";
 document.body.appendChild(form);
 // パラメタの設定
 if (data !== undefined) {
  for (var paramName in data) {
   var input = document.createElement('input');
   input.setAttribute('type', 'hidden');
   input.setAttribute('name', paramName);
   input.setAttribute('value', data[paramName]);
   form.appendChild(input);
  }
 }
 // submit
 form.submit();
}

</script>

<!-- common footer -->
<style>
table{
	border-spacing: 0px;
	border-collapse: collapse;
}

table td {
    word-break: break-all;
}
</style>

</body>
</html>


<?php
//fgroup
function set_fctable(){
	global $db_host;
	global $db_id;
	global $db_pw;
	global $db_name;

	if($_POST['mode'] == "append_mode" || 
	   $_POST['mode'] == "edit_mode"   ||
	   $_POST['mode'] == "copy_mode"	){

		if($_POST['mode'] == "append_mode"){
		echo '
	fc_<input type="text" id="table_name" value="" />
	<input type="button" id="create_table" onclick="create_table()" value="テーブルを作成" />｜
		';
		}

		//DBコンタクト
		$db         = mysql_connect($db_host,$db_id,$db_pw);
		$sql_server = mysql_select_db($db_name,$db);
		mysql_query('SET NAMES utf8'); //

		//グループ管理テーブルの情報
		$sql = "SHOW TABLES FROM " . $db_name;
		$result = mysql_query($sql);

		if (!$result) {
    			echo "DB Error, could not list tables\n";
    			echo 'MySQL Error: ' . mysql_error();
    			exit;
		}

		echo '<select name="groupDb" id="groupDb">';

		//配列として１件づつ抽出
		while ($row = mysql_fetch_row($result)) {
			if (preg_match("/^fc_/", $row[0])) {
				echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
			}
    			//echo "Table: {$row[0]}\n";
		}

		mysql_free_result($result);

		//$Table_name = "fgroup";

		echo '

		</select>
	<input type="button" id="set_table" onclick="set_table()" value="テーブルの選択" />｜
		';

		//ユーザIDの情報
		$sql = "select * FROM common_user where active=1";
		$result = mysql_query($sql);

		if (!$result) {
    			echo "DB Error, could not user list \n";
    			echo 'MySQL Error: ' . mysql_error();
    			exit;
		}

		echo '<select name="userId" id="userId">';

		//連想配列として１件づつ抽出
		while ($row = mysql_fetch_assoc($result)) {
			echo '<option value="' . $row['id'] . '">' . $row['id'] . '：' . $row['name'] . '(' . $row['level'] . ')' . '</option>';
		}

		mysql_free_result($result);

		echo '
	</select>
	<input type="button" id="set_user"  onclick="set_user()" value="グループユーザの選択" />
		';
	}

	echo '
	<script type="text/javascript">
	function set_table(){
		var fld = document.getElementsByName("gtable").item(0);
		var emt = document.getElementById("groupDb");

		for(var i = 0 ; i < emt.options.length ; i++){
			var eop = emt.options[i];

			if(eop.selected){ 
				fld.value = eop.value;
				break;
			}		
		}

	}

	function set_user(){
		var fld = document.getElementsByName("userid").item(0);
		var emt = document.getElementById("userId");

		for(var i = 0 ; i < emt.options.length ; i++){
			var eop = emt.options[i];

			if(eop.selected){
				if(fld.value != ""){
					fld.value += ",";
				}
				fld.value += eop.value;
				break;
			}		
		}

	}

	function create_table(){
		var tbn = document.getElementById("table_name");

		//文字列のチェック　小文字英数のみ可能
		if(tbn.value == ""){
			alert("テーブル名が入力されていません");
			return;
		}
		// 入力値チェック
		if (tbn.value.match(/[^a-z0-9]+/)){
			alert( "許可されていない文字が入力されています" );
			return;
		}

		var new_table = "fc_" + tbn.value;

		//既存テーブル名との重複チェック
		var emt = document.getElementById("groupDb");
		for(var i = 0 ; i < emt.options.length ; i++){
			var eop = emt.options[i];

			if(eop.value == new_table){ 
				alert("テーブル名が重複しています");
				return;
			}
		}

		if(confirm(new_table + "を新規に作成します")){
			create_fctable(new_table);
		}
	}

	function create_fctable(table){
	        $.ajax({
        		type: "GET",
            		url: "fctable.php?table=" + table,
            		//dataType: "json",
	    		//data : table },

		        success: function(data) {
				//alert("success: " + data);
				var emt = document.getElementsByName("gtable").item(0);
				emt.value = data;
	            	},
	            	error: function(XMLHttpRequest, textStatus, errorThrown) {
	                	//エラーメッセージの表示
        	        	alert("Error : " + errorThrown);
            		}
       		});
	}

	</script>
	';
}


//facilities
function set_dbname($db , $name){
	//**** データベース名を明示する *****
	print($name . "(" . $db . ")<br />");
}

//common_user
function change_sha256(){

	//**** パスワードを個別に暗号化する処理 ****
	if($_POST['mode'] == "append_mode" || 
	   $_POST['mode'] == "edit_mode"   ||
	   $_POST['mode'] == "copy_mode"	){

		echo'

	<input type="button" id="password" onclick="setCode()" value="パスワードの暗号化" />

	<input type="button" value="パスワードの生成" onclick="makePass()" />
	<input type="text"   name="keta" id="keta" size="2" value="8" />桁
	<input type="hidden" name="kazu" id="kazu" size="1" value="1" />
	<input type="checkbox" name="suuji" id="suuji" checked />数字
	<input type="checkbox" name="small" id="small" checked />英語小文字
	<input type="checkbox" name="big"   id="big"   checked />英語大文字


	<script type="text/javascript" src="../js/sha256.js"></script>

	<script type="text/javascript">
	function setCode(){
		var emt  = document.getElementsByName("pass").item(0);
		var pass = emt.value;

		if(pass == ""){
			alert("パスワードが入力されていません");
			return;
		}

		if(confirm(pass + "　パスワードを暗号化します")){
			emt.value = SHA256(pass);		
		}
	}


	//make random password
	//半角数字変換用文字定義
	half = "0123456789";
	full = "０１２３４５６７８９";
	function chgMessHalf(VAL){

  		messIn = VAL;
  		messOut = "";

  		for(i=0; i<messIn.length; i++){
    			oneStr = messIn.charAt(i);
    			num = full.indexOf(oneStr,0);
    			oneStr = num >= 0 ? half.charAt(num) : oneStr;
    			messOut += oneStr;
  		}

  		//数字か空かチェック
  		if(isNaN(messOut) || messOut==""){
    			err = "on";
  		}

  		return messOut;
	}//end chgMessHalf


	function makePass(){

  		//エラーフラグ
  		err = "off";

  		//theObj = eval("document.passForm");

  		//keta = chgMessHalf(theObj.keta.value);
  		//kazu = chgMessHalf(theObj.kazu.value);
  		keta = chgMessHalf(document.getElementById("keta").value);
  		kazu = chgMessHalf(document.getElementById("kazu").value);

  		//文字定義
  		moji = "";
  		if(document.getElementById("suuji").checked){
    			moji += "0123456789";
  		}
  		if(document.getElementById("small").checked){
    			moji += "abcdefghijklmnopqrstuvwxyz";
  		}
  		if(document.getElementById("big").checked){
    			moji += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  		}
  		if(err == "off"){
    			pass = "";
    			//パスワード生成
    			for(i=0; i< kazu; i++){
      				for(j=0; j< keta; j++){
        				num = Math.floor(Math.random() * moji.length);
        				pass += moji.charAt(num);
      				}
      				pass += "\n";
    			}
   			//theObj.viewPass.value = pass;
			var emt  = document.getElementsByName("pass").item(0);
			emt.value = pass;

 		} else {
    			alert("数字を入力してください。");
  		}//end makePass
	}

	</script>
		'; //end echo

	} //endif mode
}

//report
function report_manage(){

	//**** レポートの対応処理 ****
	if($_POST['mode'] == "append_mode" || 
	   $_POST['mode'] == "edit_mode"   ||
	   $_POST['mode'] == "copy_mode"	){

		echo'

	<input type="button" onclick="repodelete()"  value="レポート完了（非表示）" />
	<input type="button" onclick="reporestart()" value="レポート復活（表示）" />
	<input type="button" onClick="flgset(1)" value="未対応(1)" />
	<input type="button" onClick="flgset(2)" value="対応中(2)" />
	<input type="button" onClick="flgset(3)" value="対応完了(3)" />

	<script type="text/javascript">
	function repodelete(){
		var emt  = document.getElementsByName("id").item(0);
		emt.value = "DELETE";		
	}
	function reporestart(){
		var emt  = document.getElementsByName("id").item(0);
		emt.value = "";		
	}

	function flgset(no){
		var emt  = document.getElementsByName("repo_flg").item(0);
		emt.value = no;		
	}

	</script>
		'; //end echo

	} //endif mode
}

//lightclass
function set_imagefile($target){

	//**** 画像ファイル名を設定する処理 ****
	if($_POST['mode'] == "append_mode" || 
	   $_POST['mode'] == "edit_mode"   ||
	   $_POST['mode'] == "copy_mode"	){

		$dir = opendir($target);
		while (($file = readdir($dir)) !== false) {
			if ($file != "." && $file != "..") {
				print("<input type='radio' name='icon_name' value='" . $file . "' />");
				print("<img name='icon_image' src='" . $target . $file . "' width='20' />｜");
				//print($file);
			}
		}
		closedir($dir);

		echo'
	<br />
	<input type="button" id="imagefile" onclick="setIcon()" value="アイコン用画像選択" />

	<script type="text/javascript">
	function setIcon(){
		var image       = document.getElementsByName("image").item(0);
		var image_size  = document.getElementsByName("image_size").item(0);

		var icon_name  = document.getElementsByName("icon_name");
		var icon_image = document.getElementsByName("icon_image");

		for(var i = 0 ; i < icon_name.length ; i++){
			if(icon_name.item(i).checked){
				image.value = icon_name.item(i).value;

				//画像サイズ取得
				var getimage = new Image();
				getimage.src = icon_image.item(i).src;

				image_size.value = getimage.width + "," + getimage.height;

				return;
			}
		}
		alert("画像が選択されていません");
	}
	</script>
		'; //end echo


	} //endif mode
}

?>
