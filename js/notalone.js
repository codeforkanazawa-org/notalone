//notalone.js

//**********************
//window risize 時の処理
$(window).resize(function(){
	brows_init();
});
//**********************


//実行ページ名の格納変数
var thispage ="";

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

	if(screen.width >= 480){
		//var BodyWidth = screen.width>=480? "480px" : "100%";
		var BodyWidth = PcWidth + "px";

		$("body").width(BodyWidth);
		var BodyWhite = parseInt(DeviceWidth,10) - parseInt(BodyWidth,10);

		var BodyLeftMargin = BodyWhite > 0 ? (BodyWhite / 2) + "px" : "0px";
		$("body").css({"margin-left" : BodyLeftMargin });

		$("#top-menu").width(BodyWidth);
		$("#top-menu").css({"left" : BodyLeftMargin });
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

	var PrivertHeight  = Math.round(DeviceHeight * 0.35);	//個人情報欄の縦幅確保
	var PhotoPadding = 5;				//個人情報　写真の余白

	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

	$("#privert").height(PrivertHeight);


	var MenuHeight = ( DeviceHeight - TopHeight - PrivertHeight ) / 3;

	$(".jobmenu").height(MenuHeight);
	$(".jobmenu").css({"line-height" : MenuHeight + "px"});

	$("#photo > img").css({"height" : (PrivertHeight - PhotoPadding * 2) + "px" , "padding" : PhotoPadding + "px"});

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
			dateFormat:'yy/mm/dd'
		});

		$(idname).datepicker("option", "showOn", 'button');
		$(idname).datepicker("option", "buttonImageOnly", true);
		$(idname).datepicker("option", "buttonImage", 'images/ico_calendar.png');
	}
});


//******* cookie ********
//favorite max 10個
var MyFav_count = 10;
var MyFav_tab   = 4;
var MyFavStop = new Array();
MyFavStop[1] = {root: 2 , stop:  5 , name: '堀内東'};
MyFavStop[2] = {root: 4 , stop: 10 , name: '万葉台'};
MyFavStop[3] = {root: 5 , stop: 15 , name: '白山野々市広域消防本部Ａ'};
MyFavStop[4] = {root: 6 , stop:  4 , name: '粟田'};
MyFavStop[5] = {root: 6 , stop:  4 , name: '粟田'};
MyFavStop[6] = {root: 6 , stop:  4 , name: '粟田'};
MyFavStop[7] = {root: 6 , stop:  4 , name: '粟田'};
MyFavStop[8] = {root: 6 , stop:  4 , name: '粟田'};
MyFavStop[9] = {root: 6 , stop:  4 , name: '粟田'};
MyFavStop[10] = {root: 6 , stop:  4 , name: '粟田'};
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
	var emt;
	for(var s = 1 ; s<= MyFav_tab ; s++){
		emt = document.getElementById("fav" + s);
		emt.innerHTML = "☆" + s;
		emt.style.cssText = document.getElementById("fav0").style.cssText;
	}

	for(var i = 1; i<= MyFav_count ; i++){		
		MyFavStop[i]['root'] = getCookie("root"+i);
		MyFavStop[i]['stop'] = getCookie("stop"+i);
		MyFavStop[i]['name'] = getCookie("name"+i);
	}
}

//お気に入り初期化
function favInit(){
	//*** cookie *****
	getMyFav();

	var fav_i;
	var fav_name;
	var st;

	//お気に入りのタブ表示は４つまで
	for(i=1 ; i<=MyFav_tab ;i++){
		if(MyFavStop[i]['name'] != ""){
			//document.getElementById("fav"+i).innerHTML = MyFavStop[i]['name'].substr(0,4);
			fav_i = document.getElementById("fav"+i);

			//バス停名から頭の数字：を削除する
			fav_name = MyFavStop[i]['name'];
			//: の位置を見つける
			st = fav_name.indexOf(":",0);
			if(st > 0) st++;

			//バス停名の長さを制限　５文字
			fav_i.innerHTML = fav_name.substr(st,5);

			fav_i.style.background = bc[MyFavStop[i]['root']]['bg'];			
			fav_i.style.color      = bc[MyFavStop[i]['root']]['txt'];			
		}
	}
}

//お気に入り
function myfavMenu(){
	//cookie error 時強制的に削除する
	//alert(document.cookie);
	//mayfavDel_all();

	if (!navigator.cookieEnabled) {
		alert("ブラウザの設定にてクッキーの受け入れを有効にしてください。");
		return;
	}

	//var MyFav_count = 4;
	//MyFavStop[1] = {root: 2 , stop:  5 , name: '堀内東'};

	var info = "お気に入りのバス停<br />";

	var stopName = "";
	var divStyle = "";
	var favLink  = "";


 	for(var i = 1 ; i <=MyFav_count ; i++){

		stopName = MyFavStop[i]['name'];
		if(stopName == ""){
			stopName = "☆" + i;
			divStyle = "";
			favLink  = "";
		}else{
			divStyle  = "margin-left:50px; padding-left:10px; background-color:" + bc[MyFavStop[i]['root']]['bg'] + ";";
			divStyle += "color:" + bc[MyFavStop[i]['root']]['txt'] + ";";
			favLink   = "onclick='myfav(" + i + ")' ";
		}

		info += "<div style='clear:left;'>";
		info += 	"<div style='float:left'>";
		info += 		"<input name='mfset' type='radio' value='" + i + "' />";
		info += 	"</div>";

		info += 	"<div style='" + divStyle + "' " + favLink + ">";
		info += 		stopName;
		info += 	"</div>";
		info += "</div>";
	}

	info += "<input type='button' onclick='myfavSet()' value='登録' />";
	info += "<input type='button' onclick='myfavDel()' value='削除' />";
	info += "<input type='button' onclick='myfavClose()' value='中止' />";
	
	document.getElementById("favInfo").innerHTML = info;
}

