//csvDataBase controler

//conrol width（pxは付けない）
var Field_up  = 20;
var Field_dw  = 20;
var Field_ecd = 50;

/* 呼び出し側のｐｈｐファイルで定義
//フィールド構造ファイルの読み出し、または手動で設定
//フィールド名
var Fno = new Array();
Fno[0] = 'no';
Fno[1] = 'memo';

//フィールドのラベル
var Flabel = new Array();
Flabel['no'] = 'No';
Flabel['eventtitle'] = 'イベントタイトル';

//フィールドの表示幅
var Field_etc = new Array();
Field_etc['no']           = 50;
Field_etc['target_label'] = 200;
Field_etc['target_id']    = 120;
Field_etc['color']        = 80;
Field_etc['text_color']   = 80;
Field_etc['icon']         = 150;
Field_etc['memo']         = 400;

//フィールドの入力タイプ
var Ftype = new Array();
Ftype['what']    = "text";
Ftype['contact'] = "text";
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
		//buff += "<input type='button' value='データをファイルに保存する' onClick='Mysave()' >　　";
		buff += "<div class='btns'><input type='button' value='項目を新規追加' onClick='Myappend()' ></div>";

		//buff += "<input type='button' value='表示条件を設定' onClick='SearchData(1)' >";
	}


	//table tr/td width の計算
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

		if(!ReadOnly){//編集の時だけの項目(閲覧時は非出力)
			if(i == 0){//最初の行
				
				//修正、複写、削除の列追加
				buff += "<th class='col_action' width='" + Field_ecd + "'></th>";
				//Up Down の列追加
				//buff += "<th width='" + Field_up + "'>Up</th>";
				//buff += "<th width='" + Field_dw + "'>Dw</th>";
				buff += "<th class='col_move' width='" + Field_up + "'>順番変更</th>";
			
			}else{//2行目以降
				
				//修正、複写、削除の列追加
				//buff += "<td width='" + Field_ecd + "' nowrap >";
				buff += "<td class='col_action' align='center'>";
				buff += "<div>";
				buff += "<input class='btn_ecd' type='button' value='編集' onClick='Myedit(" + i + ")' >";
				buff += "<input class='btn_ecd btn2' type='button' value='複製' onClick='Mycopy(" + i + ")' >";
				buff += "<input class='btn_ecd btn_negative' type='button' value='削除' onClick='Mydele(" + i + ")' >";
				buff += "</div>";
				buff += "</td>";
				
				//Up Down の列追加
				/*
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
				*/
				buff += "<td class='col_move' align='center' width='" + Field_up + "'>";
				if( i !== 1 && jkn_disp == 0){//全件表示の場合のみ有効
					buff += "<input class='btn_arrow' type='button' value='↑' onClick='Myup(" + i + ")' >";
				}
				if( i !== dlength - 1 && jkn_disp == 0){//全件表示の場合のみ有効
					buff += "<input class='btn_arrow' type='button' value='↓' onClick='Mydw(" + i + ")' >";
				}
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
				buff += '<th class="col_'+DataArray[0][s]+'" width="' + field_width + '" >';
				//buff += '<th width="' + Field_etc[DataArray[0][s]] + '" >';
				//buff += '<th>';
			}else{
				buff += '<td class="col_'+DataArray[0][s]+'" width="' + field_width + '" >';
				//buff += '<td width="' + Field_etc[DataArray[0][s]] + '" >';
				//buff += "<td>";
			}


			//****************************
			//i=0 の時、フィールド名の代替え設定
			if(i == 0 && typeof Flabel !== "undefined"){
				if(typeof(Flabel[DataArray[0][s]]) =="undefined"){
					buff += DataArray[0][s];
				}else{
					buff += Flabel[DataArray[0][s]];
				}
			}else{
				buff += DataArray[i][s];
			}
			//*****************************

			//************
			//sort
			if( i == 0 ){
				buff += "<div class='sort'>";
				//buff += "<input type='text'  value='' size='6' />";
				//buff += "<input type='text'  id='search_" + DataArray[0][s] + "' value='' style='width:" + (field_width - 10) + "px;' />";
				//buff += "<br />";
				buff += "<input class='btn_arrow' type='button' onClick='SortAsc(" + s + ")' value='▲' id='sortasc_" + s + "' />";
				buff += "<input class='btn_arrow' type='button' onClick='SortDsc(" + s + ")' value='▼' id='sortdsc_" + s + "' />";
				buff += "</div>";
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
		//buff += "<input type='button' value='データをファイルに保存する' onClick='Mysave()' >　　";
		//buff += "<input type='button' value='新規追加' onClick='Myappend()' >　";

		//buff += "<input type='button' value='表示条件を設定' onClick='SearchData(1)' >";
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

function Mycopy(no,is_next){
	//0 : データ追加 1:コピー
	DataTemplate('copy',no,is_next);
}
function Mydele(no,is_next){
	DataTemplate('dele',no,is_next);
}
function Myappend(){
	//0 : データ追加　0:新規
	DataTemplate('append',0);
}
function MyCansel(){
	$('#cont_area').html("");
	$('#cont_area').css('display' , 'none');

	//**** 入力補助エリアがあるならば非表示にする
	if(typeof inputSupport != "undefined"){
		$('#inputSupport').css('visibility' , 'hidden');
	}
	if(typeof map_area != "undefined"){
		$('#map_area').css('visibility' , 'hidden');
	}
	//**********************
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

	Mysave("項目を変更して<br>");	//強制保存

	//MyCansel();
	ShowData();	
}
function MyappendExec(is_next){
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

	Mysave("新規項目を追加して<br>");	//強制保存

	ShowData();	
	if(is_next){
		Myedit(DataArray.length-1);
	}else{
		MyCansel();
	}
}
function MydeleExec(no,is_next){
	//配列を削除
	DataArray.splice( no , 1 );

	//noの再構築
	var dcount = DataArray.length;
	for(var i = 1 ; i < dcount ; i++){
		DataArray[i][0] = i;	//0 field が no 必須
	}

	Mysave("項目を削除して<br>");	//強制保存

	ShowData();
	if(!is_next || DataArray.length<=0){
		MyCansel();
	}else{
		if(no < DataArray.length){
			Myedit(no);
			
		}else{
			Myedit(DataArray.length-1);
		}
	}
}

function Mysave(str){
	if(!str){ str = "";}
 	//DataDir , DataHead , DataExt , DataArray
	//呼び出し側で 外部名宣言のこと	
	var data = ArrayToCsv(DataArray);
	
	var savename = saveFile( DataDir , DataHead , DataExt , data);

	if(savename == false){
		alert("ファイルの保存に失敗しました");
	}else{
		//alert(savename + " を保存しました");
		add_dialog(str + savename + " を保存しました");
	}
}

function MyMove(mode , now , flg){
	var dcount = DataArray.length - 1;
	var now_no = now;

	if(flg == -1){
		if(now_no == 1){
			add_dialog(now + "　先頭の項目です");
			return;
		}else{
			now_no --;
			//alert(now_no + " に移動します");
		}
	}
	if(flg == 1){
		if(now_no == dcount){
			add_dialog(now + "　最後の項目です");
			return;
		}else{
			now_no ++;
			//alert(now_no + "　に移動します");
		}
	}

	DataTemplate(mode,now_no);

	if(typeof map_area != "undefined"){
		//map_visible();		
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

	var buff = "";
	buff += "<form>";
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
function DataTemplate(mode,no,is_next){
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

	var buff = "";
	buff += "<div id='cont_area_inner'>";
	buff += "<div id='cont_content'>";
	if(mode==="append"){
		buff += "<h2>項目の追加</h2>";
	}else if(mode==="copy"){
		buff += "<h2>複製を追加</h2>";
	}else if(mode==="dele"){
		buff += "<h2>項目を削除</h2>";
	}else{
		buff += "<h2>項目の編集</h2>";
	}
	buff += "<form>";
	buff += "<table border=1>";

	for(i = 0 ; i < fields.length ; i++){
		buff += "<tr class='row_"+ fields[i] +"'>";
		//buff += "<th>" + fields[i]  + "</th>";

		//フィールドラベルの確認表示 *****
		if(typeof Flabel !== "undefined"){

				if(typeof(Flabel[fields[i]]) =="undefined"){
					buff += "<th>" + fields[i] + "</th>";
				}else{
					buff += "<th>" + Flabel[fields[i]] + "</th>";
				}
		}else{
			if(fields[i]!=="lat" && fields[i]!=="lng"){
				buff += "<th>" + fields[i] + "</th>";
			}
		}
		//***************************

		//noは　各データ必須。自動整理
		if(fields[i] == 'no'){
			buff += "<td class='nofield'>" + TempData[i] + "</td>";
		}else{
			//buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";

			//*******フィールド入力タイプの確認
			//buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";

			if(typeof Ftype !== "undefined"){
				if(fields[i]==="when"){
						buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' readonly></td>";
				}else if(fields[i]==="openTime" || fields[i]==="closeTime"){
						buff += "<td><input class='Mydata' type='time' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";
				}else if(fields[i]==="where"){
						buff += "<td><select class='Mydata' type='text' id='Mydata_" + i + "' data-value='" + TempData[i] + "' ><option value='other'>登録施設以外</option>"+where_option+"</select><input type='text' data-type='other' placeholder='未登録施設はこちらに入力'></td>";
				}else if(fields[i]==="tag1"){
						buff += "<td><select class='Mydata' type='text' id='Mydata_" + i + "' data-value='" + TempData[i] + "' ><option value=''>市町村未選択</option>"+tags_option+"</select></td>";
				}else if(fields[i]==="uid"){
						buff += "<td><input class='Mydata' disabled='disabled' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";
				}else{
					switch(Ftype[fields[i]]){
						case "text" :
							buff += "<td><textarea class='Mydata' id='Mydata_" + i + "' rows='3'>" + TempData[i] + "</textarea></td>";
							break;
						default : 
							buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";
					}
				}
			}else{
				if(fields[i]==="address"){
					console.log("address");
						buff += "<td><div><label>住所<input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></label>";
						buff += "<label id='adress_lat'>緯度<input class='Mydata' type='text' id='Mydata_" + (i+1) + "' value='" + TempData[(i+1)] + "' ></label>";
						buff += "<label id='adress_lng'>経度<input class='Mydata' type='text' id='Mydata_" + (i+2) + "' value='" + TempData[(i+2)] + "' ></label></div><div class='btns'><a class='btn btn2 btn_showmap'>地図で設定する</a></div></td>";
				}else if(fields[i]==="lat"){
						buff += "<td id='lat_val' style='display:none;'><span class='id'>Mydata_"+i+"</span><span class='value'>"+TempData[i]+"<span></td>";
				}else if(fields[i]==="lng"){
						buff += "<td id='lng_val' style='display:none;'><span class='id'>Mydata_"+i+"</span><span class='value'>"+TempData[i]+"<span></td>";
				}else if(fields[i]==="user_pw"){
					console.log("user_pw");
						buff += "<td>";
						buff += "<p style='font-size:12px;'>任意のパスワードを入力して控えをとった後、暗号化して保存してください</p></div>";
						buff += "<input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' >";
					
						var option = "<div class='optbox' style='text-align:right;'>";
						option += "<input type='button' style='width:200px; background:#815640;' value='パスワードを新規に自動生成' onclick='makePass()' />";
						option += "<input type='button' onclick='setCode()' style='width:100px' value='暗号化' />";
						option += "<div style='display:none;'><input type='text'   name='keta' id='keta' size='2' value='8' />桁";
						option += "<input type='hidden' name='kazu' id='kazu' size='1' value='1' />";
						option += "<input type='checkbox' name='suuji' id='suuji' checked />数字";
						option += "<input type='checkbox' name='small' id='small' checked />英語小文字";
						option += "<input type='checkbox' name='big'   id='big' />英語大文字";
						option += "</div></div>";

						buff += option;
						buff += "</td>";
					
				}else{
					buff += "<td><input class='Mydata' type='text' id='Mydata_" + i + "' value='" + TempData[i] + "' ></td>";
				}
			}
			//****************************

		}		
		buff += "</tr>";
	}
	buff += "</table>";
	buff += "</form>";
	buff += "</div>";


	buff += "<div class='btns'>";
	buff += "<div>";
	if(mode!=="copy" && mode!=="dele" && mode!=="append"){
		buff += "<input class='btn3' type='button' value='削除' onClick='Mydele(" + no + ",true)' >";
	}
	buff += "<input class='btn_negative' type='button' onClick='MyCansel()' value = '閉じる' >";
	switch(mode){
		case 'append' :
			buff += "<input type='button' id='MySubmit' onClick='MyappendExec()' value = '保存' >";
			break;
		case 'copy' :
			buff += "<input type='button' id='MySubmit' onClick='MyappendExec("+is_next+")' value = '保存' >";
			break;
		case 'edit' : 
			buff += "<input type='button' id='MySubmit' onClick='MyeditExec("+ no + ")' value = '保存' >";
			break;
		case 'dele' :
			buff += "<input type='button' id='MySubmit' onClick='MydeleExec(" + no +","+is_next+ ")' value = '削除する' >";
			break;
			/*
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
			*/
	}
	buff += "</div>";
	
	//***データの移動***
	if(mode == "edit"){
		buff += "<div>";
		buff += "<input class='btn_next' type='button' onClick='MyMove(\"" +mode + "\"," + no + ",-1)' value= '前の項目へ' >";
		buff += "<input class='btn2' type='button' value='複製して編集' onClick='Mycopy(" + no + "," + is_next+ ")' >";
		buff += "<input class='btn_back' type='button' onClick='MyMove(\"" +mode + "\"," + no + ",1)'  value= '次の項目へ' >";
		buff += "</div>";
	}
	//****************
	buff += "</div><!-- /.btns -->";

	//this file 個別オプション
	buff += setOption();
	//*********************
	buff += "</div><!-- /#cont_area_inner -->";

	$('#cont_area').html(buff);
	$('#cont_area').css('display' , 'block');

	
	//入力補助設定
	if($( "#cont_area .row_when input" ).length>0){ $( "#cont_area .row_when input" ).datepicker(); }
	$(".row_tag1 select").val($(".row_tag1 select").attr("data-value"));
	$(".row_where select").val($(".row_where select").attr("data-value"));
	if($( "#cont_area #adress_lat input" ).length>0){
		$( "#cont_area #adress_lat input" ).attr("id",$( "#lat_val .id" ).text());
		$( "#cont_area #adress_lat input" ).val($( "#lat_val .value" ).text());
		$( "#cont_area #adress_lng input" ).attr("id",$( "#lng_val .id" ).text());
		$( "#cont_area #adress_lng input" ).val($( "#lng_val .value" ).text());
	}
	set_cont_area_event();
	
	//**** 入力補助エリアがあるならば表示する
	if(typeof inputSupport != "undefined"){
		//$('#inputSupport').css('visibility' , 'visible');
	}
	if(typeof map_area != "undefined"){
		//$('#map_area').css('visibility' , 'visible');
		
	}
	$(".btn_showmap").click(function(){$('#map_area').css('visibility' , 'visible');});
	//**********************
	//return buff;



	//UIDの初期設定（新規追加、複写追加のみ）
	if(mode == "append" || mode == "copy"){
		if("uidClass" in window){
			//イベントデータの場合
			if(uidClass == "evt"){
				//alert("イベントデータの編集処理です");
				getUID();
			}else{
				alert("イベントクラスの指定がありません");
			}
		}
	}
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

//不要な文字の消去（エスケープ文字の置換）
function eraseBanString(from){
	var buff = String(from);

	
	//'", を消去する
	buff = buff.replace(/,/g,"");
	buff = buff.replace(/\'|\"/g,"");
	
	//改行を<br />に変換する
	buff = buff.replace(/\r\n/g, "<br />").replace(/(\n|\r)/g, "<br />");

	/*
 	//buff = buff.replace(/&/g, '&amp;')
	//buff = buff.replace(/</g, '&lt;')
	//buff = buff.replace(/>/g, '&gt;')
	buff = buff.replace(/"/g, '&quot;')	
	buff = buff.replace(/'/g, '&#39;');

	buff = buff.replace(/\r\n/g, "<br />").replace(/(\n|\r)/g, "<br />");

	//buff = buff.replace(/<br \/>/g, "\n");
	*/

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


(function($, window) {
	"use strict";

	var RANGE = 5,
		events = ["click", "touchstart", "touchmove", "touchend"],
		handlers = {
			click: function(e) {
				if(e.target === e.currentTarget)
					e.preventDefault();
			},
			touchstart: function(e) {
				this.jQueryTap.touched = true;
				this.jQueryTap.startX = e.touches[0].pageX;
				this.jQueryTap.startY = e.touches[0].pageY;
			},
			touchmove: function(e) {
				if(!this.jQueryTap.touched) {
					return;
				}

				if(Math.abs(e.touches[0].pageX - this.jQueryTap.startX) > RANGE ||
				   Math.abs(e.touches[0].pageY - this.jQueryTap.startY) > RANGE) {
					this.jQueryTap.touched = false;
				}
			},
			touchend: function(e) {
				if(!this.jQueryTap.touched) {
					return;
				}

				this.jQueryTap.touched = false;
				$.event.dispatch.call(this, $.Event("tap", {
					originalEvent: e,
					target: e.target,
					pageX: e.changedTouches[0].pageX,
					pageY: e.changedTouches[0].pageY
				}));
			}
		};

	$.event.special.tap = "ontouchend" in window? {
		setup: function() {
			var thisObj = this;
			
			if(!this.jQueryTap) {
				Object.defineProperty(this, "jQueryTap", {value: {}});
			}
			$.each(events, function(i, ev) {
				thisObj.addEventListener(ev, handlers[ev], false);
			});
		},
		teardown: function() {
			var thisObj = this;
			
			$.each(events, function(i, ev) {
				thisObj.removeEventListener(ev, handlers[ev], false);
			});
		}
	}: {
		bindType: "click",
		delegateType: "click"
	};

	$.fn.tap = function(data, fn) {
		return arguments.length > 0? this.on("tap", null, data, fn): this.trigger("tap");
	};
})(jQuery, this);
$(function() {
	var isTD = (document.ontouchstart !== undefined)? true : false;
	//var tap = (isTD) ? "touchstart" : "click"; 
	//var tap = "click";
	//-----------------------------------------● 開閉
	$(document).on("tap","#DbTable tr .col_eventtitle, #DbTable tr .col_name, #DbTable tr .col_when, #DbTable tr .col_address", function(){
		var tar = $(this).closest("tr");
		if(tar.hasClass("on")){
			tar.removeClass("on");
		}else{
			tar.addClass("on");
		}
	});
	
});


