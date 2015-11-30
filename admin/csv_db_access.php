<?php
//======= csv database アクセス用の共用ルーチン ==============

function kanri_DB_access(){
	global $Proc_name;
	global $Table_name;
        global $Table_struc;
	global $Table_rows;
	global $Key_row;
        global $JYOUKEN_session;
	global $SORTJKN_session;
	global $db;
	global $sql_server;

	global $db_host;
	global $db_id;
	global $db_pw;
	global $db_name;

	global $PAGE_datarow;
	global $DATAMAX_session;
	global $PAGESTART_session;
	global $PAGEEND_session;
  	//global $PAGE_start;

	global $ThisFile;
	$ReturnFile  = $ThisFile;
	$Proc_name   = $ThisFile;


	//$ReturnFile = "index.php";
	//$Proc_name  = "index.php";

	if(!isset($_POST['mode'])){
		$_POST['mode']="";
	}

	switch($_POST['mode']){
		case "" :
	        	kanri_data_get();
		
        		print("<form method='POST' action='" . $Proc_name . "'>");
	        	print("<table border=1>");
        		Hyoudai(0);
        		Input_data("select","");
        		print("</table>");
			print("<input type='hidden' name='mode' value='select_mode'>");
        		print("<input type='hidden' name='sub_mode' value='first'>");
        		print("<input type='submit' value='検索実行'>");

        		print("<input type='reset' value='条件初期化'></form><br><br>");
        		print("<form method='POST' action='" . $ReturnFile . "'>");
			print("<input type='hidden' name='mode' value=''>");
        		//print("<input type='submit' value='戻る'>");
        		print("<button onClick='window.close();'>閉じる</button>");
        		print("</form>");
			break;
  		case "select_mode" :
		     //データ一覧-------------

			if($_POST['sub_mode']=="first"){
        		//---初期条件の抽出--------
           			$jyouken  = "";
           			$jk_count = 0;
           			for($s=0 ; $s <= $Table_rows-1  ; $s++){
               				if(isset($_POST[$Table_struc[$s][0]]) && $_POST[$Table_struc[$s][0]]<>""){
						if($jk_count<>0){
                     					$jyouken = $jyouken . " and ";
						}
                  				switch($Table_struc[$s][1]){
                    					case "N" :
                      						$jyouken = $jyouken . " " . $Table_struc[$s][0] . " = " . trim($_POST[$Table_struc[$s][0]]);
								break;
                    					case "C" :
                      						$jyouken = $jyouken . " " . $Table_struc[$s][0] . " LIKE '%" . trim($_POST[$Table_struc[$s][0]]) . "%'";
								break;
                    					case "D" :
								break;
                  				}
                  				$jk_count = $jk_count + 1;
					}	
               			}

           			if($jk_count<>0){
              				$jyouken = " where " . $jyouken;
           			}

	   			//sort 条件の付加------------
           			$sort_jyouken = "";
	   			$jk_count = 0;
	   			for($s=0 ; $s <= $Table_rows-1 ; $s++){
					$temp = 'sort_' . $Table_struc[$s][0];
					if(isset($_POST[$temp])){
               					$Look_up = trim($_POST[$temp]);
					}else{
						$Look_up ="";
					}
               				if($Look_up <> ""){
		  				if($jk_count<>0){
                     					$sort_jyouken = $sort_jyouken . " , ";
                  				}
		  				$sort_jyouken = $sort_jyouken . " " . $Table_struc[$s][0]  . " " . $Look_up;
                  				$jk_count = $jk_count + 1;
               				}
	   			}

           			if($jk_count<>0){
              				$sort_jyouken = " order by " . $sort_jyouken;
           			}
           			//---------------------------

           			//--検索条件をセッション変数に格納-----
           			$_SESSION[$JYOUKEN_session] = $jyouken;
           			$_SESSION[$SORTJKN_session] = $sort_jyouken;
           			//-------------------------------

				$PAGE_start = 1;
				$_SESSION[$PAGESTART_session] = $PAGE_start;	//表示開始のページ番号
				//-------------------------
        		//----------------------%>
      			}else{
            			//修正、追加等で戻ってきたとき　セッション変数から検索条件を再設定
            			$jyouken = $_SESSION[$JYOUKEN_session];
            			$sort_jyouken = $_SESSION[$SORTJKN_session];

				$PAGE_start = $_SESSION[$PAGESTART_session];

				if($_POST['sub_mode'] == "page_cont"){
					$PAGE_start = $_POST['page_no'];
				}
         		}


     			if($_POST['sub_mode']=="first"){
           			print("<form method='POST' >");
           			print("<input type='button' value='戻る' onclick='history.go(-1)'>");
           			print("</form>");
     			}else{
           			print("<form method='POST' action='" . $Proc_name ."'>");
           			print("<input type='hidden' name='mode' value='' >");
           			print("<input type='submit' value='戻る'>");
           			print("</form>");
     			}


			//データ件数のチェック
			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name . " " . $jyouken . $sort_jyouken ,$db);
     			$data_all = mysql_num_rows($dbc);
			$page_end = ceil( $data_all / $PAGE_datarow );	//ページ数

     			print("検索条件：" . $jyouken . $sort_jyouken . "<br>");                 
    			print("データ件数：" . $data_all);

			//ページの制御

			if($PAGE_start == 1){
?>
			　　
			<input type='button' value='＜' />
<?php
			}else{
?>
			　　
			<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"select_mode","sub_mode":"page_cont","page_no":"<?=($PAGE_start - 1)?>"})' value='＜' />
