//csvDataBase controler

//conrol width（pxは付けない）
var Field_up  = 20;
var Field_dw  = 20;
var Field_ecd = 120;

/* 呼び出し側のｐｈｐファイルで定義
var Field_etc = new Array();
Field_etc['no']           = 50;
Field_etc['target_label'] = 200;
Field_etc['target_id']    = 120;
Field_etc['color']        = 80;
Field_etc['text_color']   = 80;
Field_etc['icon']         = 150;
Field_etc['memo']         = 400;
*/

//width 定義のないフィールドの初期値
var Field_defwidth = 80;

//table width
var DbTable_width = 0;


//ReadOnly mode のチェック
if(typeof(ReadOnly) == "undefined"){
	var ReadOnly = false;
}

function init(){
	//検索画面の準備
	SearchData(0);

	ShowData();
}

//データの一覧表示
function ShowData(){
	var buff = "";

	if(!ReadOnly){
		buff += "<input type='button' value='データをファイルに保存する' onClick='Mysave()' >　　";
		buff += "<input type='button' value='新規追加' onClick='Myappend()' >　";

		buff += "<input type='button' value='表示条件を設定' onClick='SearchData(1)' >";
	}


	//table width の計算
	DbTable_width = Field_up + Field_dw + Field_ecd;
	for(var s = 0 ; s < DataArray[0].length ; s++){
		if(typeof(Field_etc[DataArray[0][s]]) =="undefined"){
			DbTable_width += Field_defwidth;
		}else{
			DbTable_width += Field_etc[DataArray[0][s]];
		}
	}
	DbTable_width += (s + 5);
	//*****************

	buff += "<table id='DbTable' width='" + DbTable_width + "'>";

	var dlength = DataArray.length;
	var jkn_disp = 0;
	for(i = 0 ; i < dlength ; i++){

		//表示条件のチェック ****
		if(jkn_check(i) == -1){
			//条件外は表示しない
			jkn_disp = 1;
			continue;			
		}
		//********************

		buff += "<tr>";

		if(!ReadOnly){
			if(i == 0){
				//Up Down の列追加
				buff += "<th width='" + Field_up + "'>Up</th>";
				buff += "<th width='" + Field_dw + "'>Dw</th>";
			
				//修正、複写、削除の列追加
				buff += "<th width='" + Field_ecd + "'>処理</th>";
			}else{
				//Up Down の列追加
				if( i == 1){
					buff += "<td align='center' width='" + Field_up + "'></td>";
				}else{
					buff += "<td align='center' width='" + Field_up + "'>";
					//全件表示の場合のみ有効
					if(jkn_disp == 0){
						buff += "<input class='btn_arrow' type='button' value='↑' onClick='Myup(" + i + ")' >";
					}
					buff += "</td>";
				}
				
				if( i == dlength - 1){
					buff += "<td align='center' width='" + Field_dw + "'></td>";
				}else{
					buff += "<td align='center' width='" + Field_dw + "'>";
					//全件表示の場合のみ有効
					if(jkn_disp == 0){
						buff += "<input class='btn_arrow' type='button' value='↓' onClick='Mydw(" + i + ")' >";
					}
					buff += "</td>";
				}

				//修正、複写、削除の列追加
				//buff += "<td width='" + Field_ecd + "' nowrap >";
				buff += "<td align='center' nowrap>";
				buff += "<input class='btn_ecd' type='button' value='修正' onClick='Myedit(" + i + ")' >";
				buff += "<input class='btn_ecd' type='button' value='複製' onClick='Mycopy(" + i + ")' >";
				buff += "<input class='btn_ecd' type='button' value='削除' onClick='Mydele(" + i + ")' >";
				buff += "</td>";

			}
		}
		
		var field_width = 0;
		for(s = 0 ; s < DataArray[i].length ; s++){

			//フィールドサイズの設定の確認
			if(typeof(Field_etc[DataArray[0][s]]) =="undefined"){
				field_width = Field_defwidth;
			}else{
				field_width = Field_etc[DataArray[0][s]];
			}

			if( i == 0){
				buff += '<th width="' + field_width + '" >';
				//buff += '<th width="' + Field_etc[DataArray[0][s]] + '" >';
				//buff += '<th>';
			}else{
				buff += '<td width="' + field_width + '" >';
				//buff += '<td width="' + Field_etc[DataArray[0][s]] + '" >';
				//buff += "<td>";
			}

			buff += DataArray[i][s];

			//************
			//sort
			if( i == 0 ){
				buff += "<br />";
				//buff += "<input type='text'  value='' size='6' />";
				//buff += "<input type='text'  id='search_" + DataArray[0][s] + "' value='' style='width:" + (field_width - 10) + "px;' />";
				//buff += "<br />";
				buff += "<input class='btn_arrow' type='button' onClick='SortAsc(" + s + ")' value='▲' id='sortasc_" + s + "' />";
				buff += "<input class='btn_arrow' type='button' onClick='SortDsc(" + s + ")' value='▼' id='sortdsc_" + s + "' />";
			}
			//************

			if( i == 0){
				buff += "</th>";
			}else{
				buff += "</td>";
			}
		}

		buff += "</tr>";
	}

	buff += "</table>";

	if(!ReadOnly){
		buff += "<input type='button' value='データをファイルに保存する' onClick='Mysave()' >　　";
		buff += "<input type='button' value='新規追加' onClick='Myappend()' >　";

		buff += "<input type='button' value='表示条件を設定' onClick='SearchData(1)' >";
	}

	$('#list').html(buff);
}


