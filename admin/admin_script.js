var add_dialog;
var set_cont_area_event = function(){};
$(function() {
	
	$(document).on("change","input[type='time']",function(){
		var tar = $(this);
		var val = tar.val();
		val = val.split("：").join(":");
		val_a = val.split(":");
		if(val.match(/[^0-9:]/g)){
			add_dialog("半角数字以外の文字が使われています<br>「08:08」のように半角英数で記入してください",false,"#cont_area_inner");
		}

		if(val_a.length<2){
			val ="";
			add_dialog("時間の書式が正しくありません<br>「08:08」のように半角英数で記入してください",false,"#cont_area_inner");
		}else{
			if(val_a[0].length === 1){
				val_a[0] = "0" + val_a[0];
			}else if(val_a[0].length !== 2){
				val_a[0] = "00";
			}
			if(val_a[1].length === 1){
				val_a[1] = "0" + val_a[1];
			}else if(val_a[1].length !== 2){
				val_a[1] = "00";
			}
			val = val_a[0]+":"+val_a[1];
		}
		tar.val(val);
	});

	//------------------------------------------------------------------● セレクトのその他用
	set_cont_area_event = function(){
		var other_input = $("input[data-type='other']");
		if(other_input.length>0){
			other_input.each(function(){
				var tar = $(this);
				var tar_select = tar.closest("td").find("select");
				var s_val = tar_select.val();
				var s_data_val = tar_select.attr("data-value");
				if(!s_val){
					tar_select.val("other");
					tar.val(s_data_val);
					set_other_select_val(tar,tar_select);
				}
				tar_select.change(function(){
					set_other_select_val(tar,tar_select,true);
				});
				tar.change(function(){
					if(tar.val() && tar.val()!==""){
						tar_select.val("other");
						set_other_select_val(tar,tar_select);
					}
				});
				set_other_select_val(tar,tar_select);
			});

		}
		function set_other_select_val(tar,tar_select,is_focus){
			var select_val = tar_select.val();
			if(select_val==="other"){
				if(!tar.attr("id") || tar.attr("id")===""){
					tar.attr("id",tar_select.attr("id"));
					tar_select.attr("id","");
				}
				tar.css({display:"block"});
				if(is_focus){tar.focus();}
			}else{
				if(!tar_select.attr("id") || tar_select.attr("id")===""){
					tar_select.attr("id",tar.attr("id"));
					tar.attr("id","");
				}
				tar.css({display:"none"});
			}
		}
	}

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
	
	//------------------------------------------------------------------● 文字コード保存
	function set_encode(){
		console.log("set_encode");
		var fe = $("#fencode");
		var ue = $("#upfencode");
		var c_fe = $.cookie("fencode");
		var c_ue = $.cookie("upfencode");
		console.log("c_fe："+c_fe);

		
		if(c_fe){ fe.val(c_fe);}
		if(c_ue){ ue.val(c_ue);}
		fe.change(function(){
			$.cookie("fencode", fe.val(), { expires: 999 });
		});
		ue.change(function(){
			console.log("ue_change："+ue.val());
			$.cookie("upfencode", ue.val(), { expires: 999 });
			document.test.reset();
			//LocalFileLoad();
		});

	}
	if($("#fencode").length>0){ set_encode(); }
});
/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a)}else{if(typeof exports==="object"){module.exports=a(require("jquery"))}else{a(jQuery)}}}(function(f){var a=/\+/g;function d(i){return b.raw?i:encodeURIComponent(i)}function g(i){return b.raw?i:decodeURIComponent(i)}function h(i){return d(b.json?JSON.stringify(i):String(i))}function c(i){if(i.indexOf('"')===0){i=i.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\")}try{i=decodeURIComponent(i.replace(a," "));return b.json?JSON.parse(i):i}catch(j){}}function e(j,i){var k=b.raw?j:c(j);return f.isFunction(i)?i(k):k}var b=f.cookie=function(q,p,v){if(arguments.length>1&&!f.isFunction(p)){v=f.extend({},b.defaults,v);if(typeof v.expires==="number"){var r=v.expires,u=v.expires=new Date();u.setMilliseconds(u.getMilliseconds()+r*86400000)}return(document.cookie=[d(q),"=",h(p),v.expires?"; expires="+v.expires.toUTCString():"",v.path?"; path="+v.path:"",v.domain?"; domain="+v.domain:"",v.secure?"; secure":""].join(""))}var w=q?undefined:{},s=document.cookie?document.cookie.split("; "):[],o=0,m=s.length;for(;o<m;o++){var n=s[o].split("="),j=g(n.shift()),k=n.join("=");if(q===j){w=e(k,p);break}if(!q&&(k=e(k))!==undefined){w[j]=k}}return w};b.defaults={};f.removeCookie=function(j,i){f.cookie(j,"",f.extend({},i,{expires:-1}));return !f.cookie(j)}}));