<?php
			}
?>			

			<select id='pagelist' onChange='pre_execPost("<?=$Proc_name?>",{"mode":"select_mode","sub_mode":"page_cont","page_no":""})' >

<?php
			for($i = 1 ; $i <= $page_end ; $i++){
				print('<option value="' . $i . '"');
				if($i == $PAGE_start){
					print(' selected');
					//ページ番号記憶
					$_SESSION[$PAGESTART_session] = $PAGE_start;
				}
				print(' >' .$i . 'ページ</option>');
			}
?>
			</select>

<?php
			if($PAGE_start == $page_end){
?>
			<input type='button' value='＞' />
<?php
			}else{
?>
			<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"select_mode","sub_mode":"page_cont","page_no":"<?=($PAGE_start + 1)?>"})' value='＞' />

<?php
			}


			//表示スタート
			$data_start = ($PAGE_start - 1) * $PAGE_datarow;

			$dbc = mysql_query("select * from " . $Table_name . " " . $jyouken . $sort_jyouken . " limit " . $data_start . "," . $PAGE_datarow ,$db);
     			$data_count = mysql_num_rows($dbc);


        		print("<table><tr>");
        		print("<td><form method='POST' action='" . $Proc_name . "'>");
            		print("<input type='hidden' name='mode' value='append_mode'>");
            		print("<input type='submit' value='新規データ追加'></td>");
        		print("<td></form></td>");

        		print("<td><form method='POST' action='" . $Proc_name . "'>");
            		print("<input type='hidden' name='mode' value='inst_mode'>");
            		print("<input type='submit' value='任意並び替え'></td>");
        		print("<td></form></td>");
        		print("</tr></table>");

		        print("<table border=1>");

		        Hyoudai(1);

	
        		for($s=0 ; $s < $data_count ; $s++){
				mysql_data_seek($dbc,$s);
				$now_data = mysql_fetch_assoc($dbc);
           			print("<tr>");
           			if($s==0){
                 			print("<td></td>");
           			}else{
?>
				<td>
				<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"move_mode","sub_mode":"up","select_no":"<?=$now_data[$Table_struc[$Key_row][0]]?>"})' value='↑' />
				</td>

<?php
           			}
           			if($s==$data_count-1){
                 			print("<td></td>");
           			}else{
?>
				<td>
				<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"move_mode","sub_mode":"down","select_no":"<?=$now_data[$Table_struc[$Key_row][0]]?>"})' value='↓' />
				</td>

<?php
           			}

           			Data_show($now_data);
?>

				<td>
				<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"edit_mode","select_no":"<?=$now_data[$Table_struc[$Key_row][0]]?>"})' value='Edit' />
				</td>
				<td>
				<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"copy_mode","select_no":"<?=$now_data[$Table_struc[$Key_row][0]]?>"})' value='Copy' />
				</td>
				<td>
				<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"delete_mode","select_no":"<?=$now_data[$Table_struc[$Key_row][0]]?>"})' value='Dele' />
				</td>