//データの並び替え
function SortAsc(no){
	//0:フィールドデータを保存
	var fdata  = DataArray[0];
	//配列からfdataを削除（先頭）
	DataArray.shift();

	//DataArray を指定のフィールドで昇順ソートする
	//降順は、-1 , 1 を逆にする
	field = no;
	DataArray.sort(function(a,b){
		if(a[field] < b[field]) return -1;
		if(a[field] > b[field]) return 1;
		//if(a[ev_open] < b[ev_open]) return -1;
		//if(a[ev_open] > b[ev_open]) return 1;
		return 0;
	});

	//fdataを配列の先頭に追加
	DataArray.unshift(fdata);

	ShowData();
}

function SortDsc(no){
	//0:フィールドデータを保存
	var fdata  = DataArray[0];
	//配列のからfdataを削除（先頭）
	DataArray.shift();

	field = no;
	DataArray.sort(function(a,b){
		if(a[field] < b[field]) return 1;
		if(a[field] > b[field]) return -1;
		return 0;
	});

	//fdataを配列の先頭に追加
	DataArray.unshift(fdata);

	ShowData();
}


function jkn_check(rec){
	if(rec == 0) return 0;

	var field   = "";
	var key     = "";
	var content = "";
	for(var i = 0 ; i < DataArray[rec].length ; i++){
		content = "";	//初期化

		field   = DataArray[0][i];
		key     = TempSearch[i];
		//key     = $("#Mydata_" + i ).val();
		content = DataArray[rec][i];

		if(key == ""){
			continue;				
		}else{
			if(content.indexOf(key)== -1){
				return -1;
			}
		}
	}
	return 1;
}

function Myup(no){
	var buffArray   = copyArray(DataArray[no]);
	DataArray[no]   = copyArray(DataArray[no-1]);
	DataArray[no-1] = copyArray(buffArray);	 

	//noの再構築
	var dcount = DataArray.length;
	for(var i = 1 ; i < dcount ; i++){
		DataArray[i][0] = i;	//0 field が no 必須
	}

	ShowData();
}
function Mydw(no){
	var buffArray   = copyArray(DataArray[no]);
	DataArray[no]   = copyArray(DataArray[no+1]);
	DataArray[no+1] = copyArray(buffArray);	 

	//noの再構築
	var dcount = DataArray.length;
	for(var i = 1 ; i < dcount ; i++){
		DataArray[i][0] = i;	//0 field が no 必須
	}

	ShowData();
}
function Myedit(no){
	DataTemplate('edit',no);
}

function Mycopy(no){
	//0 : データ追加 1:コピー
	DataTemplate('copy',no);
}
function Mydele(no){
	DataTemplate('dele',no);
}
function Myappend(){
	//0 : データ追加　0:新規
	DataTemplate('append',0);
}
function MyCansel(){
	$('#cont_area').html("");
	$('#cont_area').css('display' , 'none');
}

