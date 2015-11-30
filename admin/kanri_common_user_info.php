<?php
//========= kanriデータ テーブル共用のファイル構造情報　情報表示用等　個別ルーチン ==========

//このＰＲＯＣで使用するテーブル名
   //$Table_name = $_session['KANRI_table']; 
   $Table_name = "common_user";

//kanri data の構造
   $Table_rows = 16;
   $Key_row   = 0;  	//--- no が主キー
   //Redim $Table_struc[Table_rows][8];
       //(*,0)=ﾌｨｰﾙﾄﾞ名   (*,1)=ﾌｨｰﾙﾄﾞﾀｲﾌﾟ<N,C,D>   (*,2)=桁数<数字は右寄せ>  (*,3)=自動計算:"*" 
       //(*,4)=表題　　　（*,5)=ﾃﾞｰﾀ入力ﾀｲﾌﾟ<auto,input,select,text>　(*,6)=表示桁数
       //(*,7)= *,5="select"の場合、管理ﾃｰﾌﾞﾙﾊﾞｯﾌｧ番号<Mem_buffer()>、*,5="auto"の場合、auto処理番号

$i=0;
   $Table_struc[$i][0] = "no";		//(*,0)=ﾌｨｰﾙﾄﾞ名
   $Table_struc[$i][1] = "N";		//(*,1)=ﾌｨｰﾙﾄﾞﾀｲﾌﾟ<N,C,D>
   $Table_struc[$i][2] = 4;		//(*,2)=桁数<数字は右寄せ>
   $Table_struc[$i][3] = "";		//(*,3)=自動計算ﾌｨｰﾙﾄﾞのﾌﾗｸﾞ:"*"
   $Table_struc[$i][4] = "no";		//(*,4)=表題
   $Table_struc[$i][5] = "auto";	//(*,5)=ﾃﾞｰﾀ入力ﾀｲﾌﾟ<auto,input,select,text>
   $Table_struc[$i][6] = 4;		//(*,6)=表示桁数
   $Table_struc[$i][7] = 0;		//(*,7)= *,5="select"の場合、管理ﾃｰﾌﾞﾙﾊﾞｯﾌｧ番号<Mem_buffer()>
					//       *,5="auto"の場合、auto処理番号
					//           Sub Auto_Input(no,mode,sw)
   					//               no:ﾌｨｰﾙﾄﾞ番号　mode:edit/append/select  sw:自動実行処理の番号
$i++;
   $Table_struc[$i][0] = "active";
   $Table_struc[$i][1] = "N";
   $Table_struc[$i][2] = 4;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "Act";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 2;
   $Table_struc[$i][7] = "";

$i++;
   $Table_struc[$i][0] = "id";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "User ID";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 10;
   $Table_struc[$i][7] = "";
$i++;
   $Table_struc[$i][0] = "pass";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 256;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "User PW";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 20;
   $Table_struc[$i][7] = "";
$i++; 
   $Table_struc[$i][0] = "name";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "氏名";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 10;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "email";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "メールアドレス";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 20;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "phone";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 20;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "電話番号";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 12;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "house";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "居住地";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 10;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "memo";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "記事";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 20;
   $Table_struc[$i][7] = "";
$i++; 
   $Table_struc[$i][0] = "level";
   $Table_struc[$i][1] = "N";
   $Table_struc[$i][2] = 4;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "レベル";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 2;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "state";
   $Table_struc[$i][1] = "N";
   $Table_struc[$i][2] = 2;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "状態";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 2;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "temp";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 255;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "経過";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 20;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "act_date";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "処理日時";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 14;
   $Table_struc[$i][7] = "";

$i++; 
   $Table_struc[$i][0] = "first_in";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "初回ログイン";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 14;
   $Table_struc[$i][7] = "";
$i++; 
   $Table_struc[$i][0] = "last_in";
   $Table_struc[$i][1] = "C";
   $Table_struc[$i][2] = 50;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "最終ログイン";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 14;
   $Table_struc[$i][7] = "";
$i++; 
   $Table_struc[$i][0] = "count_in";
   $Table_struc[$i][1] = "N";
   $Table_struc[$i][2] = 4;
   $Table_struc[$i][3] = "";
   $Table_struc[$i][4] = "ログイン数";
   $Table_struc[$i][5] = "input";
   $Table_struc[$i][6] = 6;
   $Table_struc[$i][7] = "";


//自動処理が伴う場合の処理（個別ＤＢ毎のプログラム処理）
//function Auto_Input($no,$mode,$sw,$now_data) に処理追加　include.php内


//実行時チェックルーチン
function Appen_check(){

      //特にチェックなし ***************
      //*****************************************
}

function Edit_check(){

      //特にチェックなし ***************
      //*****************************************
}

//管理データの取得
function kanri_data_get(){

	//使用時は、下記コメントアウトを外す	
	//kanri_data_get_bak();
}

?>