<?php
        		}

        		print("</table>");

        		mysql_close($db);

        		print("<table><tr>");
        		print("<td><form method='POST' action='" . $Proc_name . "'>");
            		print("<input type='hidden' name='mode' value='append_mode'>");
            		print("<input type='submit' value='新規データ追加'></td>");
        		print("<td></form></td>");

        		print("<td><form method='POST' action='" . $Proc_name . "'>");
            		print("<input type='hidden' name='mode' value='inst_mode'>");
            		print("<input type='submit' value='任意並び替え'></td>");
        		print("<td></form></td>");
        		print("</tr></table>");

			//ページの制御
			if($PAGE_start == 1){
?>
			　　
			<input type='button' value='＜' />
<?php
			}else{
?>
			　　
			<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"select_mode","sub_mode":"page_cont","page_no":"<?=($PAGE_start - 1)?>"})' value='＜' />
<?php
			}
?>						
			<?=$PAGE_start?>page

<?php
			if($PAGE_start == $page_end){
?>
			<input type='button' value='＞' />
<?php
			}else{
?>
			<input type='button' onClick='execPost("<?=$Proc_name?>",{"mode":"select_mode","sub_mode":"page_cont","page_no":"<?=($PAGE_start + 1)?>"})' value='＞' />

<?php
			}

       			print("<br><br>");

     			if($_POST['sub_mode']=="first"){
           			print("<form method='POST' >");
           			print("<input type='button' value='戻る' onclick='history.go(-1)'>");
           			print("</form>");
     			}else{
           			print("<form method='POST' action='" . $Proc_name . "'>");
           			print("<input type='hidden' name='mode' value='' >");
           			print("<input type='submit' value='戻る'>");
           			print("</form>");
     			}


			break;

  		case "append_mode" :
     			//新規追加登録-------------

          		kanri_data_get();

          		print("<br>");
          		print("データ新規登録<br>");
          		print("<form method='POST' action='" . $Proc_name . "'>");
          		print("<table border=1>");

          		Hyoudai(2);

          		Input_data("append","");

          		print("</table>");

          		print("<input type='submit' value='登録'>");
          		print("<input type='hidden' name='mode' value='append_exec'>");
          		print("</form>");

          		print("<form method='POST' >");
          		print("<input type='button' value='戻る（登録中止）' onclick='history.go(-1)'>");
          		print("</form>");
	
			break;

		case "copy_exec"   :
  		case "append_exec" :
     			//登録実行--------------
                        //入力データのエラーチェック
       			if(Input_error_check()==-1){
           			exit();
        		}

     			//<% kanri_data_get() %>

     			//登録実行時のチェック実行==========
			Appen_check();
       			//================================== %>

			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$query1 = "insert into " .$Table_name . " ( ";
                        $query2 = " values ( ";

        		for($s=0 ; $s <= $Table_rows-1 ; $s++){
	   			if($Table_struc[$s][3]=="*"){
					$syori_sw = $Table_struc[$s][7];
					//Auto_Input($s , "append" , $syori_sw );
	   			}else{
					$query1 = $query1 . $Table_struc[$s][0] ;
           				if($Table_struc[$s][1]=="N"){
						$query2 = $query2 . trim($_POST[$Table_struc[$s][0]]);
					}else{
						$query2 = $query2 . "'" .  trim($_POST[$Table_struc[$s][0]]) . "'";
					}
		   		}
				if($s==$Table_rows-1){
					$query1 = $query1 . ") ";
					$query2 = $query2 . ") ";
				}else{
					$query1 = $query1 . ", ";
					$query2 = $query2 . ", ";
				}
	      		}

			print($query1 . $query2 . "<br>");

			$dbc=mysql_query($query1 . $query2 ,$db);
			if($dbc){
			}else{
				print($dbc . "データ行の追加失敗（insert)<br>");
        			mysql_close($db);
				//exit();				
			}
        		mysql_close($db);

        		print("<br>");
        		print($Table_name . "<br>");
        		print("<br>");
        		print("データ新規登録完了しました。<BR>");