function myfavSet(){
	//日付データを作成する
	//expires=' + new Date(2030, 1).toUTCString();
	var date1 = new Date(2030, 1).toUTCString();
	//2030年1月 日付データをセットする

	if(Root_no == null || Root_no == 0 || Root_stop_no == null || Root_stop_no == 0){
		alert("バス停を指定してください");
	}else{
		//Root_no
		//Root_stop_no
		//Root_stop_name
		/*
		var emt   = document.getElementById("stop");
		var index = emt.selectedIndex;
		var stop_name = emt.options[index].text;
		*/
		var stop_name = Root_stop_no + ":" + Root_stop_name;

		var radioList  = document.getElementsByName("mfset");
		var fno;
		var str = stop_name + "登録できません。";

		for(var i=0; i<radioList.length; i++){
			if (radioList[i].checked) {
				
				//** cookie set
				fno = radioList[i].value;
				document.cookie = "root" + fno + "=" + Root_no + ";expires=" + date1;
				document.cookie = "stop" + fno + "=" + Root_stop_no + ";expires=" + date1; 
				document.cookie = "name" + fno + "=" + escape(stop_name) + ";expires=" + date1;
			
				str = stop_name + "を☆" +  radioList[i].value + "に登録しました。";
				//favInfo を閉じてからリセットルート
				myfavClose();
				//resetRoot();
				favInit();

				break;
			}
		}
		//alert(str);
	}
}

//cookie 強制削除
function mayfavDel_all(){
	//日付データを作成する
	var date1 = new Date();
	//1970年1月1日00:00:00の日付データをセットする
	date1.setTime(0);
  	//有効期限を過去にして書き込む
	for(var i=1; i<= MyFav_count ; i++){
		//** cookie set
		fno = i;
		document.cookie = "root" + fno + "=;expires=" + date1.toGMTString();
		document.cookie = "stop" + fno + "=;expires=" + date1.toGMTString(); 
		document.cookie = "name" + fno + "=;expires=" + date1.toGMTString();
	}

}


function myfavDel(){
	//日付データを作成する
	var date1 = new Date();
	//1970年1月1日00:00:00の日付データをセットする
	date1.setTime(0);
  	//有効期限を過去にして書き込む

	var radioList  = document.getElementsByName("mfset");
	var fno;
	var str = "削除できません。";

	for(var i=0; i<radioList.length; i++){
		if (radioList[i].checked) {
			//** cookie set
			fno = radioList[i].value;
			document.cookie = "root" + fno + "=;expires=" + date1.toGMTString();
			document.cookie = "stop" + fno + "=;expires=" + date1.toGMTString(); 
			document.cookie = "name" + fno + "=;expires=" + date1.toGMTString();
			
			str = "☆" +  radioList[i].value + "から削除しました。";
			//document.cookie = "counts=;expires="+date1.toGMTString();

			//favInfo を閉じてからリセットルート
			myfavClose();
			//resetRoot();
			favInit();

			break;
		}
	}
	//alert(str);
}


function myfavClose(){
	$("#favInfo").slideUp("slow");

	//document.getElementById("favInfo").innerHTML = "";
}

function myfav(no){
	var f_root = MyFavStop[no]['root'];
	var f_stop = MyFavStop[no]['stop'];

	if(f_root == "" || f_stop == "" || f_stop == 0){
		return;
	}

	//すべての選択を閉じる。フラグのリセット
	$("#stop_data_" + Root_stop_no).slideUp("slow");
	$("#root_data_" + Root_no).slideUp("slow");
	Root_open_flg = false;
	Stop_open_flg = false;


	selectRoot(f_root);

	setTimeout('selectStop(' + f_stop + ')' , 1000);

	//selectStop(f_stop);

	//サイトでは、バス停リストの作成に遅延あり。一定タイミング後に実施
	//setTimeout('myfav_later()' , 1000);

	myfavClose();
}

//myfav() 後処理
function myfav_later(){
	//バス停位置をトップにスライドする
	var targetY = $('#stop_' + Root_stop_no).offset().top;
	$("html,body").animate({scrollTop:targetY});
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
			eventClick (event){
				selectEvent(event.id);
			},

			/*
			//日のクリック
			dayClick (){
				alert("day click");
			},
			*/
		});

		//イベント情報の設定（eventArrayへのデータ設定完了までの十分な時間を確保する）
		setTimeout("eventSetCalendar()" , 1000);
	});
}
