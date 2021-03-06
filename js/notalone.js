//notalone.js

//**********************
//window risize 時の処理
$(window).resize(function(){
	brows_init();
});
//**********************


//GETデータの取得
function getUrlVars(){
    	var vars = {}; 
    	var param = location.search.substring(1).split('&');
    	for(var i = 0; i < param.length; i++) {
        	var keySearch = param[i].search(/=/);
        	var key = '';
        	if(keySearch != -1) key = param[i].slice(0, keySearch);
        	var val = param[i].slice(param[i].indexOf('=', 0) + 1);
        	if(key != '') vars[key] = decodeURI(val);
    	} 
    	return vars; 
}
var getArray = new Array;

//不要なGETデータを消去し、その他を次に引き継ぐデータを再構成
function eraseGetString(erasekey){
	var urlstring = "";
	var i = 0;
	for(key in getArray){
  		if(key != erasekey){
			if( i == 0 ){
				urlstring += "?";
			}else{
				urlstring += "&";
			}
			urlstring += key + "=" + getArray[key];
			i++;
		}
	}
	return urlstring;
}



//実行ページ名の格納変数
var thispage ="";

//デフォルトのメッセージ
var Def_info ="	誕生日の設定で年齢が表示できます。<br />お気に入りの画像も登録できます。<br />設定する時は画像をクリック！！<br />";

//Setting情報読み込み用　２次元配列
var settingArray;

//初期位置情報
var DEFAULT_LAT;
var DEFAULT_LNG;

//初期化
function init(){

	//setting データの読み出し
	var table = "localhost/setting.csv";
	readSettingData(table , function(data){
		DEFAULT_LAT = parseFloat(getSetting("DEFAULT_LAT"));
		DEFAULT_LNG = parseFloat(getSetting("DEFAULT_LNG"));

		if(parseInt(getSetting("DRAGGABLE")) == 1){
			DRAGGABLE = true;
		}else{
			DRAGGABLE = false;
		}

		init_after();
	});


	//getデータ有無の判断
	getArray = getUrlVars();

	//連想配列の要素数は Object.keys で検出できる
	if(Object.keys(getArray).length == 0){
		//alert("getデータはありません");
	}else{
		for(key in getArray){
  			//alert(key + "：" + getArray[key]) ;
		}

		//class引数があった場合、当該メニューにジャンプする
		//class以外の引数は、次のメニューに引き継ぐ
		switch(getArray['class']){
			case 'evt' :
				var nextString = eraseGetString('class');
				location.href = "events.html" + nextString;
				break; 
			case 'map' :
				var nextString = eraseGetString('class');
				location.href = "map/map.html" + nextString;
				break;
			case 'inq' :
				var nextString = eraseGetString('class');
				location.href = "inquiry.html" + nextString;
				break;
		}
	}
}



function init_after(){
	//実行ページのチェック
	thispage = $("#thispage").html();

	switch(thispage){
		case "index.html"  :
			brows_init();
			index_init();

			break;

		case "events.html" :

			//target tableの読み込み
			var table = "localhost/target.csv";
			readTarget(table);

			//area tableの読み込み
			var table = "localhost/area.csv";
			readArea(table);

			//location tableの読み込み
			var table = "localhost/location.csv";
			readLocation(table);

			//FullCalendarの初期化を先に実行
			events_init();
			brows_init();

			//***************
			//イベント表示　基準月の確認（初期は今日）
			//初期値は今月
			DefYear  = TodayYear;
			DefMonth = ("0" + TodayMonth).substr(-2);
			DefDay   = TodayDay;

			//年月の指定がある場合 
			if(getArray['mon'] && getArray['mon'].length == 6){
				var getAry   = getArray['mon'];
				var getYear  = getAry.substr(0,4);
				var getMonth = getAry.substr(4,2);

				//月のチェック
				if( getMonth <= 0 || getMonth >=13){
					//alert("月指定エラー");
				}else{
					//mon　で指定の月
					DefYear  = getYear;
					DefMonth = getMonth;
					DefDay   = "1";
				}
			}
			
			//基準月のカレンダーを表示
			//$('#calendar').fullCalendar('gotoDate',"2016-11");
			$('#calendar').fullCalendar('gotoDate',DefYear + "-" + DefMonth);

			//イベントデータの設定
			readEvents(0 , function(){
				eventSetCalendar(function(){

					//UIDによる指定イベントのオープン
					if(getArray['uid']){
						var suid = getArray['uid'];
						var euid = SearchEvent( 'uid' , suid , 'eno');
						if(euid == -1){
							alert(suid + "：指定のUIDが見つかりません");
						}else{
							//selectEvent(euid);
							uidCallEvent(euid);
						}
					}else{
	
						//当日直近日にスクロール
						TodayEvent();
					}
				});
			});



			//alert(eventArray.length);
			//eventArray;
			//selectEvent(idno);

			//****************




			//googlemap の初期設定
			mapInit();

			break;

		case "inquiry.html" :

			//inquiry tableの読み込み
			var table = "inquiry/inquiry.csv";
			readInquiry(table);

			brows_init();

			//googlemap の初期設定
			mapInit();

			break;

		case "about.html"  :
			brows_init();
			//index_init();

			break;
	}	 
}


//ブラウザの初期化
var DeviceWidth;
var DeviceHeight;
var CalendarToolHeight;

var TopHeight = 30;	//トップメニューの縦幅

function brows_init(){

	//共通部の設定****************
	//for menu
	DeviceWidth     = window.innerWidth;	//ブラウザの横幅、スマホの横幅
	DeviceHeight   = window.innerHeight;	//ブラウザの縦幅、スマホの縦幅
	//var TopHeight = 30;			//トップメニューの縦幅
	var PcWidth = 480;			//パソコンの場合の横幅
	//*****************************

	if(DeviceWidth >= 480){
		var BodyWidth = PcWidth + "px";
		var BodyWidthMath  = PcWidth;
		var BodyHeightMath = DeviceHeight;
 
		var BodyWhite = parseInt(DeviceWidth,10) - parseInt(BodyWidth,10);
		var BodyLeftMargin = BodyWhite > 0 ? (BodyWhite / 2) + "px" : "0px";
	}else{
		var BodyWidth      = DeviceWidth + "px";
		var BodyWidthMath  = DeviceWidth;
		var BodyHeightMath = DeviceHeight;

		var BodyLeftMargin = "0px";
	}
	$("body").width(BodyWidth);
	$("body").css({"margin-left" : BodyLeftMargin });

/*
	$("#top-menu").width(BodyWidth);
	$("#top-menu").css({"left" : BodyLeftMargin });
	$("#top-menu").height(TopHeight);
	$("#top-menu").css({"line-height" : TopHeight + "px"});
*/
	
	//個別部の設定
	//************************
	switch(thispage){
		//****************
		case "index.html" :

	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

	//縦幅 var ritu = 0.58;
	var ImageHeight  = Math.round(DeviceHeight * 0.5);	//イメージ画像
	$("#notalone_image").height(ImageHeight);

	//縦幅 var ritu = 0.58;
	var PrivertHeight  = Math.round(DeviceHeight * 0.5);	//個人情報欄の縦幅確保
	var PhotoPadding = 5;				//個人情報　写真の余白
	$("#privert").height(PrivertHeight);
	$("#privert").css("display" , "none");	//初期非表示

	var MenuHeight = ( DeviceHeight - TopHeight - ImageHeight ) / 3.5;

	$(".jobmenu").height(MenuHeight);
	$(".jobmenu").css({"line-height" : MenuHeight + "px"});


	//イベント欄ダミーの高さ(px)
	$("#dummy").height(TopHeight);	//メニューの高さ分の余裕

	//canvasサイズは縦を基準、横幅は元画像から比率で設定
	$("#myCanvas").css({"height" : (PrivertHeight * 0.7 - PhotoPadding * 2) + "px" , "padding" : PhotoPadding + "px"});

	//個人情報の読み出し　指標を表示
	favInit(0);

	//本体を表示
	$('#index_contener').css({
		"display" : "block"
	});

		break;

		//***************************
		case "events.html" :

	//カレンダーの高さ
	//$('#calendar').fullCalendar('option', 'contentHeight', DeviceHeight * 0.5);
	//$('#calendar').fullCalendar();
	$('#calendar').fullCalendar('option', 'contentHeight', 1000);
			
	// コンテンツの高さ(px)
	var calendar_div = $("#calendar").height();

	$("#menu-back").height(TopHeight);


	//カレンダーのtoolbarの高さ
	CalendarToolHeight = 40;

	if(CalendarMode == 1){
		modeCalendarTop = DeviceHeight * 0.5;
	}else{
		modeCalendarTop = 0;
	}
	/*
	$("#calendar").css({
		"display"  : "block",
		"position" : "fixed",
		"top"      : modeCalendarTop,
		"width"  : BodyWidth,
		"font-size" : "14px",
		"background-color" : "#F5F5F5",
		"z-index"  : 1
	});

	//customMonthButton 初期非表示
	$('.fc-myCustomMonth-button').css({'visibility':'hidden'}); 

	//イベント欄ダミーの高さ(px)
	$("#dummy").height(DeviceHeight);


	//FullCalendar デザインの一部変更 *****
	$('.fc-toolbar').css({
		"height"  : "30px",
		//"line-height" : "15px",
		"padding" : "0px 5px 0px 5px",

		"background-color" : "#F5F5F5",
		"border"   : "1px solid #DCDCDC",
		"margin"   : 0		
	});
	$('.fc-center h2').css({
		"font-size" : "20px",
	});
*/



	var MapAjast = 18; //マップエリア微調整
	var MapWidth  = (BodyWidthMath  - MapAjast);
	var MapHeight = (BodyHeightMath - MapAjast - TopHeight); 

	//map_area の表示位置を動的に調整
	if($("#map_area").css('visibility') =='visible'){
		//表示されている時
		var nowHeight = TopHeight;
	}else{
		//非表示の時
		var nowHeight = DeviceHeight;
	}

	$("#map_area").css({
		//"top"    : TopHeight,
		//"top"    : DeviceHeight,
		"top"    : nowHeight,

		"left"   : BodyLeftMargin,
		"width"  : MapWidth,
		"height" : MapHeight
	});

	$("#map_canvas").css({
		//"width"  : "100%",
		"width"  : MapWidth,
 		"height" : MapHeight - 30
	});

	//set_area の表示位置を動的に調整
	$("#set_area").css({
		"left" : BodyLeftMargin
	});



	//**********************************


		break;

		//***************************
		case "inquiry.html" :

	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

	//５メニューに対応可、実際は１メニューのみ。高さの調整のため８で割っている
	var MenuHeight = ( DeviceHeight - TopHeight ) / 8;


	$(".jobmenu").height(MenuHeight);
	$(".jobmenu").css({"line-height" : MenuHeight + "px"});

	//イベント欄ダミーの高さ(px)
	$("#dummy").height(50);

	var MapAjast = 18; //マップエリア微調整
	var MapWidth  = (BodyWidthMath  - MapAjast);
	var MapHeight = (BodyHeightMath - MapAjast - TopHeight); 

	//map_area の表示位置を動的に調整
	if($("#map_area").css('visibility') =='visible'){
		//表示されている時
		var nowHeight = TopHeight;
	}else{
		//非表示の時
		var nowHeight = DeviceHeight;
	}

	$("#map_area").css({
		//"top"    : TopHeight,
		//"top"    : DeviceHeight,
		"top"    : nowHeight,

		"left"   : BodyLeftMargin,
		"width"  : MapWidth,
		"height" : MapHeight,
	});
	$("#map_canvas").css({
		//"width"  : "50%",
		"width"  : MapWidth,
 		"height" : MapHeight - 30,
	});


	//本体を表示
	$('#inquiry_contener').css({
		"display" : "block"
	});

		break;

		//***************************

		case "about.html" :

	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

	//5メニューの設定
	var MenuHeight = ( DeviceHeight - TopHeight ) / 5;

	$(".about_title").height(MenuHeight);
	$(".about_title").css({"line-height" : MenuHeight + "px"});

	//イベント欄ダミーの高さ(px)
	$("#dummy").height(DeviceHeight - $("#top_menu").height());


	//map_area の表示位置を動的に調整
	$("#map_area").css({
		"left" : BodyLeftMargin
	});

	//本体を表示
	$('#about_contener').css({
		"display" : "block"
	});
		break;

		//***************************

	}


	//***************************
}