//exit();
			jamp_select_mode();

                        break;

  		case "copy_mode" :
     			//複製データ登録-------------
     			kanri_data_get();

			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name . " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']) ,$db);
     			$data_count = mysql_num_rows($dbc);
                 
     			if($data_count == 0){ 
           			print("修正する該当のデータがありません。<BR><BR>");
          			print("<form method='POST' >");
          			print("<input type='button' value='戻る' onclick='history.go(-1)'>");
          			print("</form>");
        			exit();
        		}

			$now_data = mysql_fetch_assoc($dbc);
     			mysql_close();

     			print("<br>");
     			print("複製データ<br>");
     			print("<form method='POST' action='" . $Proc_name . "'>");
     			print("<table border=1>");

     			Hyoudai(2);

     			Input_data("copy",$now_data);

     			print("</table>");



     			print("<input type='submit' value='複製登録'>");
     			print("<input type='hidden' name='mode' value='copy_exec'><br>");
     			print("<input type='hidden' name='select_no' value='" . $_POST['select_no'] . "'><br>");
     			print("</form>");

     			print("<form method='POST' >");
     			print("<input type='button' value='戻る（複製登録中止）' onclick='history.go(-1)'>");
     			print("</form>");

			break;

  		case "edit_mode" :
     			//登録データ修正-------------
     			kanri_data_get();

			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name . " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']) ,$db);
     			$data_count = mysql_num_rows($dbc);
                 
     			if($data_count == 0){ 
           			print("修正する該当のデータがありません。<BR><BR>");
          			print("<form method='POST' >");
          			print("<input type='button' value='戻る' onclick='history.go(-1)'>");
          			print("</form>");
        			exit();
        		}

			$now_data = mysql_fetch_assoc($dbc);

     			print("<br>");
     			print("修正データ<br>");
     			print("<form method='POST' action='" . $Proc_name . "'>");
     			print("<table border=1>");

     			Hyoudai(2);

     			Input_data("edit",$now_data);

     			print("</table>");

     			mysql_close();

     			print("<input type='submit' value='修正'>");
     			print("<input type='hidden' name='mode' value='edit_exec'><br>");
     			print("<input type='hidden' name='select_no' value='" . $_POST['select_no'] . "'><br>");
     			print("</form>");

     			print("<form method='POST' >");
     			print("<input type='button' value='戻る（修正中止）' onclick='history.go(-1)'>");
     			print("</form>");

			break;


  		case "edit_exec" :
     			//修正実行--------------

     			//入力データのエラーチェック
        		if(Input_error_check()==-1){
           			exit();
        		}

     			//kanri_data_get() %>

     			//修正実行時のチェック実行==========
			Edit_check();
        		//==================================

			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			//$dbc = mysql_query("select * from " . $Table_name . " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']) ,$db);
     			//$data_count = mysql_num_rows($dbc);

			$w_jyouken = " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']);

			print("select * from " . $Table_name . " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']) . "<br>");
			//print("data_count : " . $data_count);

        		for($s=0 ; $s <= $Table_rows-1 ; $s++){
	   			if($Table_struc[$s][3]=="*"){
					$syori_sw = $Table_struc[$s][7];
					//Auto_Input(s , "edit" , syori_sw );		
	   			}else{ 
					if($Table_struc[$s][1]=="N"){
						mysql_query("update " . $Table_name . " set " . $Table_struc[$s][0] . " =  " . trim($_POST[$Table_struc[$s][0]]) . $w_jyouken ,$db);
					}else{
						mysql_query("update " . $Table_name . " set " . $Table_struc[$s][0] . " =  '" . trim($_POST[$Table_struc[$s][0]]) . "'" . $w_jyouken,$db);
					}
	   			}
        		}

        		mysql_close();

        		print("<br>");
        		print("データ修正完了しました。<BR>");

        		jamp_select_mode();

                        break;

  		case "move_mode" : 
    	 		//データ並び替え-------------

        		//Redim mem_data(2,Table_rows)
           		//(0,*)=元データ　　　(1,*)=移動先データ

        		//セッション変数から条件データを再設定----
            		$jyouken      = $_SESSION[$JYOUKEN_session];
            		$sort_jyouken = $_SESSION[$SORTJKN_session];
           		//----------------------------------------

        		//移動するデータをメモリに保存
			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name . " " . $jyouken . $sort_jyouken ,$db);
     			$data_count = mysql_num_rows($dbc);

			for($m = 0 ; $m < $data_count ; $m++){
                        	mysql_data_seek($dbc,$m);
				$now_data = mysql_fetch_assoc($dbc);
				if($now_data[$Table_struc[$Key_row][0]] == $_POST['select_no']){
					$base_rec = $m;
        				$i = 0;
					$mem_Key[$i] = $now_data[$Table_struc[$Key_row][0]];
           				for($s= 0 ; $s <= $Table_rows-1 ; $s++){
               					$mem_data[$i][$s] = $now_data[$Table_struc[$s][0]];
           				}
					break;
				}
			}

        		if($_POST['sub_mode']=="up"){
              			$move_rec = $base_rec - 1;
           		}else{
              			$move_rec = $base_rec + 1;
           		}
	
			mysql_data_seek($dbc,$move_rec);
			$now_data = mysql_fetch_assoc($dbc);
        		$i=1;
           		for($s = 0 ; $s <= $Table_rows-1 ; $s++){
				$mem_Key[$i]      = $now_data[$Table_struc[$Key_row][0]];
               			$mem_data[$i][$s] = $now_data[$Table_struc[$s][0]];
           		}
              
        		//no の入れ替え
			$dumy = $mem_Key[0];
			$mem_Key[0] = $mem_Key[1];
			$mem_Key[1] = $dumy;
			$dumy = $mem_data[0][0];
			$mem_data[0][0] = $mem_data[1][0];
			$mem_data[1][0] = $dumy;

        		//書き戻し
			for($i = 0 ; $i <= 1 ; $i++){ 
				$w_jyouken = " where " . $Table_struc[$Key_row][0] . " = " . $mem_Key[$i];
        			for($s=0 ; $s <= $Table_rows-1 ; $s++){
					if($Table_struc[$s][1]=="N"){
						mysql_query("update " . $Table_name . " set " . $Table_struc[$s][0] . " =  "  . $mem_data[$i][$s] .       $w_jyouken ,$db);
					}else{
						mysql_query("update " . $Table_name . " set " . $Table_struc[$s][0] . " =  '" . $mem_data[$i][$s] . "'" . $w_jyouken ,$db);
					}
        			}
			}

			//Key_row の入れ替え
			mysql_query("update " . $Table_name . " set " . $Table_struc[$Key_row][0] . " =  -1 where " . $Table_struc[$Key_row][0] . " = " . $mem_Key[0] , $db);
			mysql_query("update " . $Table_name . " set " . $Table_struc[$Key_row][0] . " =  -2 where " . $Table_struc[$Key_row][0] . " = " . $mem_Key[1] , $db);

 			mysql_query("update " . $Table_name . " set " . $Table_struc[$Key_row][0] . " = " . $mem_data[1][0] . " where " . $Table_struc[$Key_row][0] . " = -1" , $db);
 			mysql_query("update " . $Table_name . " set " . $Table_struc[$Key_row][0] . " = " . $mem_data[0][0] . " where " . $Table_struc[$Key_row][0] . " = -2" , $db);
         	

			mysql_close();

        		print("<br>");
        		print("データ移動完了しました。<BR>");

			jamp_select_mode();

			break;

   		case "delete_mode" :
			//データ削除-------------
			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name . " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']) ,$db);
     			$data_count = mysql_num_rows($dbc);
                 
     			if($data_count == 0){ 
           			print("修正する該当のデータがありません。<BR><BR>");
          			print("<form method='POST' >");
          			print("<input type='button' value='戻る' onclick='history.go(-1)'>");
          			print("</form>");
        			exit();
        		}

			$now_data = mysql_fetch_assoc($dbc);

      			print("<br>");
      			print("削除データ<br>");
      			print("<form method='POST' action='" . $Proc_name . "'>");
      			print("<table border=1>");

      			Hyoudai(2);
      			Data_show($now_data);

      			print("</table>");

      			mysql_close();

      			print("<input type='submit' value='削除'>");
      			print("<input type='hidden' name='mode' value='delete_exec'><br>");
      			print("<input type='hidden' name='select_no' value='" . $_POST['select_no'] . "'><br>");
      			print("</form>");

      			print("<form method='POST' >");
      			print("<input type='button' value='戻る（削除中止）' onclick='history.go(-1)'>");
      			print("</form>");

			break;

     		case "delete_exec" :
			//削除実行--------------
			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("delete from " . $Table_name . " where " . $Table_struc[$Key_row][0] . "=" . trim($_POST['select_no']) ,$db);
                        mysql_close();

	       		print("<br>"); 
        		print("データ削除完了しました。<BR>");

			jamp_select_mode();

               		break;

   		case "inst_mode" :
			//並び替え-------------

        		//並び替えのため、セッション変数の条件データをクリア
            		$_SESSION[$JYOUKEN_session] = "";
            		$_SESSION[$SORTJKN_session] = "";
           		//----------------------------------------

			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name ,$db);
     			$data_count = mysql_num_rows($dbc);
                 
    			print("データ件数：" . $data_count);

        		print("<form>");
        		print("<input type='button' value='戻る' onclick='history.go(-1)'>");
        		print("</form>");

        		print("右端入力欄に並べ替え順を設定してください。<br>（処理後[" . $Table_struc[$Key_row][4] . "]は初期化されます。）<br>");

        		print("<table border=1>");
        		Hyoudai(2);

        		print("<form method='post' action='" .$Proc_name . "'>");
        		for($s=0 ; $s < $data_count ; $s++){
				mysql_data_seek($dbc,$s);
				$now_data = mysql_fetch_assoc($dbc);
           			print("<tr>");
				Data_show($now_data);
				print("<td>");
				print("<input type='text' name='inst_" . $s. "' size=4  value='" . ($s+1) . "' >");
				print("</td>");
				print("</tr>");
               		}

        		print("</table>");

        		print("<input type='hidden' name='mode' value='inst_exec'>");
        		print("<input type='submit' value='並べ替え実行'></form>");

       			mysql_close();

        		print("<br>");
        		print("<form>");
        		print("<input type='button' value='戻る' onclick='history.go(-1)'>");
        		print("</form>");

			break;


   		case "inst_exec" :
			//入れ替え、再番号-------------


			$db         = mysql_connect($db_host,$db_id,$db_pw);
			$sql_server = mysql_select_db($db_name,$db);

			mysql_query('SET NAMES utf8'); //

			$dbc = mysql_query("select * from " . $Table_name ,$db);
     			$max_rec = mysql_num_rows($dbc);
 
       		 	//0以下の番号指示チェック
        		for($s=0 ; $s < $max_rec ; $s++){
          			if(Num_check($_POST['inst_' . $s])==""){
              				print($s+1 . "番目：数字を入力してください。<br>");
               				print("<form>");
               				print("<input type='button' value='戻る' onclick='history.go(-1)'>");
               				print("</form>");
					exit();
        			}else{
					//並び替え指示データ
               				$data_key[$s] = Num_check(trim($_POST['inst_' . $s]));
               			}
        		}

			//並び替え指示作成
			asort($data_key,SORT_NUMERIC);

			$sort_key[0] = key($data_key);
			for($s=1 ; $s < $max_rec ; $s++){
				next($data_key);
				$sort_key[$s] = key($data_key);
			}

			//temp_db（中間ＤＢ） 作成
			//出現しない条件を与えて、テーブル構造のみ複写
			if(mysql_query("drop table temp_db",$db)){
			}else{
				print("warrning: " . mysql_error() . "<br><br>");
			}
			$query = "create table temp_db as select * from " . $Table_name . " where " . $Table_struc[$Key_row][0] . " = -1 " ;

			//print($query);

			if(mysql_query($query,$db)){
			}else{
				print("error:" . mysql_error() . "<br>");
			}

			//$Table_name から　temp_db へ任意並び替えでデータ複写
			$dbc = mysql_query("select * from " . $Table_name ,$db);
			for($i = 0 ; $i < $max_rec ; $i++){
				//並び替え順配列 $sort_key[]　によりデータを順次読み出し
				//print("sort_key : " . $sort_key[$i] . "<br>");
				mysql_data_seek($dbc,($sort_key[$i]));
				$now_data = mysql_fetch_assoc($dbc);

				$query1 = "insert into temp_db ( ";
        	                $query2 = " values ( ";

        			for($s=0 ; $s <= $Table_rows-1 ; $s++){
					$query1 = $query1 . $Table_struc[$s][0] ;
					if($s == $Key_row){
						//$key_row は強制的に順位に変更する
                                        	$query2 = $query2 . ($i+1);
					}else{
       						if($Table_struc[$s][1]=="N"){
							$query2 = $query2 . $now_data[$Table_struc[$s][0]];
						}else{
							$query2 = $query2 . "'" .  $now_data[$Table_struc[$s][0]] . "'";
						}
					}

					if($s==$Table_rows-1){
						$query1 = $query1 . ") ";
						$query2 = $query2 . ") ";
					}else{
						$query1 = $query1 . ", ";
						$query2 = $query2 . ", ";
					}
	      			}

				//print($query1 . $query2 . "<br>");

				$dbw=mysql_query($query1 . $query2 ,$db);
				if($dbw){
				}else{
					print($dbw . "データ行の追加失敗（insert)<br>");
        				mysql_close($db);
					//exit();				
				}
			}

			//元ＤＢを削除
			if(mysql_query("drop table " . $Table_name , $db)){
			}else{
				print("error:" . mysql_error() . "<br>");
			}
			//temp_db を　元ＤＢにリネーム
			if(mysql_query("alter table temp_db rename to " . $Table_name ,$db)){
			}else{
				print("error:" . mysql_error() . "<br>");
			}

        		mysql_close($db);

			jamp_select_mode();
			break;

	}
}
?>





