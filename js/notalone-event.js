//notalone.js
var add_dialog;
jQuery(document).ready(function($){
	//共通
	var body = $("body");
	
	//FIXヘッダー
	/*
	var header = $("#fix_header");
	var header_top = header.offset().top;
	var is_fixed = false;
	$(window).scroll(function() {
		var sc = $(this).scrollTop();
		//if(nav_btn_tar.hasClass("on")){ close_nav(); }//スマホナビゲーション閉じる
		if(sc < header_top && is_fixed){
			body.removeClass("header_fixed");
			is_fixed = false;
		}else if(sc >= header_top && !is_fixed){
			body.addClass("header_fixed");
			is_fixed = true;
		}
	});
	*/
	/*
	function close_nav(){
		
	}
	*/
	
	//セット
	var search_set = $("#set_area");
	$("#set_bar").click(function(){
			setArea_visible();
	});
	
	
	//カレンダー
	var cal_bar = "#calendar .fc-toolbar";
	var cal = $("#calendar_wrap");
	$(document).on("tap",cal_bar+" button",function(e){
		e.stopPropagation();
	});
	$(document).on("tap",cal_bar,function(){
		if(cal.hasClass("on")){
			cal.removeClass("on");
			body.removeClass("cal_open");
		}else{
			cal.addClass("on");
			body.addClass("cal_open");
		}
	});
	
	$(document).on("tap",".fc-event-container",function(){
		var btn = $(this);
		var crs = btn.closest("tr");
		var idx = crs.find("td").index(this);
		var crs2 = crs.closest("table");
		var idx2 = crs2.find("tbody tr").index(crs.get(0));
		var day = crs2.find("thead tr").eq(idx2).find(".fc-day-number").eq(idx).text();
		var tar = $("#day"+day);
		var plusH = $("#fix_header").height()+$("#header").height();
		//console.log("idx:"+idx+"  idx2:"+idx2+"  day:"+day);
		$("html,body").animate({scrollTop:tar.offset().top-plusH},{duration: 300});
		
	});
	/*
	var cssanimstr="";
	for(var i = 1; i<=31; i++){
		cssanimstr+=".calendar-event-day:nth-child("+i+") { animation-delay: "+i*0.1+"s; }"
	}
	console.log(cssanimstr);
	*/
	
	//イベントコンテンツ
	var evt_con = $(".calendar-event-cont");
	$(document).on("tap",".calendar-event-wrap:not(.single) .calendar-event, .calendar-event-wrap .btn_close",function(){
		var btn = $(this);
		var tar = btn.closest(".calendar-event-wrap");
		var tar_con = tar.find(".calendar-event-cont");
		if(tar.hasClass("open")){
			tar.removeClass("open")
			tar_con.hide("blind", "", 300 );			
		}else{
			tar.addClass("open")
			tar_con.show("blind", "", 300 );
		}
	});
	
	
	//------------------------------------------------------------------● ダイアログ
	add_dialog = function(content,time,stage,oncomplete){
		var isStage =true;
		if($.type(stage)==="string"){
			stage=$(stage); 
		}else if($.type(stage)==="object"){
		}else{
			isStage = false;
			stage=$("body"); 
		}
		var wrapper1='<div class="common_dialog"><div class="dialog-inner">';
		var wrapper2='</div></div>';
		var tar_dialog = $(wrapper1+content+wrapper2).appendTo(stage);
		if(isStage){tar_dialog.addClass("onstage");}
		if(time!=="stop"){
			if(!time || time<=0){time = 2500;}//デフォルトの表示時間
			tar_dialog.fadeIn(400).delay(time).fadeOut(300,function(){$(this).remove();if(oncomplete){oncomplete();} });
		}else{
			tar_dialog.fadeIn(400);
		}
		tar_dialog.click(function(){
			$(this).fadeOut(300,function(){$(this).remove();});
			if(oncomplete){oncomplete();}
		});
	};
	
	

	
});

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