//topメニュー
function top_index(){
	location.href = "index.html";
}


//****for index.html ********
//index.html　初期化
function index_init(){
	//ローカルストレージに目的のデータがあるか確認 
	var key = "MyFavPicture";

	if (window.localStorage[key]) { 
		//存在すればそれを使用 
		ImageSet('myCanvas',window.localStorage[key]);
	}else{
		ImageSet('myCanvas','images/notalone_icon.png');
	} 
}

//2nd(next) menu link
function snd_index(link){
	switch(link){
		case "events"  : location.href="events.html";
				break;
 		case "map"   	: location.href="map/map.html";
				break;
		case "inquiry" : location.href="inquiry.html";
				break;
		case "about"   : location.href="about.html";
				break;
	}
}

//settingMenuの切り替え
function setMenu(tab){
	var activ_css = {
		"background-color" : "#FFFFCC" ,
	};
	var stanby_css ={
		"background-color" : "#EEEEEE" ,
	};
	var activ_block={
		"display" : "block",
		"background-color" : "#FFFFCC" ,
	};
	var stanby_block={
		"display" : "none",
		"background-color" : "#EEEEEE" ,
	};

	if(tab == 1){
		$("#settingMenu1").css(activ_css);
		$("#favSetting").css(activ_block);
		$("#settingMenu2").css(stanby_css);
		$("#imageSetting").css(stanby_block);
	}else{
		$("#settingMenu2").css(activ_css);
		$("#imageSetting").css(activ_block);
		$("#settingMenu1").css(stanby_css);
		$("#favSetting").css(stanby_block);
	}
}

//プライベートエリア（画像、成長指標）の表示切り替え
function privert(){
	var flg = $("#privert").css("display");

	if(flg == "none"){
		$("#notalone_image").hide("blind", "", 1000 );
		$("#privert").show("blind", "", 1000 );
	}else{
		$("#privert").hide("blind", "", 1000 );
		$("#notalone_image").show("blind", "", 1000 );
	}
}

//誕生日等の個人情報設定欄の表示切り替え
function setting(){
	var flg = $("#setting").css("display");

	if(flg == "none"){
		$("#setting").show("blind", "", 1000 );
	}else{
		$("#setting").hide("bling", "", 1000 );
	}
}

//カレンダー（detepicker）による誕生日入力
$(function() {
	for(var i=1 ; i<=3 ; i++){
		var idname = "#bday" + i;

		$(idname).datepicker({
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy/MM/dd'
		});

		$(idname).datepicker("option", "showOn", 'button');
		$(idname).datepicker("option", "buttonImageOnly", true);
		$(idname).datepicker("option", "buttonImage", 'images/ico_calendar.png');

		$(idname).datepicker("option", "showButtonPanel", true);

	}
});

//datepicker　日本語化オプション
$(function($){
    $.datepicker.regional['ja'] = {
        closeText: '閉じる',
        prevText: '<前',
        nextText: '次>',
        currentText: '今日',
        monthNames: ['01','02','03','04','05','06',
        '07','08','09','10','11','12'],
        monthNamesShort: ['1月','2月','3月','4月','5月','6月',
        '7月','8月','9月','10月','11月','12月'],
        dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
        dayNamesShort: ['日','月','火','水','木','金','土'],
        dayNamesMin: ['日','月','火','水','木','金','土'],
        weekHeader: '週',
        dateFormat: 'yy/mm/dd',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: '年'};
    $.datepicker.setDefaults($.datepicker.regional['ja']);
});


//******* cookie ********
//favorite max 3個
var MyFav_count = 3;
var MyFavFamily = new Array();
MyFavFamily[1] = {name: "" , bday: "" };
MyFavFamily[2] = {name: "" , bday: "" };
MyFavFamily[3] = {name: "" , bday: "" };
//***********************

function getCookie(key) {
	// Cookieから値を取得する
	var cookieString = document.cookie;
	// 要素ごとに ";" で区切られているので、";" で切り出しを行う
	var cookieKeyArray = cookieString.split(";");

	// 要素分ループを行う
	for (var i=0; i<cookieKeyArray.length; i++) {
		var targetCookie = cookieKeyArray[i];
		// 前後のスペースをカットする
		targetCookie = targetCookie.replace(/^\s+|\s+$/g, "");

		var valueIndex = targetCookie.indexOf("=");
		if (targetCookie.substring(0, valueIndex) == key) {
			// キーが引数と一致した場合、値を返す
			return unescape(targetCookie.slice(valueIndex + 1));
		}
	}
	return "";
}

function getMyFav(){
	for(var i = 1; i<= MyFav_count ; i++){	
		MyFavFamily[i]['name'] = getCookie("name"+i);
		MyFavFamily[i]['bday'] = getCookie("bday"+i);
	}
}

//お気に入り初期化
function favInit(type){
	//type : 0 指標のみ表示
	//type : 1 入力データも表示

	//*** cookie *****
	getMyFav();

	var fav_name;
	var fav_bday;

	var count = 0;
	var setdata = "<table>";

	for(i=1 ; i<=MyFav_count ;i++){
		fav_name = MyFavFamily[i]['name'];
		fav_bday = MyFavFamily[i]['bday'];

		if(fav_name != ""){
			if(type == 1){
				//入力欄に表示
				$("#name" + i).val(fav_name);
				$("#bday" + i).val(fav_bday);		
			}

			//子育て指標の表示
			count ++;
			setdata += "<tr><td>";
			setdata += "・" + fav_name;
			setdata += "</td><td>";
			setdata += "：" + calculateAge(fav_bday);
			setdata += "</td></tr>";
		}else{
			//入力欄クリア
			$("#name" + i).val('');
			$("#bday" + i).val('');		
		}
	}
	setdata += "</table>";

	if(count>0){
		$("#myFavFamily").html(setdata);
	}else{
		//設定がない場合（デフォルトメッセージ)
		$("#myFavFamily").html(Def_info);
	}
}

//誕生日からの経過年月日を算出（出産予定日対応）
function calculateAge(birthday) {
	var  birth = birthday.split('/');

	// 文字列型に明示変換後にparseInt
	var _birth = parseInt("" + birth[0] + birth[1] + birth[2]);

	var  today = new Date();

	// 文字列型に明示変換後にparseInt
	var _today = parseInt("" + today.getFullYear() + affixZero(today.getMonth() + 1) + affixZero(today.getDate()));

	//生まれてからの日数 
	var AlldiffDays = getDiff(birthday , today);


    	//経過月、日数を算出
	//今年の誕生日までの日数
	var  strToday    = today.getFullYear() + "/" + affixZero(today.getMonth() + 1) + "/" +  affixZero(today.getDate());

	if(_birth > _today){
		//出産予定の場合（未来月）		
		var  diffDays = getDiff(strToday , birthday) - 1;

		//平均月数(365/12)
		var diffMonth = diffDays / (365 / 12);
		return "あと" + diffDays + "日"; 		
	}else{
		//誕生日の場合
		//年数確定
		var Age = parseInt((_today - _birth) / 10000);

		//今年の誕生日
		var  thisBirthday = today.getFullYear() + "/" + birth[1] + "/" + birth[2];
		var  diffDays = getDiff(thisBirthday , strToday);


		//誕生日前の場合（うるう年考慮せず）
		if(strToday < thisBirthday){
			diffDays = 365 - Math.abs(diffDays);
		} 

		//平均月数(365/12)
		var diffMonth = parseInt(diffDays / (365 / 12));

		if(Age < 1){
			if(diffMonth <= 3){
				return diffDays + "日目（" + diffMonth + "月）";
				//return diffDays + "日目";
			}else{
				return diffMonth + "ケ月（" + AlldiffDays + "日）";
				//return diffMonth + "ケ月（" + diffDays + "日）";
				//return diffMonth + "ケ月";
			}
		}else{
			return Age + "歳" + diffMonth + "月（" + AlldiffDays + "日）"; 
			//return Age + "歳" + diffMonth + "月（" + diffDays + "日）"; 
			//return Age + "歳" + diffMonth + "ケ月"; 
		}
	}
}