<?php
//自動クリックでselect_modeへジャンプ
function jamp_select_mode(){
	global $Proc_name;

	print("<form method='POST' name='auto_form' action='" .$Proc_name . "'>");
	print("<input type='hidden' name='mode' value='select_mode'>");
	print("<input type='hidden' name='sub_mode' value='second'>");
	print("<input type='submit' name='auto_click' value='click here'>");
	print("</form>");
	print("<script language='javascript'>");
	print("document.forms['auto_form'].elements['auto_click'].click();");
	print("</script>");
}

//入力データのエラーチェック
function Input_error_check(){
   	//return  0 : No Error
   	//return -1 : Error In
	global $Table_rows;
	global $Table_struc;

   	$Error=0;
   	for($s=0 ; $s <= $Table_rows-1 ; $s++){
       		switch($Table_struc[$s][1]){
         		case "N" :
            			if(Num_check($_POST[$Table_struc[$s][0]])==""){
               				print($Table_struc[$s][0] . "＝数値を入力してください。<br>");
               				$Error++;
            			}
				break;
         		case "C" :
            			if(strlen($_POST[$Table_struc[$s][0]]) > $Table_struc[$s][2]){
               				print($Table_struc[$s][0] . "＝文字の桁数が多すぎます。（" . $Table_struc[$s][2] . "以下）<br>");
               				$Error++;
            			}
				break;
         		case "D" :
       		}
   	}

   	if($Error <> 0 ){
      		print("<form method='POST' >");
      		print("<input type='button' value='戻る' onclick='history.go(-1)'>");
      		print("</form>");
      		return -1;
	}else{
     	 	return 0;
   	}
}



