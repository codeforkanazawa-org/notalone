//notalone.js

//window risize 時の処理
$(window).resize(function(){
	init();
});


//ブラウザ初期化
function init(){
	//ブラウザ表示設定****************
	//for menu
	var DeviceWidth    = window.innerWidth;		//ブラウザの横幅、スマホの横幅
	var DeviceHeight   = window.innerHeight;	//ブラウザの縦幅、スマホの縦幅
	var TopHeight = 30;				//トップメニューの縦幅
	var PcWidth = 480;				//パソコンの場合の横幅

	//**** for index.html ****
	var PrivertHeight  = Math.round(DeviceHeight * 0.35);	//個人情報欄の縦幅確保
	var PhotoPadding = 5;				//個人情報　写真の余白
	//************************
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


	//****for index.html *******
	$("#menu-back").height(TopHeight);
	$("#menu-back").css({"line-height" : TopHeight + "px"});

	$("#privert").height(PrivertHeight);


	var MenuHeight = ( DeviceHeight - TopHeight - PrivertHeight ) / 3;

	$(".jobmenu").height(MenuHeight);
	$(".jobmenu").css({"line-height" : MenuHeight + "px"});

	$("#photo > img").css({"height" : (PrivertHeight - PhotoPadding * 2) + "px" , "padding" : PhotoPadding + "px"});
	//***************************


	//**** for events.html ******
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
	//***************************
}



//topメニュー
function top_index(){
	location.href = "index.html";
}

//****for index.html ********
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