function affixZero(int) {
    if (int < 10) int = "0" + int;
    return "" + int;
}


//日付の差分日数を返却します。
function getDiff(date1Str, date2Str) {
	var date1 = new Date(date1Str);
	var date2 = new Date(date2Str);
 
	// getTimeメソッドで経過ミリ秒を取得し、２つの日付の差を求める
	var msDiff = date2.getTime() - date1.getTime();
 
	// 求めた差分（ミリ秒）を日付へ変換します（経過ミリ秒÷(1000ミリ秒×60秒×60分×24時間)。端数切り捨て）
	var daysDiff = Math.floor(msDiff / (1000 * 60 * 60 *24));
 
	// 差分へ1日分加算して返却します
	return ++daysDiff;
}


function myfavSet(){
	//日付データを作成する
	var date1 = new Date(2030, 1).toUTCString();
	//2030年1月 日付データをセットする

	for(var i=1; i<=MyFav_count; i++){		
		//** cookie set
		document.cookie = "name" + i + "=" + escape($("#name" + i).val()) + ";expires=" + date1;
		document.cookie = "bday" + i + "=" + escape($("#bday" + i).val()) + ";expires=" + date1; 
	}

	favInit(1);
}

//cookie 強制削除
function myfavDel_all(){
	//日付データを作成する
	var date1 = new Date();
	//1970年1月1日00:00:00の日付データをセットする
	date1.setTime(0);
  	//有効期限を過去にして書き込む
	for(var i=1; i<= MyFav_count ; i++){
		//** cookie set
		fno = i;
		document.cookie = "name" + i + "=;expires=" + date1.toGMTString();
		document.cookie = "bday" + i + "=;expires=" + date1.toGMTString(); 
	}

	favInit(1);
}


//******** 
var IMAGES = [];
var imageType;
//********
//ローカル画像ファイルの表示
//function localImageSet(){
$(function(){
  $("#uploadFile").change(function() {
    //canvas　の設定は　jqueryではうまくいかない
    //var canvas = $("#myCanvas");
    //var ctx = canvas[0].getContext("2d");

    var canvas = document.getElementById('myCanvas');
    var ctx = canvas.getContext("2d");

    // 選択されたファイルを取得
    var file = this.files[0];

    // 画像ファイル以外は処理中止
    if (!file.type.match(/^image\/(png|jpeg|gif)$/)){
	return;
    }else{
	imageType = file.type;
    }

    var reader = new FileReader();

    // File APIを使用し、ローカルファイルを読み込む
    reader.onload = function(evt) {

      // 画像の情報（base64）を配列に設定
      IMAGES.push(evt.target.result);


      //配列情報からcanvasに描画
      ImageSet('myCanvas' , IMAGES);

    }

    // ファイルを読み込み、データをBase64でエンコードされたデータURLにして返す
    reader.readAsDataURL(file);

  });
});


//canvasに指定の画像を表示する
function ImageSet(canvasname,imagename){
    var canvas  = document.getElementById(canvasname);
    var context = canvas.getContext('2d');

    var image = new Image();
    image.src = imagename;

    image.addEventListener('load', function() {
	var swidth  = image.width;
	var sheight = image.height;
	var sexp    = swidth / sheight;

        var iheight = canvas.height;
        var iwidth  = parseInt(iheight * sexp);

	//canvasのサイズ変更は　css は NG（縦長） --> attr　で設定
	canvas.width  = iwidth;
	canvas.height = iheight;

        context.drawImage(image, 0 , 0 , iwidth , iheight);
    }, false);
}


//canvasに読み込んだ画像をローカルストレージに保存
function imgStorageSet(){
	var canvas = document.getElementById("myCanvas");
	var ctx = canvas.getContext("2d");

	//alert("画像の保存方法は検討中です");
	//return false;


	//ローカルストレージに保存 
	//toDataURL は、ローカルのイメージをクロスドメインと判断する
	//window.localStorage["MyFavPicture"] = canvas.toDataURL('image/jpeg'); 
	window.localStorage["MyFavPicture"] = canvas.toDataURL(imageType); 

	alert("選択した画像情報を保存しました");
}

//ローカルストレージのキャッシュをクリアする
function imgStorageClear(){
	//キャッシュをクリアする場合 
	window.localStorage.clear();
	alert("登録していた画像情報を消去しました");
}
//****************************




//**** for events.html *******

//本日年月
var Today    = new Date();
//var Today    = new Date(2016,10,11);

var TodayYear  = Today.getFullYear();
var TodayMonth = Today.getMonth() + 1; 	//month = 0から始まる
var TodayDay   = Today.getDate();

/*
//基準年月（初期）
var SetYear  = TodayYear;
var SetMonth = TodayMonth;
var SetDay   = TodayDay; 
*/

var DefYear;
var DefMonth;
var DefDay;

var SetYear;
var SetMonth;
var SetDay;

var event_dir = "events/";	//イベントファイルの保存ディレクトリ
var event_ext = ".csv";		//イベントファイルの拡張子

var eventArray  = new Array();	//イベント用配列（２次元：連想配列）
var targetArray = new Array(); //tag1用配列（２次元：連想配列） 
var areaArray   = new Array(); //地域用配列（２次元：連想配列） 
var locationArray = new Array(); //開催場所用配列（２次元：連想配列） 

var JpWeekday = ['日','月','火','水','木','金','土'];	//日本語曜日


//基準月に応じたファイルを読み込みeventArrayに保存
//function readEvents(target){
function readEvents(target , cb){
	//配列初期化
	eventArray = new Array();

	//イベント一覧、fullcalendarイベント初期化
	$('#events-title').empty();
	$('#calendar').fullCalendar('removeEvents');

	switch(target){
		case  0 : //初期・当月または基準月
			  //SetYear  = Today.getFullYear();
			  //SetMonth = Today.getMonth() + 1; 
			  SetYear  = DefYear;
			  SetMonth = DefMonth; 
			  break;
			  //翌月
		case  1 : SetMonth++;
			  if(SetMonth > 12){
				SetMonth -= 12;
				SetYear++;
			  }
			  break;
			  //前月
		case -1 : SetMonth--;
			  if(SetMonth < 1){
				SetMonth += 12;
				SetYear--;
			  }
			  break;

			 //いずれも該当しない（絞りみの再表示）
		default : 
	}

	var thisMonth = SetYear + ("0" + SetMonth).substr(-2);
	var eventfile = event_dir + thisMonth + event_ext;
	//alert(eventfile);
	csvToArray( eventfile , csv_function);
	function csv_function(data) {
		if(!data){
			csvToArray( event_dir+"blank.csv" , csv_function);
		}else{
			//1行目をフィールド名として扱い連想配列にする
			for(var i = 1 ; i < data.length ; i++){
				var rensou = new Object();
				for(var s = 0; s < data[i].length ; s++){
					rensou[data[0][s]] = data[i][s]; 
				}
				eventArray.push(rensou);
			}


			//eventArray を日付、開始時間で昇順ソートする
			//降順は、-1 , 1 を逆にする
			//ev_when : 年月 ev_open : 開始時間
			eventArray.sort(function(a,b){
				if(a[ev_when] < b[ev_when]) return -1;
				if(a[ev_when] > b[ev_when]) return 1;
				if(a[ev_open] < b[ev_open]) return -1;
				if(a[ev_open] > b[ev_open]) return 1;
				return 0;
			});
			//callback化
			cb();
			//*********
		}
	}
}

//target（tag1）ファイルを読み込みtargetArrayに保存
function readTarget(table){
	csvToArray( table , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			targetArray.push(rensou);
		}
	});
}

//area（地域）ファイルを読み込みareaArrayに保存
function readArea(table){
	csvToArray( table , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			areaArray.push(rensou);
		}
	});
}

//location（開催場所）ファイルを読み込みlocationArrayに保存
function readLocation(table){
	csvToArray( table , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			locationArray.push(rensou);
		}

		//googlemap の初期設定
		//mapInit();
	});
}

//開催場所から、location情報を取得（確認）
function getLocationLatLng(where){
	var loc  = locationArray;

	var nlat = "";
	var nlng = "";

	for(var i = 0; i < loc.length ; i++){
		if(loc[i][loc_name] == where){
			nlat = loc[i][loc_lat];
			nlng = loc[i][loc_lng];
			break;
		}
	}

	if(nlat != "" && nlng != ""){
		return i;
	}else{
		return -1;
	}
}

//開催場所から、area名を取得
function getLocationAreaName(where){
	var loc  = locationArray;

	var area = "";

	for(var i = 0; i < loc.length ; i++){
		if(loc[i][loc_name] == where){
			area = loc[i][loc_area];
			break;
		}
	}

	if(area != "" ){
		return area;
	}else{
		return -1;
	}
}


var mapCanvas;	//googlemap canvas

var now_marker;		//位置表示用マーカー
var now_infowindow;	//インフォウインドウ
function mapInit(){
	//sub/js/main.js　で定義
	showGoogleMap(DEFAULT_LAT,DEFAULT_LNG);
}