//処理実行
function MyeditExec(no){
	var field = DataArray[0].length;
	for(var i = 0 ; i < field ; i++){
		if(i == 0){
		}else{
 			DataArray[no][i] = eraseBanString($('#Mydata_' + i).val());
		}
	}
	MyCansel();
	ShowData();	
}
function MyappendExec(no){
	var field = DataArray[0].length;
	var buffArray = new Array();
	var dataNo = DataArray.length; 

	for(var i = 0 ; i < field ; i++){
		if(i == 0){
			buffArray[0] = dataNo;
		}else{
 			buffArray[i] = eraseBanString($('#Mydata_' + i).val());
		}
	}

	//データ行追加
	DataArray.push(buffArray);

	MyCansel();
	ShowData();	
}
function MydeleExec(no){
	//配列を削除
	DataArray.splice( no , 1 );

	//noの再構築
	var dcount = DataArray.length;
	for(var i = 1 ; i < dcount ; i++){
		DataArray[i][0] = i;	//0 field が no 必須
	}

	MyCansel();
	ShowData();	
}

function Mysave(){
 	//DataDir , DataHead , DataExt , DataArray
	//呼び出し側で 外部名宣言のこと	
	var data = ArrayToCsv(DataArray);
	
	var savename = saveFile( DataDir , DataHead , DataExt , data);

	if(savename == false){
		alert("ファイルの保存に失敗しました");
	}else{
		alert(savename + " を保存しました");
	}
}

//検索条件の初期化
function SearchClear(){
	var field = DataArray[0];
	for(var i = 0 ; i < field.length ; i++){
		TempSearch[i] = "";
		$("#Mydata_" + i).val("");
	}	
}

//検索表示の実行
function SearchExec(){
	var field = DataArray[0];
	for(var i = 0 ; i < field.length ; i++){
		TempSearch[i] = $("#Mydata_" + i).val();
	}

	$('#cont_area').css('display' , 'none');
	ShowData();
}

//検索用の配列
var TempSearch = new Array();
function SearchData(sw){
	//sw : 0 初回起動　　1以上　条件起動

	var field = DataArray[0];

	var buff = "<form>";
	buff += "<table border=1>";

	for(var i = 0 ; i < field.length ; i++){
		buff += "<tr>";
		buff += "<th>" + field[i]  + "</th>";

		if(typeof(TempSearch[i]) == "undefined"){
			TempSearch[i] = "";
		}

		//buff += "<td><input class='Mydata' type='text' id='search_" + field[i] + "'  value='" + TempSearch[i] + "' ></td>";
		buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "'  value='" + TempSearch[i] + "' ></td>";
		buff += "</tr>";
	}
	buff += "</table>";
	buff += "</form>";


	buff += "<input type='button' id='MySubmit' onClick='SearchExec()' value = 'この条件で表示する' >　";
	buff += "<input type='button' id='MySubmit' onClick='SearchClear()' value = '条件の初期化' >";

	buff += "　　";
	buff += "<input type='button' onClick='MyCansel()' value = '中止する' >";

	//this file 個別オプション
	buff += setOption();
	//*********************

	$('#cont_area').html(buff);

	//初回起動時は表示させない
	if(sw != 0){
		$('#cont_area').css('display' , 'block');
	}
}


