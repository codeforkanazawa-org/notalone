//notalone.js

//**********************
//window risize 時の処理
$(window).resize(function(){
	brows_init();
});
//**********************


//実行ページ名の格納変数
var thispage ="";

//デフォルトのメッセージ
var Def_info ="	<br />みんなで遊そぼ！<br /><br />まずは写真を<br />クリック！！<br />";

//初期化
function init(){
	//実行ページのチェック
	thispage = $("#thispage").html();

	switch(thispage){
		case "index.html"  :
			brows_init();
			//index_init();

			break;

		case "events.html" :
			//FullCalendarの初期化を先に実行
			events_init();
			brows_init();

			break;
	}	 
}


//ブラウザの初期化
function brows_init(){

	//共通部の設定****************
	//for menu
	var DeviceWidth    = window.innerWidth;		//ブラウザの横幅、スマホの横幅
	var DeviceHeight   = window.innerHeight;	//ブラウザの縦幅、スマホの縦幅
	var TopHeight = 30;				//トップメニューの縦幅
	var PcWidth = 480;				//パソコンの場合の横幅
	//*****************************

	//if(screen.width >= 480){
	if(DeviceWidth >= 480){
		//var BodyWidth = screen.width>=480? "480px" : "100%";
		var BodyWidth = PcWidth + "px";

		$("body").width(BodyWidth);
		var BodyWhite = parseInt(DeviceWidth,10) - parseInt(BodyWidth,10);

		var BodyLeftMargin = BodyWhite > 0 ? (BodyWhite / 2) + "px" : "0px";
		$("body").css({"margin-left" : BodyLeftMargin });

		$("#top-menu").width(BodyWidth);
		$("#top-menu").css({"left" : BodyLeftMargin });

		$("#myCalendar").width(BodyWidth);
		$("#myCalendar").css({"left" : BodyLeftMargin });

	}else{
		var BodyWidth = DeviceWidth + "px";
	}


	$("#top-menu").height(TopHeight);
	$("#top-menu").css({"line-height" : TopHeight + "px"});

	
	//個別部の設定
	//************************
	switch(thispage){
		//****************
		case "index.html" :

	//縦幅 0.35
	var PrivertHeight  = Math.round(DeviceHeight * 0.35);	//個人情報欄の縦幅確保
	var PhotoPadding = 5;				//個人情報　写真の余白

	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

	$("#privert").height(PrivertHeight);


	var MenuHeight = ( DeviceHeight - TopHeight - PrivertHeight ) / 3;

	$(".jobmenu").height(MenuHeight);
	$(".jobmenu").css({"line-height" : MenuHeight + "px"});

	//$("#photo > img").css({"height" : (PrivertHeight - PhotoPadding * 2) + "px" , "padding" : PhotoPadding + "px"});
	//$("#photo > img").css({"height" : (PrivertHeight * 0.7 - PhotoPadding * 2) + "px" , "padding" : PhotoPadding + "px"});

	//canvasサイズは縦を基準、横幅は元画像から比率で設定
	$("#myCanvas").css({"height" : (PrivertHeight - PhotoPadding * 2) + "px" , "padding" : PhotoPadding + "px"});

	//canvas内に初期画像を表示
	ImageSet('myCanvas','uploads/images/IMG_0763.jpg');


	//個人情報の読み出し　指標を表示
	favInit(0);


		break;

		//***************************
		case "events.html" :

	$("#calendar").css({
		"position" : "fixed",
		"top"    : TopHeight + "px",
		"width"  : BodyWidth,
		"font-size" : "14px",
		"background-color" : "white",
		"z-index" : 1 
	});

	//カレンダー表示の高さを規定（いずれかを設定）
	//縦横比率（数値が大きいほど縦が縮む）
	$('#calendar').fullCalendar('option', 'aspectRatio', 1.7);
	//カレンダーの高さ
	//$('#calendar').fullCalendar('option', 'contentHeight', 300);

	// コンテンツの高さ(px)
	var calendar_div = $("#calendar").height();

	$("#menu-back").height(TopHeight + calendar_div);

	//イベント欄ダミーの高さ(px)
	$("#dummy").height(DeviceHeight - calendar_div - $("#top_menu").height());

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

}

//2nd(next) menu link
function snd_index(link){
	switch(link){
		case "events" : location.href="events.html";
				break;
 		case "aidmap" :	location.href="sub/map.html";
				break;
		case "advice" :
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
	var setdata = "";

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
			setdata += fav_name + "<br />";
			setdata += "・" + calculateAge(fav_bday) + "<br />";
		}else{
			//入力欄クリア
			$("#name" + i).val('');
			$("#bday" + i).val('');		
		}
	}

	if(count>0){
		$("#myFavFamily").html(setdata);
	}else{
		//設定がない場合（デフォルトメッセージ)
		$("#myFavFamily").html(Def_info);
	}
}