function showGoogleMap(initLat, initLng) {
        var latlng = new google.maps.LatLng(initLat, initLng);

	var mapOpt = { 
		mapTypeControl: true,
		mapTypeControlOptions: { mapTypeIds: [google.maps.MapTypeId.ROADMAP,google.maps.MapTypeId.HYBRID] },
		
		streetViewControl:true,
		zoomControl : true,
		zoomControlOptions: {
    			style: google.maps.ZoomControlStyle.LARGE,
    			position: google.maps.ControlPosition.TOP_RIGHT
		},
		zoom: 16,

		center: latlng,

		//２本指でのマップ移動をキャンセル
		gestureHandling: 'greedy',
	};

        mapCanvas = new google.maps.Map(document.getElementById("map_canvas"), mapOpt);


	//マップサイズが変更されたことを確認する（対処措置）
	google.maps.event.trigger(mapCanvas, 'resize'); 

        //現在地のピン
        var now_latlng = new google.maps.LatLng(initLat, initLng);
        now_marker = new google.maps.Marker({
            position:now_latlng,
            title: '位置表示用マーカー',
	    //draggable : true,

            map: mapCanvas,
        });

	mapCanvas.setCenter(latlng);

	//***********
        now_infowindow = new google.maps.InfoWindow();
	var html = "test info";
        now_infowindow.setContent(html);

        google.maps.event.addListener(now_marker, 'click', function() {
            now_infowindow.open(mapCanvas , now_marker);
        });
	//***********

}

function eventMap_visible(locationNo){
	//location位置情報
	var nlat = locationArray[locationNo][loc_lat];
	var nlng = locationArray[locationNo][loc_lng];

	var latlng = new google.maps.LatLng(nlat , nlng);

	//******
	var html = "<div style='width:150px'>";
	//googlemapのルート検索起動
	html += "<input type='button' onClick='googleRoot(" + nlat + "," + nlng + ")' value='ルート検索'><br />";
	html += "<b>" + locationArray[locationNo][loc_name] + "</b><br />";
	html += locationArray[locationNo][loc_memo] + "<br />";
	html += locationArray[locationNo][loc_address];
	html += "</div>";



	$('#map_area').css({
		'display' : 'block',
		'top'        : TopHeight
	});	
	google.maps.event.trigger(mapCanvas, 'resize');
     now_infowindow.setContent(html);
	//******

	now_marker.setPosition(latlng);
	mapCanvas.setCenter(latlng);

}

function eventMap_hidden(){
	//openInfoWindow close
        now_infowindow.close();

	$('#map_area').css({
		'display' : 'none',
		'top'        : DeviceHeight
	});
}

function Area_setting(){

	//ローカルストレージから表示条件を読み出し
	if(window.localStorage["AreaMode"]){
		var area_mode = window.localStorage["AreaMode"];
	}else{
		var area_mode = '0';	//default
	}

	if(window.localStorage["AreaName"]){
		var check_area = window.localStorage["AreaName"].split(",");
	}else{
		var check_area = "";
	}

	if(window.localStorage["LocName"]){
		var check_loc  = window.localStorage["LocName"].split(",");
	}else{
		var check_loc  = "";
	}

	if(window.localStorage["TagId"]){
		var check_tag  = window.localStorage["TagId"].split(",");
	}else{
		var check_tag  = "";
	}
	
	if(window.localStorage["AreaName"] || window.localStorage["LocName"] || window.localStorage["TagId"]){
		area_mode = 1;
	}

	//条件設定エリアの内容作成
	var buff = "<h3>イベントの絞り込み</h3>";
	buff += "<div class='btns'><input class='btn_search' type='button' value='この内容で表示' onClick='checkArea_setting(1)' />";
	buff += "<input class='btn_all' type='button' onClick='checkArea_setting(0)' value='絞り込みを解除' />";
	buff += "<input class='btn_close' type='button' value='×' onClick='setArea_hidden()' /></div>";

	buff += "<div class='set_area_content' >";
	buff += "<form >";

	//buff += "<input type='button' value='絞り込み条件を保存する' onClick='setArea_save()' /><br />";

	//buff += "<input type='checkbox' name='area_mode' ";
	buff += "<input style='display:none;' type='checkbox' name='area_mode' ";
	if(area_mode == '1'){
		buff += " checked ";
	}
	buff += " value='1' />";//：絞り込み条件の有効化<br />

	//areaArrayの一覧を表示
	var areaName  = "";
	var areaColor = "";
	var areaText  = "";
	var areaVisi  = "";

	var locName  = "";
	var locVisi  = "";
	for(var s = 0; s < areaArray.length ; s++){
		areaName  = areaArray[s][area_label];
		areaColor = areaArray[s][area_color];
		areaText  = areaArray[s][area_text];
		areaVisi  = areaArray[s][area_visi];

//		buff += "<div style='background:" + areaColor + ";";
//		buff += "color:" + areaText + ";'>";
		buff += "<div class='search_areas'>";
		buff += "<label class='search_areas_name'><input type='checkbox' class='area'";
		for(var l = 0 ; l < check_area.length ; l++){
			if(check_area[l] == areaName){
				buff += " checked ";
			}
		}

		buff += " name='" + areaName + "' value='" + areaName + "' />：";
		buff += areaName+"</label>";
		buff += "<div class='search_areas-body'>";
		for(var m = 0 ; m < locationArray.length ; m++){
			if(locationArray[m][loc_area] == areaName){
				locName = locationArray[m][loc_name];
				locVisi = locationArray[m][loc_visi];

				//buff += "<br />";
				buff += "<label><input type='checkbox' class='loc'";
				for(var l = 0 ; l < check_loc.length ; l++){
					if(check_loc[l] == locName){
						buff += " checked ";
					}
				}
				buff += " name='" + locName + "' value='" + locName + "' />";
				buff += locName+"</label>";
			}
		}
		buff += "</div>";
		buff += "</div><!-- /.search_areas -->";
	}

	//地域区分なし　targetArray を設定する
	
	buff += "<div style='background:" + default_area_color + ";";
	buff += "color:" + default_area_text + ";'>";
	buff += "<label><input type='checkbox' class='area'";
	for(var l = 0 ; l < check_area.length ; l ++){
		if(check_area[l] == "area_non"){
			buff += " checked ";
		}
	}
	buff += " name='area_non' value='area_non' />：";
	buff += "任意タグ</label>";
	for(var m = 0 ; m < targetArray.length ; m++){
		tarName  = targetArray[m][tar_label];
		tarId    = targetArray[m][tar_id];
		tarColor = targetArray[m][tar_color];
		tarText  = targetArray[m][tar_text];
		tarVisi  = targetArray[m][tar_visi];

		buff += "<div style='background:" + tarColor + ";";
		buff += "color:" + tarText + ";'>";
		buff += "<label><input type='checkbox' class='tag1'";
		for(var l = 0 ; l < check_tag.length ; l++){
			if(check_tag[l] == tarId){
				buff += " checked ";
			}
		}
		buff += " name='" + tarId + "' value='" + tarId + "' />";
		buff += tarName;
		buff += "</label></div>";
	}

	buff += "</div>";

	buff += "</form>";
	buff += "</div>";


	$("#set_area").html(buff);
	set_bar_str();
}


function set_bar_str(bar_text){
	if(!bar_text){
		bar_text = "";
		if($('input[name="area_mode"]').prop('checked')){
			if(window.localStorage["AreaName"]){
				bar_text = window.localStorage["AreaName"].split(",").join("・");
			}else{
				bar_text = "";
			}
			if(window.localStorage["LocName"]){
				if(bar_text!==""){bar_text+="/"}
				bar_text += window.localStorage["LocName"].split(",").join("・");
			}
			if(window.localStorage["TagId"]){
				if(bar_text!==""){bar_text+="/"}
				//bar_text = window.localStorage["TagId"].split(",").join("・");
				TagId = window.localStorage["TagId"].split(",");
				for(var i = 0 ; i < TagId.length ; i++){
					for(var s = 0 ; s < targetArray.length ; s++){
						if(targetArray[s][tar_id] == TagId[i]){
							if(i > 0){
								bar_text += "・";
							}
							bar_text += targetArray[s][tar_label];
						}
					}
				}  
			}
		}else{
		}
	}else{
	}
	if(bar_text===""){
		bar_text = "全てのイベント";
	}
	$("#set_bar .set_str").text(bar_text);
}
function setArea_hidden(){
	$('#set_area').css('display' , 'none');
	$('body').removeClass("search_open");
}

function setArea_visible(){
	//topへスクルール
	//$("html,body").animate({scrollTop:0},{duration: 1000});
	$("body").addClass("search_open");
	$('#set_area').css('display' , 'block').css('visibility' , 'visible');
}

//イベント地域条件で再表示
function setArea_setting(){
	setArea_hidden();
	

	$('#calendar').fullCalendar('changeView','month');

	//再表示
	readEvents(100 , function(){
		eventSetCalendar(function(){
			//callback dummy
			TodayEvent();
		});
	});
}

//イベント表示条件をチェックしイベントを表示する
function checkArea_setting(sw){
	//sw 0 : all  sw : 1 area
	
	if(sw == 1 && ($('[class="area"]:checked').length > 0 || $('[class="loc"]:checked').length > 0 || $('[class="tag1"]:checked').length > 0)){
		$('input[name="area_mode"]').prop('checked',true);		
	}else{
		$('input[name="area_mode"]').prop('checked',false);
		set_bar_str();
	}

	setArea_setting();
	setArea_save();
	set_bar_str();
}

//地域条件の保存
function setArea_save(){
	if($('input[name="area_mode"]').prop('checked') && $('[class="area"]:checked').length > 0){
		var area_mode = 1;
	}else{
		var area_mode = 0;
	}

	var area = $('[class="area"]:checked').map(function(){
  		return $(this).val();
	}).get();

	var area_buff = "";
	for(var i = 0 ; i < area.length ; i++){
		area_buff += area[i];
		if(i < area.length -1 ){
			area_buff += ",";
		} 
	}

	var loc = $('[class="loc"]:checked').map(function(){
  		return $(this).val();
	}).get();

	var loc_buff = "";
	for(var i = 0 ; i < loc.length ; i++){
		loc_buff += loc[i];
		if(i < loc.length -1){
			loc_buff += ",";
		} 
	}


	var tag = $('[class="tag1"]:checked').map(function(){
  		return $(this).val();
	}).get();

	var tag_buff = "";
	for(var i = 0 ; i < tag.length ; i++){
		tag_buff += tag[i];
		if(i < tag.length -1){
			tag_buff += ",";
		} 
	}

	//ローカルストレージに保存
	window.localStorage["AreaMode"] = area_mode;
	window.localStorage["AreaName"] = area_buff;
	window.localStorage["LocName"]  = loc_buff;
	window.localStorage["TagId"]    = tag_buff;

	//alert("表示条件を保存しました");
}


