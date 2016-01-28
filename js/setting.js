//notalone.jp　の設定ファイル読み出しに関する部分のみ
//phpスクリプトで必要な部分のみ
//（注）notalone.jp と併用しないこと


//**************
var SettingTable = "../localhost/setting.csv";

var DEFAULT_LAT;
var DEFAULT_LNG;

var DRAGGABLE;
//**************

//setting データの読み出し
readSettingData(SettingTable , function(data){
	DEFAULT_LAT = parseFloat(getSetting("DEFAULT_LAT"));
	DEFAULT_LNG = parseFloat(getSetting("DEFAULT_LNG"));

	if(parseInt(getSetting("DRAGGABLE")) == 1){
		DRAGGABLE = true;
	}else{
		DRAGGABLE = false;
	}
});
//************


//*********** common ****************

//Setting情報読み込み用　２次元配列
var settingArray;


//CSVファイルの読み込み
function csvToArray(filename, cb) {
	//キャッシュしない
	$.ajaxSetup({
		cache: false
	});

	$.get(filename, function(csvdata) {
		//CSVのパース作業
		//CRの解析ミスがあった箇所を修正しました。
		//以前のコードだとCRが残ったままになります。
		// var csvdata = csvdata.replace("\r/gm", ""),
		csvdata = csvdata.replace(/\r/gm, "");

		var line = csvdata.split("\n"),
		ret = [];
		for (var i in line) {
        		//空行はスルーする。
        		if (line[i].length == 0) continue;

        		var row = line[i].split(",");
        		ret.push(row);
      		}
      		cb(ret);
	});
}


//setting ファイルを読み込み settingArrayに保存
function readSettingData(table , callback){
	csvToArray( table , function(data) {

		settingArray = new Array();

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			settingArray.push(rensou);
		}
		callback(settingArray);
	});
}

//settingArrayから、フィールド名のデータを読み出す
function getSetting(fieldname){
	var data = settingArray;
 
	for(var i = 0 ; i < data.length ; i++){
		if(data[i]['define'] == fieldname){
			return data[i]['data'];
			break;
		}
	}
	return false;
}