//処理用の配列
var TempData = new Array();
function DataTemplate(mode,no){
	//sw 0:修正 1:追加
	//no 0:データ追加　1以上:データ番号

	var fields = DataArray[0];

	if(mode == "append" || mode == "copy"){
		//新しい追加番号
		var new_no = DataArray.length;	//0行目はフィールド

		if(mode == "append"){
		//append
			TempData  = new Array();
			for(i = 0 ; i < fields.length ; i++){
				if( i == 0){
					TempData[i] = new_no;
				}else{
					TempData[i] = "";
				}
			}
		}else{
		//copy
			TempData = copyArray(DataArray[no]);
			TempData[0] = new_no;
		}
	}else{
		TempData = DataArray[no];
	}

	var buff = "<form>";
	buff += "<table border=1>";

	for(i = 0 ; i < fields.length ; i++){
		buff += "<tr>";
		buff += "<th>" + fields[i]  + "</th>";

		//noは　各データ必須。自動整理
		if(fields[i] == 'no'){
			buff += "<td class='nofield'>" + TempData[i] + "</td>";
		}else{
			buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";
		}		
		buff += "</tr>";
	}
	buff += "</table>";
	buff += "</form>";


	switch(mode){
		case 'append' :
			buff += "<input type='button' id='MySubmit' onClick='MyappendExec()' value = '新規追加する' >";
			break;
		case 'copy' :
			buff += "<input type='button' id='MySubmit' onClick='MyappendExec()' value = '複写追加する' >";
			break;
		case 'edit' : 
			buff += "<input type='button' id='MySubmit' onClick='MyeditExec("+ no + ")' value = '修正する' >";
			break;
		case 'dele' :
			buff += "<input type='button' id='MySubmit' onClick='MydeleExec(" + no + ")' value = '削除する' >";
			break;
	}

	buff += "　　";
	buff += "<input type='button' onClick='MyCansel()' value = '中止する' >";

	//this file 個別オプション
	buff += setOption();
	//*********************

	$('#cont_area').html(buff);
	$('#cont_area').css('display' , 'block');

	//return buff;
}

//配列複写の関数
function copyArray(arry){
	var tempArray = [];
	for(var i=0; i<arry.length ; i++){
		if(arry[i] instanceof Array){
			tempArray[i] = copyArray(arry[i]);
		}else{
			tempArray[i] = arry[i];
		}
	}
	return tempArray;
}

//配列添字からフィールド名を返す
function DataFieldName(fieldno){
	var fields = DataArray[0].length;

	if(fieldno > fields || fieldno < 0){
		return false;
	}else{
		var retno = DataArray[0][fieldno];
		return retno;
	}
}

//フィールド名から配列添字を返す
function DataFieldNo(fieldname){
	var fields = DataArray[0].length;
	for(var i = 0 ; i < fields ; i++){
		if(DataArray[0][i] == fieldname){
			return i;
			break;
		}
	}
	return false;
}


//配列をcsvデータに変換
function ArrayToCsv(from){
	var cr   = String.fromCharCode(0x0d,0x0a);
	var tab  = String.fromCharCode(0x09);

	var buff = "";
	var datac  = from.length; 
	for(var i = 0 ; i < datac ; i++){
		var fieldc = from[i].length;
		for(var s = 0 ; s < fieldc ; s++){
			//不要な文字を消去
 			buff += eraseBanString(from[i][s]);
			if(s < fieldc - 1){
				buff += ",";
			}
		}
		buff += cr;
	}
	return buff;
}

//不要な文字の消去
function eraseBanString(from){
	var buff = String(from);

	//'", を消去する
	buff = buff.replace(/,/g,"");
	buff = buff.replace(/\'|\"/g,"");
	
	return buff;
}

//テキストファイルの保存
//outdir : 保存ディレクトリ　"files/apprise";	//末尾の　/　は除く
//fhead  : ファイル名　　　　inputName;
//fext   : ファイル拡張子　　"xml"
//indata : テキストデータ  　buff
function saveFile( outdir , fhead , fext , indata){

	//**************************
	//xml データをサーバーへ送信する
	send_data = { dir : outdir , header : fhead , ext : fext , data : indata };

	//alert(send_data);

	var sfilename = "";

        // 送信処理
	$.ajax({
		url: "savefile.php", // 送信先のPHP
		type: "POST", // POSTで送る
		async : false,	//同期通信

		data:send_data ,

		success : function(data, status, xhr) {
 			// 通信成功時の処理
			console.log("success");
			console.log("data ="+data);
			console.log("status ="+status);
			console.log("xhr ="+xhr);

			//保存したファイル名を返す
			sfilename = data;

			//通信OK後、データベース処理でエラーとなった場合の確認処理が必要。
		} ,

		error : function(xhr, status, error) {
			// 通信失敗時の処理
        		console.log("error");
			console.log("status ="+status);
			console.log("xhr ="+xhr);
			alert("データが転送がエラーとなりました");

			sfilename = false;
		}
	});

	return sfilename;
}