//開催場所と地域設定　またはtag1のチェック
function LocToSetArea(loc,tag){ 
	//絞り込み条件が無効（チェックなし）な場合
	if($('input[name="area_mode"]').prop('checked')){
	}else{
		return 1;
	}

	//開催場所から地域名を取得
	var areaName = getLocationAreaName(loc);
	//地域名が設定されていない（開催場所登録がない）場合
	if(areaName == -1 ){
		//任意(tag1)にチェックがある場合
		if($("[name=area_non]").prop("checked")){
			return 1;
		}else{
			//tag_idのcheck
			if(targetId_check(tag) == 1){
				//任意(tag1)にチェックがある場合
				if($("[name=" + tag + "]").prop("checked")){
					return 1;
				}
			}
		}
	}

	//地域名にチェックがある場合
	if($('input[name="' + areaName + '"]').prop('checked')){
		return 1;
	}

	//開催場所にチェックがある場合
	if($('input[name="' + loc + '"]').prop('checked')){
		return 1;
	}

	return -1;
}



/***  event csv format 変換テーブル  ****/
var ev_title = "eventtitle";	//タイトル
var ev_where = "where";		//開催場所（施設など）
var ev_place = "place";		//開催場所（部屋など）
var ev_whom  = "whom";		//対象者
var ev_what  = "what";		//内容
var ev_who   = "who";		//主催者
var ev_phone = "phone";		//連絡電話番号
var ev_email = "email";		//連絡電子メール
var ev_cont  = "contact";	//連絡記事
var ev_fee   = "fee";		//参加費
var ev_when  = "when";		//開催日　　yyyy/mm/dd
var ev_open  = "openTime";	//開始時間　hh:mm:ss  or  hh:mm
var ev_close = "closeTime";	//終了時間　hh:mm:ss  or  hh:mm
var ev_cat   = "tag1";		//識別・カテゴリー
var ev_url   = "url";		//URL
var ev_uid   = "uid";		//UID

//スクリプト内で作成
var ev_no    = "no";		//必須
var ev_year  = "year";		//イベント年　ev_when から取得
var ev_month = "month";		//イベント月
var ev_day   = "day";		//イベント日

var ev_area  = "area_name";	//地域名
/*********/

/***  target csv format 変換テーブル  ****/
var tar_label = "target_label";	//
var tar_id    = "target_id";	//
var tar_color = "color";	//
var tar_text  = "text_color";	//
var tar_icon  = "icon";		//
var tar_visi  = "visible";	//イベント表示選択用
var default_tar_color = "#ffffff";	//
var default_tar_text  = "#604037";	//
/*********/

/***  area csv format 変換テーブル  ****/
var area_label = "area_name";	//
var area_color = "color";	//
var area_text  = "text_color";	//
var area_icon  = "icon";
var area_visi  = "visible";		//イベント表示選択用
var default_area_color = "#ffffff";	//
var default_area_text  = "#604037";	//
/*********/

/***  location csv format 変換テーブル  ****/
var loc_area = "area_name";	//
var loc_name = "location_name";	//
var loc_id   = "location_id";	//
var loc_lat  = "lat";		//
var loc_lng  = "lng";		//
var loc_address = "address";	//
var loc_memo = "memo";		//
var loc_visi = "visible";	//イベント表示選択用
/*********/


//地域別表示用データのセット状態
var areaSettingFlg = 0;

//tag1のtargetId正当性確認
function targetId_check(nid){
	for(var i = 0 ; i < targetArray.length ; i++){
		if(targetArray[i][tar_id] == nid){
			return 1;
		}
	}
	return -1;
}