//誕生日からの経過年月日を算出（出産予定日対応）
function calculateAge(birthday) {
	// birth[0]: year, birth[1]: month, birth[2]: day
	var  birth = birthday.split('/');
	// 文字列型に明示変換後にparseInt
	var _birth = parseInt("" + birth[0] + birth[1] + birth[2]);

	var  today = new Date();
	// 文字列型に明示変換後にparseInt
	var _today = parseInt("" + today.getFullYear() + affixZero(today.getMonth() + 1) + affixZero(today.getDate()));






    	//経過月、日数を算出
	//今年の誕生日までの日数
	var  strToday    = today.getFullYear() + "/" + affixZero(today.getMonth() + 1) + "/" +  affixZero(today.getDate());
	//var  thisBirthday = today.getFullYear() + "/" + birth[1] + "/" + birth[2];

	if(_birth > _today){
		//出産予定の場合（未来月）		
		var  diffDays = getDiff(strToday , birthday) - 1;

		//平均月数(365/12)
		var diffMonth = diffDays / (365 / 12);
		//return diffDays + "日後（" + diffMonth.toFixed(1) + "月）"; 		
		return diffDays + "日後"; 		
	}else{
		//誕生日の場合
		//年数確定
		var Age = parseInt((_today - _birth) / 10000);

		//今年の誕生日
		var  thisBirthday = today.getFullYear() + "/" + birth[1] + "/" + birth[2];
		var  diffDays = getDiff(thisBirthday , strToday);

		//誕生日前の場合（うるう年考慮せず）
		if(strToday < thisBirthday){
			diffDays = 365 - diffDays;
		} 

		//平均月数(365/12)
		var diffMonth = parseInt(diffDays / (365 / 12));

		if(Age < 1){
			if(diffMonth <= 3){
				//return diffDays + "日目（" + diffMonth + "月）";
				return diffDays + "日目";
			}else{
				//return diffMonth + "ケ月（" + diffDays + "日）";
				return diffMonth + "ケ月";
			}
		}else{
			//return Age + "歳" + diffMonth + "月（" + diffDays + "日）"; 
			return Age + "歳" + diffMonth + "ケ月"; 
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

/*
年間 365日　４年に１回　366日
月
   1  2  3   4   5   6   7   8   9  10  11  12
  31 28 31  30  31  30  31  31  30  31  30  31
    (29)
  31 59 90 120 151 181 212 243 273 304 334 365

  誕生日から今日までの日数を算出する　・・・　総日数
　日数を365日で割る　・・・　年数を算出
　　総日数から、年数分の日数を減算する
　　年数を４で割る　・・・　割れた回数分　総日数から減算する（うるう年対策）
　　　０の場合（４年未満）、誕生年から現在年まで、西暦でうるう年を判断し
　　　　

うるう年
（1）西暦年号が4で割り切れる年をうるう年とする。
（2）（1）の例外として、西暦年号が100で割り切れて400で割り切れない年は平年とする。

　２月２９日生まれは、２８日を起算日とする　
*/





function myfavSet(){
	//日付データを作成する
	//expires=' + new Date(2030, 1).toUTCString();
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


//ローカル画像ファイルの表示
function localImageSet(){
$("#uploadFile").change(function() {
    var canvas = $("#myCanvas");
    var ctx = canvas[0].getContext("2d");

    // 選択されたファイルを取得
    var file = this.files[0];

    // 画像ファイル以外は処理中止
    if (!file.type.match(/^image\/(png|jpeg|gif)$/)) return;

    var image = new Image();
    var reader = new FileReader();

    // File APIを使用し、ローカルファイルを読み込む
    reader.onload = function(evt) {

      // 画像がloadされた後に、canvasに描画する
      image.onload = function() {
        //ctx.drawImage(image, 0, 0);
      }

      // 画像のURLをソースに設定
      image.src = evt.target.result;

      ImageSet('myCanvas' , image.src);

      //
      //$('#urldata').val(image.src);
      //alert(image.src);

    }

    // ファイルを読み込み、データをBase64でエンコードされたデータURLにして返す
    reader.readAsDataURL(file);

  });
}

//canvasに指定の画像を表示する
function ImageSet(canvasname,imagename){
    var canvas  = $('#' + canvasname);
    var context = canvas[0].getContext('2d');

    var image = new Image();
    image.src = imagename;

    image.addEventListener('load', function() {
	var swidth  = image.width;
	var sheight = image.height;
	var sexp    = swidth / sheight;

        var iheight = canvas.height();
        var iwidth  = parseInt(iheight * sexp);
 
	//canvasのサイズ変更は　css は NG（縦長） --> attr　で設定
	$(canvas).attr('width'  , iwidth);
	$(canvas).attr('height' , iheight);

        //context.drawImage(image, 100, 100);
        context.drawImage(image, 0 , 0 , iwidth , iheight);
    }, false);
}
//****************************




//**** for events.html *******
var Today    = new Date();

//基準年月
var SetYear  = Today.getFullYear();
var SetMonth = Today.getMonth() + 1; 	//month = 0から始まる

var event_dir = "events/";	//イベントファイルの保存ディレクトリ
var event_ext = ".csv";		//イベントファイルの拡張子

var eventArray = new Array();	//イベント用配列（２次元：連想配列）

var JpWeekday = ['日','月','火','水','木','金','土'];	//日本語曜日


//基準月に応じたファイルを読み込みeventArrayに保存
function readEvents(target){
	//配列初期化
	eventArray = new Array();

	//イベント一覧、fullcalendarイベント初期化
	$('#events-title').empty();
	$('#calendar').fullCalendar('removeEvents');


	switch(target){
		case  0 : //初期・当月または基準月
			  SetYear  = Today.getFullYear();
			  SetMonth = Today.getMonth() + 1; 
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
	}

	var thisMonth = SetYear + ("0" + SetMonth).substr(-2);
	var eventfile = event_dir + thisMonth + event_ext;

	//csvToArray("events/201511.csv", function(data) {
	csvToArray( eventfile , function(data) {

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			eventArray.push(rensou);
		}
	});
}

//eventArrayのデータをFullCalendarに設定する（月単位）
function eventSetCalendar(){

	var source = new Array();

	var evtclass , evttitle , evtday , weekday;
	var evtcont;
	var bcolor , tcolor;

	for(var i =0 ; i < eventArray.length ; i++){
		var edata = new Object();

		edata['id']    = eventArray[i]['no'];
		edata['title'] = eventArray[i]['eventname'];
		edata['start'] = eventArray[i]['year'] + "-" + eventArray[i]['month'] + "-" + eventArray[i]['day'];

		//対象年齢によって色設定 ************
		//define化が必要
		//color: 'yellow',   // an option!
	    	//textColor: 'black' // an option!

		switch(eventArray[i]['icon']){
			case 'over5'  : bcolor = "green";	break;
			case 'over8'  : bcolor = "blue";	break;
			case 'over10' : bcolor = "red";		break;
		}
		edata['color']     = bcolor;
		edata['textcolor'] = tcolor;
		//*******************************


		source.push(edata);

		//イベント欄に一覧表示する
		if((i % 2) == 0){
			evtclass = "calendar-event-even";
		}else{
			evtclass = "calendar-event-odd";
		}

		var ev = eventArray[i];

		//イベント日から曜日を取得
		evtday  = new Date(ev['year'] + "/" + ev['month'] + "/" + ev['day']);
		weekday = JpWeekday[evtday.getDay()];

		//イベントのタイトル
		evttitle  = "<div id='evtid_" + ev['no'] + "_title' ";
		evttitle += "onClick='selectEvent(" + ev['no'] + ")' ";
		evttitle += "class='" + evtclass + "'>";
		evttitle += ev['day'] + "日(" + weekday + ")　";
		evttitle += ev['starttime'] + "〜　";
		evttitle += ev['eventname'];
		evttitle += "</div>";

		$("#events-title").append(evttitle);

		//イベントの詳細
		evtcont  = "<div id='evtid_" + ev['no'] +"' ";
		evtcont += "class='calendar-event-cont'>";
		evtcont += "時間　"  + ev['starttime'] + "〜" + ev['endtime'] + "<br />";
		evtcont += "内容　"  + ev['contents'] + "<br />";
		evtcont += "場所　"  + ev['place'] + "<br />";
		evtcont += "対象者　" + ev['target'] + "<br />";
		evtcont += "申込み　" + ev['entry'] + "<br />";
		evtcont += "参加非　" + ev['fee'] + "<br />";
		evtcont += "画像　"  + ev['icon'];
		evtcont += "</div>";	

		$("#events-title").append(evtcont);	
	}

	//連想配列でsourceを渡し、カレンダーにイベントを追加する	
	$('#calendar').fullCalendar('addEventSource', source );
}


//イベントタイトルのアコーディオン機能
var openEvt = "";

function evtScroll(callEvt){
	
	//スクロール量の計算
	var targetY  = $(callEvt).offset().top;		//イベントタイトルの現在位置
	targetY -= $(callEvt + "_title").height();	//イベントタイトルの高さ減算
	targetY -= $("#menu-back").height(); 		//メニューバックの高さ減算
	//スクロール実施
	$("html,body").animate({scrollTop:targetY},{duration: 1000});

}

//イベントの選択
function selectEvent(idno){
	var callEvt = '#evtid_' + idno;

	if(openEvt != ""){
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
		$(callEvt).show("blind", "", 1000 );
		openEvt = '#evtid_' + idno;

		evtScroll(openEvt);
	}
}

//CSVファイルの読み込み
function csvToArray(filename, cb) {
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


//FullCalendar（イベントカレンダー）の初期設定
function events_init(){

	$(document).ready(function() {

		//eventデータの読み込み
		//readEvents();
		readEvents(0);

		//******************

		$('#calendar').fullCalendar({

			//カスタムボタンによるヘッダーの設定
			customButtons: {
        			myCustomToday: {
            				text: '今日',
					size: 'small',
            				click: function() {
               					//alert('今日へ');
						$('#calendar').fullCalendar('today');

						readEvents(0);
						setTimeout("eventSetCalendar()" , 1000);
            				}
	        		},
        			myCustomPrev: {
            				text: '＜前月',
            				click: function() {
               					//alert('前月へ');
						$('#calendar').fullCalendar('prev');

						readEvents(-1);
						setTimeout("eventSetCalendar()" , 1000);
            				}
	        		},
        			myCustomNext: {
            				text: '翌月＞',
            				click: function() {
               					//alert('翌月へ');
						$('#calendar').fullCalendar('next');

						readEvents(1);
						setTimeout("eventSetCalendar()" , 1000);
           				}
	        		},
			},


			header: {
        			//left: 'prev,next today myCustomButton',
				left: 'title',
        			//center: 'title',
				center: '',
        			//right: 'month,agendaWeek,agendaDay'
				right: 'myCustomToday myCustomPrev,myCustomNext'
			},
			//******************


			//タイトルのフォーマット
	        	titleFormat: {
        	    		month: 'YYYY年M月',	// 2013年9月			
			},

 	       		// ボタン文字列
        		buttonText: {
            			prev:     '前月', // <
            			next:     '翌月', // >
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
        		dayNamesShort: ['日', '月', '火', '水', '木', '金', '土'],
			//more表示の書式
			dayPopoverFormat:'YYYY年 M月 D日[(]ddd[)]',

			/* 
	       		// イベントソース（例）
        		eventSources: [
            		{
                		events: [
                    		{
                       			title: 'event1',
                        		start: '2015-11-01',
					color: 'yellow',   // an option!
	    				textColor: 'black' // an option!
                    		},
                    		{
                       			title: 'event2',
                        		start: '2015-11-02',
                        		end: '2015-11-03',
					color: 'red',   // an option!
	    				textColor: 'white' // an option!
                    		},
                    		{
                       			title: 'event3',
                        		start: '2015-11-05 12:30:00',
                        		allDay: false // will make the time show
                    		}
                		],
            		}
        		],
			*/

			//イベントのクリック
			eventClick : function(event){
				selectEvent(event.id);
			},

			/*
			//日のクリック
			dayClick : function(){
				alert("day click");
			},
			*/

			//イベントの最大表示数
    			eventLimit: true,
    			views: {
        			agenda: {
            				eventLimit: 1,
        			}
    			},
			eventLimitText: '件あり'
		});

		//イベント情報の設定（eventArrayへのデータ設定完了までの十分な時間を確保する）
		setTimeout("eventSetCalendar()" , 1000);
	});
}
