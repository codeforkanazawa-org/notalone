//csvDataBase controler

//ReadOnly mode のチェック
if(typeof(ReadOnly) == "undefined"){
	var ReadOnly = false;
}


//データの一覧表示
function ShowData(){
	var buff = "<table border=1>";
	var dlength = DataArray.length;
	for(i = 0 ; i < dlength ; i++){
		buff += "<tr>";

		if(!ReadOnly){
			if(i == 0){
				//Up Down の列追加
				buff += "<th>Up</th><th>Dw</th>";
			
				//修正、複写、削除の列追加
				buff += "<th>管理</th>";
			}else{
				//Up Down の列追加
				if( i == 1){
					buff += "<td></td>";
				}else{
					buff += "<td><input type='button' value='↑' onClick='Myup(" + i + ")' ></td>";
				}
				if( i == dlength - 1){
					buff += "<td></td>";
				}else{
					buff += "<td><input type='button' value='↓' onClick='Mydw(" + i + ")' ></td>";
				}

				//修正、複写、削除の列追加
				buff += "<th>";
				buff += "<input type='button' value='Edit' onClick='Myedit(" + i + ")' >";
				buff += "<input type='button' value='Copy' onClick='Mycopy(" + i + ")' >";
				buff += "<input type='button' value='Dele' onClick='Mydele(" + i + ")' >";
				buff += "</th>";

			}
		}
		
		for(s = 0 ; s < DataArray[i].length ; s++){

			if( i == 0){
				buff += "<th>";
			}else{
				buff += "<td>";
			}

			buff += DataArray[i][s];

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
		buff += "<input type='button' value='新規追加' onClick='Myappend()' ><br /><br />";
		buff += "<input type='button' value='データをファイルに保存する' onClick='Mysave()' ><br />";
	}

	$('#list').html(buff);
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