//eventArrayのデータをFullCalendarに設定する（月単位）
//function eventSetCalendar(){
function eventSetCalendar(cb){

	//地域の設定情報（初めてのイベント読み込み時のみ動作）
	if(areaSettingFlg == 0){
		Area_setting();
		areaSettingFlg = 1;
	}
	//************




	var source = new Array();

	var evtclass , evttitle , evtday , weekday;
	var evtcont;
	var tg1 = "<tr><th>";
	var tg2 = "</th><td>";
	var tg3 = "</td></tr>"; 

	//var bcolor , tcolor;
	var bcolor = default_tar_color;
	var tcolor = default_tar_text;

	//地図リンク用画像
	var maplink   = "<span class='icon maplink' ";

	var ev,loc,edata,areaName;
	var s_evtday="";
	var evt_day_content="";
	
	for(var i =0 ; i < eventArray.length ; i++){

		//var ev = eventArray[i];
		ev = eventArray[i];


		//**************
		//地域設定の確認　またはtag1の確認
		if(LocToSetArea(ev[ev_where],ev[ev_cat]) == -1){
			continue;
		}
		//**************


		//location位置情報の確認
		//var loc = getLocationLatLng(ev[ev_where]);
		loc = getLocationLatLng(ev[ev_where]);

		edata = new Object();

		//FullCalendar 用のデータセット
		edata['id']    = i + 1;

		if(loc != -1){
			ev[ev_area] = locationArray[loc][loc_area];
			edata['title'] = eventArray[i][ev_title];
		}else{
			edata['title'] = eventArray[i][ev_title];
		}

		//開催日 yyyy/mm/dd -> yyyy-mm-dd  FullCalendar formatに合わせる
		edata['start'] = eventArray[i][ev_when].replace(/\//g,"-");
		edata['start'] += " " + eventArray[i][ev_open];


		//地域によって色設定 ************
		//target優先、空白の場合 areaで色設定
		edata['color']     = default_area_color;
		edata['textColor'] = default_area_text;

		var looked = 0;
		for(var s = 0; s < targetArray.length ; s++){
			if(eventArray[i][ev_cat] == targetArray[s][tar_id]){
				edata['label']     = targetArray[s][tar_label];
				edata['color']     = targetArray[s][tar_color];
				edata['textColor'] = targetArray[s][tar_text];
				looked = 1;
				break;
			}
		}
		//targetがヒットしない場合
		//locationArrayの地域名からareaArrayを検索
		if(looked == 0){
			for(var s = 0; s < areaArray.length ; s++){
				//if(ev_area == areaArray[s][area_label]){
				if(ev[ev_area] == areaArray[s][area_label]){
					edata['color']     = areaArray[s][area_color];
					edata['textColor'] = areaArray[s][area_text];
					break;
				}
			}
		}
		//*******************************


		source.push(edata);


		evtclass = "calendar-event";

		//アコーディオン用のデータセット
		ev['no'] = i + 1;

		//when から　年月日取得
		buff = ev[ev_when].split("/"),
		ev[ev_year]  = buff[0];
		ev[ev_month] = buff[1];
		ev[ev_day]   = buff[2];	

		//イベント日から曜日を取得
		var evtday_str = ev[ev_year] + "/" + ev[ev_month] + "/" + ev[ev_day];
		evtday  = new Date(evtday_str);
		var week_num = evtday.getDay();
		weekday = JpWeekday[week_num];
		
		var evt_time = ev[ev_open].substr(0,5) + "〜" + ev[ev_close].substr(0,5);

		//過去イベントの表示
		if(PastEvent(ev[ev_when]) == 1){
			evtclass += " past";
		}
		
		evttitle = "";
		
		//日付のくくり
		if(s_evtday !== evtday_str){
			evt_day_content = $("<div id='day"+parseInt(ev[ev_day])+"' class='calendar-event-day w"+week_num+"'/>").appendTo("#events-title");
			//タイトルの日にち部分
			evttitle += "<div class='evt_date'><span class='evt_daynum'>"+ parseInt(ev[ev_day]) + "<span class='mint'>日</span></span><span class='evt_week'>（" + weekday + "）</span></div>";
		}

		
		//イベントのタイトル+内容をまとめるラッパー
		evttitle += "<div class='calendar-event-wrap'>";
		
		//イベントのタイトル
		evttitle += "<div id='evtid_" + ev[ev_no] + "_title' ";
		//evttitle += "onClick='selectEvent(" + ev[ev_no] + ")' ";

		evttitle += "class='" + evtclass + "' "; 
		evttitle += ">"; 
		//evttitle += "style='background-color:" + edata['color'] + "; ";
		//evttitle += "color:" + edata['textColor'] + ";' >";
		
		//タイトルの時間・場所
		evttitle += "<div class='ti_param'><span class='ti_time'>"+ evt_time + "</span><span class='tit_where'>" + ev[ev_where] + "</span></div>";
		
		evttitle += "<div class='tit_title'>" + ev[ev_title] + "</div>"
		
		//AM PM　fullcalendar に合わす
		//evttitle += (ev[ev_open].substr(0,2) < 12 ? 'Am' : 'Pm') + " ";
		//evttitle += (ev[ev_open].substr(0,2) < 12 ? 'A' : 'P') + " ";


		//area*************
		//location位置情報の確認
		if(loc == -1){
			/*
			if(ev[ev_area]){
				evttitle += "<div class='tit_loc'>" + ev[ev_area] + "</div> ";
			}
			*/

			//********
			if(ev[ev_cat]){
				evttitle += "<div class='tit_loc' style='background-color:"+ edata['color'] + ";'>" + edata['label'] + "</div> ";
			}
			//********

		}else{
			//地域名を表示しない仕様に変更
			evttitle += "<div class='tit_loc' style='background-color:"+edata['color']+";'>" + locationArray[loc][loc_area] + "</div> ";
		}
		//*****************

		evttitle += "</div>";

		//$("#events-title").append(evttitle);

		//イベントの詳細
		evtcont  = "<div id='evtid_" + ev[ev_no] +"' ";
		evtcont += "class='calendar-event-cont'>";
		
		if(ev[ev_what]){
			evtcont += "<div class='calendar-event-cont-body'>"+ev[ev_what]+"</div>";
		}

		evtcont += "<table class='evt_table eventclass'>";
		
		evtcont += tg1 + "名称"  + tg2 + ev[ev_title] + tg3;
		evtcont += tg1 + "日時"  + tg2 + "<b>" + ev[ev_year]+ "年" + parseInt(ev[ev_month])+ "月" + parseInt(ev[ev_day]) + "日(" + weekday + ") </b>" + evt_time + tg3;

		//evtcont += tg1 + "内容"  + tg2 + ev[ev_what] + tg3;
		evtcont += tg1 + "対象者" + tg2 + ev[ev_whom] + tg3;

		if(ev[ev_fee] == 0 || ev[ev_fee] ==""){
			evtcont += tg1 + "参加費" + tg2 + "無料" + tg3;
		}else{
			evtcont += tg1 + "参加費" + tg2 + ev[ev_fee] + "円" + tg3;
		}

		//location位置情報の確認
		if(loc == -1){
			//evtcont += tg1 + "場所"  + tg2 + ev[ev_where] + tg3;
			evtcont += tg1 + "場所"  + tg2 + "<b>" + ev[ev_where] + "</b>";
			if(ev[ev_place] != undefined){
				evtcont += ev[ev_place];
			}
			evtcont += tg3;

		}else{
			evtcont += tg1 + "場所<br>";
			evtcont += maplink + " onClick='eventMap_visible(" + loc + ")' >地図</span>";
			evtcont += tg2;
			evtcont += "<b>"+ev[ev_where] + "</b>";
			if(ev[ev_place] != undefined){
				evtcont += ev[ev_place];
			}

			//イベント開催場所のメモを表示
			var nmemo = locationArray[loc][loc_memo];
			if(nmemo !=""){
				evtcont += "<br />";
				evtcont += nmemo;
			}

			evtcont += tg3;
		}

		/*
		//電話発信機能 電話番号のチェック
		var teldata = telNumber(ev[ev_cont]);
		if(teldata == -1 ){
			evtcont += tg1 + "連絡先" + tg2 + ev[ev_cont] + tg3;
		}else{
			evtcont += tg1 + "連絡先" + tg2 + "<a href='tel:" + teldata + "' >" + ev[ev_cont] + "</a>" + tg3;
		}
		*/

		//連絡先を電話番号、メールアドレス、その他記事に分離
		//電話発信機能 電話番号のチェック
		if(ev[ev_phone]){
			var teldata = telNumber(ev[ev_phone]);
			if(teldata == -1 ){
				evtcont += tg1 + "連絡番号" + tg2 + ev[ev_phone] + tg3;
			}else{
				evtcont += tg1 + "連絡番号" + tg2 + "<a href='tel:" + teldata + "' >" + ev[ev_phone] + "</a>" + tg3;
			}
		}

		//電子メールのチェック
		if(ev[ev_email]){
			var emaildata = mailAddress(ev[ev_email]);
			if(emaildata == -1 ){
				evtcont += tg1 + "連絡メール" + tg2 + ev[ev_email] + tg3;
			}else{
				evtcont += tg1 + "連絡メール" + tg2 + "<a href='mailto:" + emaildata + "' >" + ev[ev_email] + "</a>" + tg3;
			}
		}

		//連絡記事（簡易チェック）
		if(ev[ev_cont] != ""){
			evtcont += tg1 + "連絡記事" + tg2 + ev[ev_cont] + tg3;
		}

		


		evtcont += tg1 + "主催者" + tg2 + ev[ev_who] + tg3;

		//url 追加（簡易チェック）
		if(ev[ev_url]){
			if(urlStrings(ev[ev_url]) == 1){
				evtcont += tg1 + "URL" + tg2;
				evtcont += "<a href='" + ev[ev_url] + "' target='_blank'>";
				evtcont += ev[ev_url] + "</p>" + tg3;
			}
		}

		//uid 追加
		var link_copy_btn = "";
		if(ev[ev_uid]){
			if(ev[ev_uid]){
				var linkstring = "?mon=" + ev[ev_year] + ev[ev_month] + "&uid=" + ev[ev_uid];

				evtcont += tg1 + "リンク" + tg2;
				evtcont += "<a href='" + linkstring + "'>" + ev[ev_uid] + "</a>";

				//link url copy
				var baseurl = window.location.href.split('?');
				//evtcont += '　<input type="button" value="URLをコピー" onClick="copyTextToClipboard(\'' + baseurl[0] + linkstring + '\')" />';
				link_copy_btn = '　<input type="button" value="このイベントのリンクをコピー" onClick="copyTextToClipboard(\'' + baseurl[0] + linkstring + '\')" />';
				evtcont += tg3;
			}
		}


		//色別検証用
		//evtcont += tg1 + "(color)" + tg2;
		//evtcont += "tag1:" + ev[ev_cat]  + "　";
		//evtcont += "area:" + ev[ev_area] + tg3;


		evtcont +="</table>";

		//evtcont +="<div class='btns'><button class='btn_close'>閉じる</button>"+link_copy_btn+"</div>"
		//evtcont +="<div class='btns'><button class='btn_close' onClick='selectEvent(" + ev['no'] + ")'>閉じる</button>"+link_copy_btn+"</div>"
		evtcont +="<div class='btns'><button class='btn_close' >閉じる</button>"+link_copy_btn+"</div>"

		evtcont += "</div><!-- /.calendar-event-cont -->";	
		evtcont += "</div><!-- /.calendar-event-wrap -->";	

		evt_day_content.append(evttitle+evtcont);	
		
		if(s_evtday !== evtday_str){
			s_evtday = evtday_str;
		}

	}

	//連想配列でsourceを渡し、カレンダーにイベントを追加する	
	//$('#calendar').fullCalendar('addEventSource', source );
	
	//カレンダーにイベントを表示
	var cal_day_nos = $("#calendar .fc-content-skeleton>table .fc-day-number");
	cal_day_nos.each(function(i){
		//.fc-event-container
		var cal_day_no = $(this);
		if(cal_day_no.hasClass("fc-other-month")){return;};
		var tar = $("#day"+cal_day_no.text());
		if(tar.length>0){
			var crs = cal_day_no.closest("tr");
			var idx = crs.find("td").index(cal_day_no.get(0));
			var crs2 = crs.closest("table");
			var event_box = crs2.find("tbody td").eq(idx);
			var event_box_inner = $("<div></div>").appendTo(event_box);
			var evts = tar.find(".calendar-event ");
			var is_over_max = false;
			var evts_max = 5;
			event_box.addClass("fc-event-container").addClass("e"+evts.length);
			if(evts.length>evts_max){
				is_over_max = true;
				event_box.addClass("overmax");
			}
			evts.each(function(ei){
				var cev = $(this);
				var cev_title = cev.find(".tit_title").text();
				var cev_style = cev.find(".tit_loc").attr("style");
				var maxclass = (is_over_max && evts_max-1<=ei) ? " overmax" : "";
				if(is_over_max && evts_max-1 === ei){
					event_box_inner.append('<span class="plus">'+(evts.length-evts_max+1)+'</span>');
				}
				if(!cev_style){ cev_style = ""; }
				event_box_inner.append('<a class="fc-event'+maxclass+'" style="'+cev_style+'"><span>'+cev_title+'</span></a>');
			});
		}

	});
		


	//callback化
	$(evttitle).ready(function(){
		//表示完了したらコールバック
		//現状、イベントの表示完了が検知できない状態です（意味なし機能）
		//alert("ready");
		cb();
	});
	//*********
	
	//$(".tit_loc")

}

var is_iOS = false;
if (navigator.userAgent.indexOf('iPhone') > 0 || navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('iPad') > 0) {
    is_iOS=true;
}
//クリップボードに文字列をコピーする
function copyTextToClipboard(textVal){
	// テキストエリアを用意する
	var copyFrom = document.createElement("textarea");
	// テキストエリアへ値をセット
	copyFrom.textContent = textVal;
 
	// bodyタグの要素を取得
	var bodyElm = document.getElementsByTagName("body")[0];
	// 子要素にテキストエリアを配置
	bodyElm.appendChild(copyFrom);
 
	// テキストエリアの値を選択
	copyFrom.select();
	// コピーコマンド発行
	var retVal = document.execCommand('copy');
	// 追加テキストエリアを削除
	bodyElm.removeChild(copyFrom);
 
	//return retVal;
	//alert(textVal + "\n：クリップボードにコピーしました");
	
	/*
	var yourCode = document.getElementById('day4');
	var range = document.createRange();
	range.selectNode(yourCode);
	window.getSelection().addRange(range);
	document.execCommand('copy');
	alert('コピーした');
	*/

	add_dialog(textVal + "<br>をクリップボードにコピーしました");
}


//イベントタイトルのアコーディオン機能
var openEvt = "";

function evtScroll(callEvt){
	
	//スクロール量の計算
	var targetY  = $(callEvt).offset().top;		//イベントタイトルの現在位置
	//targetY -= $(callEvt + "_title").height();	//イベントタイトルの高さ減算
	//targetY -= $("#menu-back").height();//メニューバックの高さ減算
	var plus_y = 0;
	if($("#header").length>0){
		plus_y = $("#header").height()+$("#fix_header").height();
	}
	//targetY+=plus_y;
	//int($("events-title").css("margin-top").split("px").join(""));
	//スクロール実施
	$("html,body").animate({scrollTop:targetY-plus_y},{duration: 300});

	//alert(callEvt);
	//alert(targetY);
}

//イベントの選択 //notalone-event.jsに移行
/*
function selectEvent(idno){
	var callEvt = '#evtid_' + idno;

	//if(openEvt != ""){
	if(false){
		//イベント詳細が開いている場合は、閉じてから次のオープンに進む
		$(openEvt).hide("blind" , "" , 200 ).promise().done(function(){

			if(openEvt == callEvt){
				openEvt = "";
			}else{
				$(callEvt).show("blind", "", 500 );
				openEvt = '#evtid_' + idno;

				evtScroll(openEvt);
			}

		});	
	}else{
		//イベント詳細が開いていない場合
		$(callEvt).show("blind", "", 400 );
		openEvt = '#evtid_' + idno;

		//evtScroll(openEvt);
	}

	//UID指定の後処理
	$(".calendar-event").css("visibility" , "visible");
	$("#calendar").css("visibility" , "visible");
}
*/

//UID指定のイベント表示
function uidCallEvent(idno){
	var callEvt = '#evtid_' + idno;
/*
	if(openEvt != ""){
		//イベント詳細が開いている場合は、閉じてから次のオープンに進む
		$(openEvt).hide("blind" , "" , 200 ).promise().done(function(){

			if(openEvt == callEvt){
				openEvt = "";
			}else{
				$(callEvt).show("blind", "", 500 );
				openEvt = '#evtid_' + idno;

				//evtScroll(openEvt);
			}

		});	
	}else{
		//イベント詳細が開いていない場合
		$(callEvt).show("blind", "", 1000 );
		openEvt = '#evtid_' + idno;

		//evtScroll(openEvt);
	}
*/

	//UID指定のイベントのみ表示する
	//$(".calendar-event").css("visibility" , "hidden");
	//$("#calendar").css("visibility" , "hidden");
	
	//CSSで表示・非表示を切り替え /*-- 個別ページ表示 --*/
	var event_wrap = $(callEvt).parent();
	$("body").addClass("event_single");
	event_wrap.addClass("single");
	event_wrap.append('<div class="btns"><a class="btn btn3" href="'+location.href.split("&uid")[0]+'">← 同じ月の他のイベントを見る</a></div>');
}


//過去イベントのチェック
function PastEvent(eday){
	var Today    = new Date();

	//年月日
	var month = "0" + TodayMonth;
	var day   = "0" + TodayDay;

	var search = TodayYear + "/" + month.substr(month.length - 2, 2) + "/" + day.substr(day.length - 2 , 2);

	if(search > eday){
		return 1;
	}else{
		return -1;
	}
}


//当日、または当日に一番近いイベントにスクロールする
function TodayEvent(){
	//年月日
	var month = "0" + TodayMonth;
	var day   = "0" + TodayDay;

	var search = TodayYear + "/" + month.substr(month.length - 2, 2) + "/" + day.substr(day.length - 2 , 2);

 	var data   = "";
	var len = eventArray.length;
	var openEvt = "";

	for(var i = 0 ; i < len ; i++){ 
		data = eventArray[i][ev_when];
		if(search <= data){
			openEvt = '#evtid_' + (i + 1) + "_title";

			if(!$(openEvt).length){
				continue;
			} 

			evtScroll(openEvt);

			break;
		}
	}
}

//eventArray の　keyにヒットしたイベント情報を返す
function SearchEvent( ukey , uword , type){
	//ukey  : eventArray[ukey]　検索するキー
	//uword : 検索ワード 
	//tyep  : 'eno' イベント番号を返す（デフォルト）

 	var data   = "";
	var len = eventArray.length;

	for(var i = 0 ; i < len ; i++){ 
		data = eventArray[i][ukey];
		if(uword == data){
			return i+1;
		}
	}
	return -1;
}


//eventLimit Click した日のイベントをスクロールアップ
//機能していない
function LimitEvent(fdate){
	//fdateはフォーマットかされた日付
	var search = fdate;

 	var data   = "";
	var len = eventArray.length;

	for(var i = 0 ; i < len ; i++){ 
		data = eventArray[i][ev_when];
		if(search <= data){

			var openEvt = '#evtid_' + (i + 1);
			evtScroll(openEvt);
//alert(openEvt);
			break;
		}
	}
}


//FullCalendar（イベントカレンダー）の初期設定
var CalendarMode = 1;	//初期で表示
var CalendarTop;	//カレンダーの位置記憶用
function events_init(){

	$(document).ready(function() {

		//******************

		$('#calendar').fullCalendar({

			//カスタムボタンによるヘッダーの設定
			customButtons: {				
        			myCustomToday: {
            				text: '基月',
					size: 'small',
            				click: function() {
               					//alert('今日へ');
						$('.fc-myCustomMonth-button').css({'visibility':'hidden'});
						$('#calendar').fullCalendar('changeView','month');
						//$('#calendar').fullCalendar('today');
						$('#calendar').fullCalendar('gotoDate',DefYear + "-" + DefMonth);

						readEvents(0 , function(){
							eventSetCalendar(function(){
								//当日直近日にスクロール
								TodayEvent();
							});
						});
            				}
	        		},
        			myCustomMonth: {
            				text: '戻',
            				click: function() {
               					//alert('前月へ');
						$('.fc-myCustomMonth-button').css({'visibility':'hidden'});
						$('#calendar').fullCalendar('changeView','month');
            				}
	        		},

				//カレンダー非表示ボタン
				myCustomBlock: {
					text: '●',
					click: function(){
						if(CalendarMode == 1){
							//現在top位置を記憶
							CalendarTop = $("#calendar").css("top");
							$("#calendar").animate({top:"0"},1000);
							$(".fc-view-container").hide("blind", "", 1000 );


							CalendarMode = 0;

						}else{
							//ブラインドを開いて表示を整える
							$(".fc-view-container").show("blind", "", 1000 ).promise().done(function(){

								//月Limit表示のため　日ー＞月で切り替える
								$('#calendar').fullCalendar('changeView','basicDay');

								$('#calendar').fullCalendar('changeView','month');

							});

							//元の位置に戻す
							$("#calendar").animate({top:CalendarTop},1000);

							CalendarMode = 1;
						}

						//強制的に月表示モードに戻す
						$('.fc-myCustomMonth-button').css({'visibility':'hidden'});



					       }
				},

				//設定メニューボタン
				myCustomSet: {
					text: '＊',
					click: function(){
							setArea_visible();
					       }
				},
        		
				myCustomPrev: {
            				text: '＜',
            				click: function() {
               					//alert('前月へ');
						$('.fc-myCustomMonth-button').css({'visibility':'hidden'});
						$('#calendar').fullCalendar('changeView','month');
						$('#calendar').fullCalendar('prev');

						readEvents(-1 , function(){
							eventSetCalendar(function(){
								//topへスクロール
								$("html,body").animate({scrollTop:0},{duration: 0});
							});
						});

            				}
	        		},
        			myCustomNext: {
            				text: '＞',
            				click: function() {
               					//alert('翌月へ');
						$('.fc-myCustomMonth-button').css({'visibility':'hidden'});
						$('#calendar').fullCalendar('changeView','month');
						$('#calendar').fullCalendar('next');

						readEvents(1 , function(){
							eventSetCalendar(function(){
								//topへスクロール
								$("html,body").animate({scrollTop:0},{duration: 0});
							});
						});

           				}
	        		},
			},


			header: {
				left: 'myCustomBlock,myCustomSet,myCustomMonth',

        			center: 'title',
				right: 'myCustomPrev,myCustomToday,myCustomNext'
			},
			//******************


			//タイトルのフォーマット
	        	titleFormat: {
        	    		month: 'YYYY年M月',	// 2013年9月
				week:  'YYYY年M月',
				day:   'YYYY年M月',
			},

 	       		// ボタン文字列
        		buttonText: {
            			prev:     '＜', // <
            			next:     '＞', // >
            			prevYear: '前年',  // <<
            			nextYear: '翌年',  // >>
            			today:    '今日',
            			month:    '月',
            			week:     '週',
            			day:      '日'
        		},
 
	        	// 月名称
        		monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
	        	// 月略称
        		monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
	        	// 曜日略称
        		dayNames: ['日', '月', '火', '水', '木', '金', '土'],
        		dayNamesShort: ['日', '月', '火', '水', '木', '金', '土'],
			//more表示の書式
			dayPopoverFormat:'YYYY年 M月 D日[(]ddd[)]',

			//AM PM入れない
		        timeFormat: 'T',	//AM PM
		        //timeFormat: '',	//AM PM

		        // 列の書式
        		columnFormat: {
            			month: 'ddd',    // 月
            			week:  'DD日[(]ddd[)]', // 7(月)
            			day:   'DD日[(]ddd[)]'    // 7(月)
        		},
 

			//イベントのクリック
			eventClick : function(event){
				//selectEvent(event.id);
			},

			//日のクリック
			/*
			dayClick : function(){
				//alert("day click");
				var moment = $('#calendar').fullCalendar('getDate');
				//alert(moment.format('YYYY-M-D'));
				$('#calendar').fullCalendar('gotoDate',moment.format('YYYY-M-D'));
				$('#calendar').fullCalendar('changeView','basicDay');
			},
			*/

			//イベントの最大表示数
    			eventLimit: false,
    			views: {
        			agenda: {
            				eventLimit: 5
        			}
    			},
			eventLimitText: '件',
			eventLimitClick : function(cellInfo){
				var date = $.fullCalendar.moment(cellInfo.date);
				
				//day 表示になる	
				var elm = $('#calendar');
				elm.fullCalendar('gotoDate', date );

				$('.fc-myCustomMonth-button').css({'visibility':'visible'});

				elm.fullCalendar('changeView','basicDay');
				
			},

		});

		/* 関数外に出す
		//callback化
		readEvents(0 , function(){
			eventSetCalendar(function(){
				TodayEvent();
			});
		});
		*/
	});
}

//非表示のカレンダーを表示にする
function showCalendar(){
	$("#calendar").css({
		"display" : "block"
	});

	$("#showBlock").css({
		"display" : "none"
	});
}

//**** for inquiry.html *******
var inquiryArray  = new Array();	//相談用配列（２次元：連想配列）

//相談ごとの選択 アコーディオン機能
var openInq = "";
function inqScroll(callInq){
	
	//スクロール量の計算
	var targetY = $(callInq + "_title").offset().top;	//タイトルの現在位置
	targetY -= $("#menu-back").height(); 		//メニューバックの高さ減算

	//スクロール実施
	$("html,body").animate({scrollTop:targetY},{duration: 500});

}

function selectInquiry(idno){
	var callInq = '#inqid_' + idno;

	if(openInq != ""){
		//イベント詳細が開いている場合は、閉じてから次のオープンに進む
		$(openInq).hide("blind" , "" , 200 ).promise().done(function(){

			if(openInq == callInq || idno == 0){
				openInq = "";

				//topに戻る
				var targetY = 0;
				$("html,body").animate({scrollTop:targetY},{duration: 500});
			}else{
				$(callInq).show("blind", "", 500 );
				openInq = '#inqid_' + idno;

				inqScroll(openInq);

			}

		});	
	}else{
		//イベント詳細が開いていない場合
		if(idno == 0){
			inqScroll("#inqid_1");
		}else{

			$(callInq).show("blind", "", 1000 );
			openInq = '#inqid_' + idno;

			inqScroll(openInq);
		}
	}
}

//inquiry（相談）ファイルを読み込みinquiryArrayに保存
function readInquiry(table){
	csvToArray( table , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			inquiryArray.push(rensou);
		}

		//そのままデータセット
		inquirySetData();

		//googlemap の初期設定
		//mapInit();
	});
}