//データ表題表示
function Hyoudai($sw){
    //$sw 0:制御項目なし（sortあり）
    //    1:制御項目あり（sortなし）
    //    2:制御項目なし（sortなし）
    //    3:制御項目あり（sortあり）

	global $Table_rows;
	global $Table_struc;

	if($sw==0 || $sw==3){
		//データの並び替え用のフィールド
        	print("<tr><td colspan=" . $Table_rows . ">");
		print("Sort条件チェック：　○昇順　○降順</td></tr>");
		print("<tr>");
		for($i=0 ; $i <= $Table_rows-1 ; $i++){
           		if($Table_struc[$i][3] ==""){
	   			print("<td align='center'>");
				print("<input type='radio' name='sort_" . $Table_struc[$i][0] ."' value='ASC'>");
				print("<input type='radio' name='sort_" . $Table_struc[$i][0] ."' value='DESC'>");
	   			print("</td>");
	   		}
        	}
        	print("</tr>");
    	}

    	print("<tr>");
        if($sw==1 || $sw==3){
           	print("<th>Up</th>");
           	print("<th>Dw</th>");
        }

    	for($i=0 ; $i <= $Table_rows-1 ; $i++){
       		if($Table_struc[$i][3]==""){
	        	print("<th>" . $Table_struc[$i][4] . "</th>");
       		}
    	}

        if($sw==1 || $sw==3){
           	print("<th>修正</th>");
           	print("<th>複製</th>");
           	print("<th>削除</th>");
        }
    	print("</tr>");
}