//inquiry別のデータ分類
//inquiry table のデータは、カテゴリ単位に表示順にソートしておくこと
//***** 変換テーブル *******
var inq_cat1  = "category1";
var inq_cat2  = "category2";
var inq_name  = "name";
var inq_phone = "phone";
var inq_mail  = "mail";
var inq_addr  = "address";
var inq_memo  = "memo";
var inq_open  = "open";
var inq_close = "close";
var inq_url   = "url";
var inq_lat   = "lat";
var inq_lng   = "lng";

var maplink   = "<span class='icon maplink' ";

function inquirySetData(){
	var ary = inquiryArray;
	var len = inquiryArray.length;

	//category 1 は、５つが前提（settingでの決めが必要）
	//var max_cat1 = 5;
	var max_cat1 = 1;

	//*****************

	if(len > 0){
		//スタートのカテゴリ
		var cat1 = ary[0][inq_cat1];
		var cat2 = ary[0][inq_cat2];
		var now_id = 1;
		var buff = "";
		var cat1_f = 0;
		var cat2_f = 0;
	}else{
		return false;
	}

	for(var i = 0 ; i < len ; i++){
		if(cat1_f == 0){
			//buff += "<h3>" + ary[i][inq_cat1] + "</h3>";
			cat1_f = 1;
		}
		if(cat2_f == 0){
			buff += "<h4>" + ary[i][inq_cat2] + "</h4>";
			cat2_f = 1;
		}

		//max_cat1 を超えた場合はスルー


		//
		buff += "<h4 class='inq_name'>" + ary[i][inq_name] + "</h4>";

		if(ary[i][inq_phone] != ""){
			//電話発信機能 電話番号のチェック
			var teldata = telNumber(ary[i][inq_phone]);
			if(teldata == -1 ){
				buff += ary[i][inq_phone] + "<br />";
			}else{
				buff += "<a href='tel:" + teldata + "' >" + ary[i][inq_phone] + "<a><br />";
			}
		}

		if(ary[i][inq_addr] != ""){
			buff += ary[i][inq_addr];
		}

		//位置情報がある場合　名前をボタンに表示しマップ起動可能とする
		if(ary[i][inq_lat] != "" && ary[i][inq_lng] != ""){
			//buff += "<input class='inq_map' type='button' onClick='inquiryMap_visible(" + i + ")' value='地図' ><br />";
			buff += "　" + maplink + " onClick='inquiryMap_visible(" + i + ")' >地図</span><br />";

		}else{
			buff += "<br />";
		}

		if(ary[i][inq_mail] != ""){
			buff += ary[i][inq_mail] + "<br />";
		}

		if(ary[i][inq_open] != ""){
			buff += "開館：" + ary[i][inq_open] + "<br />";
		}

		if(ary[i][inq_close] != ""){
			buff += "休館：" + ary[i][inq_close] + "<br />";
		}

		if(ary[i][inq_memo] != ""){
			buff += ary[i][inq_memo] + "<br />";
		}

		if(ary[i][inq_url] != ""){
			buff += "<a href='" + ary[i][inq_url] + "' target='_blank'>" + ary[i][inq_url] + "</a><br />";
		}
		//

		if(i < (len -1)){

			if(cat1 == ary[i+1][inq_cat1]){
				if(cat2 != ary[i+1][inq_cat2]){
					cat2 = ary[i+1][inq_cat2];
					cat2_f = 0;
				}
			}else{
				//カテゴリが変わったらデータを書き出し
				$('#inqid_' + now_id + '_cont').html(buff);

				//各種フラグ更新
				cat1 = ary[i+1][inq_cat1];
				cat2 = ary[i+1][inq_cat2];
				cat1_f = 0;
				cat2_f = 0;
				now_id++;
				buff = "";
			}
		}

		buff += "<br />";
	}
	$('#inqid_' + now_id + '_cont').html(buff);
}