//テーブルデータの表示
function Data_show($now_data){
	global $Table_rows;
	global $Table_struc;

  	for($i=0 ; $i <= $Table_rows-1 ; $i++){
     		if($Table_struc[$i][3]==""){
            		if($Table_struc[$i][1]=="N"){
				print("<td align='right'>");
            		}else{
                		print("<td>");
	    		}
	    		print($now_data[$Table_struc[$i][0]]);
            		print("</td>");
     		}
 	}
}


//データの入力フォーマット
function Input_data($mode,$now_data){
    	//mode: append / edit / copy / select
	global $Table_struc;
	global $Table_rows;
	global $Mem_buffer;
	global $Counter;

	print("<tr>");
  	for($i=0 ; $i < $Table_rows ; $i++){

     		if($Table_struc[$i][3]==""){
            		if($Table_struc[$i][1]=="N"){
				print("<td align='right'>");
            		}else{
                		print("<td>");
	    		}

              		switch($Table_struc[$i][5]){
				case "auto" :
					//個別処理
			   		$syori_sw = $Table_struc[$i][7];
			   		Auto_Input($i , $mode , $syori_sw , $now_data );
					break;
				case "input" :
					switch($mode){
            					case "edit" :
                     			 		print("<input type='text' name='" . $Table_struc[$i][0] . "' size=" . $Table_struc[$i][6] . " value='" . trim($now_data[$Table_struc[$i][0]]) . "'>");
							break;
            					case "copy"  :
		                     			print("<input type='text' name='" . $Table_struc[$i][0] . "' size=" . $Table_struc[$i][6] . " value='" . trim($now_data[$Table_struc[$i][0]]) . "'>");
							break;
            					case "append" :
                     			 		print("<input type='text' name='" . $Table_struc[$i][0] . "' size=" . $Table_struc[$i][6] . " value=''>");
							break;
            					case "select" :
                     			 		print("<input type='text' name='" . $Table_struc[$i][0] . "' size=" . $Table_struc[$i][6] . " value=''>");
							break;
         				}
                                        break;
				case "select" :
					print("<select name='" . $Table_struc[$i][0] . "' size=1>");
					switch($mode){
            					case "edit" :
                     					print("<option value='" . trim($now_data[$Table_struc[$i][0]]) . "'>"  . trim($now_data[$Table_struc[$i][0]]) . "</option>");
							break;
            					case "copy" :
                     					print("<option value='" . trim($now_data[$Table_struc[$i][0]]) . "'>"  . trim($now_data[$Table_struc[$i][0]]) . "</option>");
							break;
            					case "append" :
         					case "select" :
                                                	print("<option value='' ></option>");
							break;
					}

					for($s=0 ; $s < $Counter[$Table_struc[$i][7]][1] ; $s++){
      						print("<option value='" . $Mem_buffer[$Table_struc[$i][7]][$s][0] . "'>" . $Mem_buffer[$Table_struc[$i][7]][$s][1] . "【" . $Mem_buffer[$Table_struc[$i][7]][$s][0] ."】</option>");
					}
					print("</select>");
	                               	break;
				case "text" :
					switch($mode){
						case "edit" :
							print("<textarea name='" . $Table_struc[$i][0] . "' rows=2 cols=" . $Table_struc[$i][6] . ">" . trim($now_data[$Table_struc[$i][0]]) . "</textarea>");
							break;
						case "copy" :
							print("<textarea name='" . $Table_struc[$i][0] . "' rows=2 cols=" . $Table_struc[$i][6] . ">" . trim($now_data[$Table_struc[$i][0]]) . "</textarea>");
							break;
						case "append" :
							print("<textarea name='" . $Table_struc[$i][0] . "' rows=2 cols=" . $Table_struc[$i][6] . "></textarea>");
							break;
						case "select" :
							print("<textarea name='" . $Table_struc[$i][0] . "' rows=2 cols=" . $Table_struc[$i][6] . "></textarea>");
							break;
					}
					break;
				}
		}
    		print("</td>");

  	}
  	print("</tr>");
}

?>