function inquiryMap_visible(locationNo){
	//location位置情報
	var nlat = inquiryArray[locationNo][inq_lat];
	var nlng = inquiryArray[locationNo][inq_lng];
	var latlng = new google.maps.LatLng(nlat , nlng);

	//******
	var html = "<div style='width:150px'>";
	//googlemapのルート検索起動
	html += "<input type='button' onClick='googleRoot(" + nlat + "," + nlng + ")' value='ルート検索'><br />";
	html += inquiryArray[locationNo][inq_name];
	html += "</div>";

        now_infowindow.setContent(html);
	//******

	now_marker.setPosition(latlng);
	mapCanvas.setCenter(latlng);

	$('#map_area').css({
		'visibility' : 'visible',
		'top'        : TopHeight
	});
}

function inquiryMap_hidden(){
        now_infowindow.close();

	$('#map_area').css({
		'visibility' : 'hidden',
		'top'        : DeviceHeight
	});
}


//******* about.html *****
function selectAbout(idno){
	var callInq = '#aboutid_' + idno;

	if(openInq != ""){
		//イベント詳細が開いている場合は、閉じてから次のオープンに進む
		$(openInq).hide("blind" , "" , 200 ).promise().done(function(){

			if(openInq == callInq || idno == 0){
				openInq = "";

				//topに戻る
				var targetY = 0;
				$("html,body").animate({scrollTop:targetY},{duration: 1000});
			}else{
				$(callInq).show("blind", "", 500 );
				openInq = '#aboutid_' + idno;

				inqScroll(openInq);
			}

		});	
	}else{
		//イベント詳細が開いていない場合
		if(idno == 0){
			inqScroll("#aboutid_1");
		}else{
			$(callInq).show("blind", "", 1000 );
			openInq = '#aboutid_' + idno;

			inqScroll(openInq);
		}
	}
}


//*********** common ****************

//CSVファイルの読み込み
function csvToArray(filename, cb) {
	//キャッシュしない
	$.ajaxSetup({
		cache: false
	});
	/*
	$.get({
	  url:  filename
	}).(function(csvdata, textStatus, jqXHR){
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
	}).fail(function(jqXHR, textStatus, errorThrown){
		cb(false);
	});
	*/
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

//電話番号のチェック
function telNumber(txt){
	var txt0 = txt;

	// -- () を削除して評価する
	var txt1 = txt0.replace(/-/g, '');
	var txt2 = txt1.replace(/\(/g, '');
	var number = txt2.replace(/\)/g, '');

	//data1 = txt.match(/^[0-9-]{6,9}$|^[0-9-]{12}$/);
	//data2 = txt.match(/^\d{1,4}-\d{4}$|^\d{2,5}-\d{1,4}-\d{4}$/);
	data1 = number.match(/^[0-9-]{9,12}$/);

	//if(!data1 && !data2){
	if(!data1){
		//alert(number + "　電話番号が不正です");
		return -1;
	}else if(number.substr(0,1) == "0"){
		//alert(number);
		return number;
	}else{
		//alert(number + "　0がありません");
		return -1;
	}
}

//メールアドレスのチェック
function mailAddress(txt){
	var txt0 = txt;

	return txt;


	/* メールアドレスのチェックに変更してください
	// -- () を削除して評価する
	var txt1 = txt0.replace(/-/g, '');
	var txt2 = txt1.replace(/\(/g, '');
	var number = txt2.replace(/\)/g, '');

	//data1 = txt.match(/^[0-9-]{6,9}$|^[0-9-]{12}$/);
	//data2 = txt.match(/^\d{1,4}-\d{4}$|^\d{2,5}-\d{1,4}-\d{4}$/);
	data1 = number.match(/^[0-9-]{9,12}$/);

	//if(!data1 && !data2){
	if(!data1){
		//alert(number + "　電話番号が不正です");
		return -1;
	}else if(number.substr(0,1) == "0"){
		//alert(number);
		return number;
	}else{
		//alert(number + "　0がありません");
		return -1;
	}
	*/
}


//URLのチェック
function urlStrings(txt){

	//return -1;

	data1 = txt.match(/^(http|ftp):\/\/.+$/);
	data2 = txt.match(/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/);

	if(!data1 && !data2){
		//alert("URLが不正です");
		return -1;
	}else{
		return 1;
	}
}


//googlemap root 検索
var win;
function googleRoot(lat,lng){
	var url = "http://maps.google.com/maps?";
	url += "daddr=" + lat + "," + lng + "&saddr=現在地&dirflg=d";

	if(!win || win.closed){
	}else{
		win.close();
	}

	var features = "menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes";

	win = window.open(url, "googleroot", features);
}

