//$(function(){
//    initlaize();

    var googlemap;
    var now_marker;
    var now_infowindow;
    var carrent_infowindow;

    var locflg;

    //var KANAZAWA_STATION_LAT = 36.578273;
    //var KANAZAWA_STATION_LNG = 136.647763;
    //輪島市役所
    //var DEFAULT_LAT = 37.390556;
    //var DEFAULT_LNG = 136.899167;
    //var FARAWAY_DISTANCE     = 0.8;

    var DEFAULT_LAT;
    var DEFAULT_LNG;
    var DEFAULT_ZOOM;
    var DRAGGABLE;

    function initlaize() {

	//setting データの読み出し
	var table = "../localhost/setting.csv";
	readSettingData(table , function(data){
		DEFAULT_LAT = parseFloat(getSetting("DEFAULT_LAT"));
		DEFAULT_LNG = parseFloat(getSetting("DEFAULT_LNG"));
		DEFAULT_ZOOM = parseInt(getSetting("DEFAULT_ZOOM"));

		if(parseInt(getSetting("DRAGGABLE")) == 1){
			DRAGGABLE = true;
		}else{
			DRAGGABLE = false;
		}


		//setting を完全に読みだしてから、後処理を実施
		//カテゴリアイコンの読み出し
		var table = "../localhost/categoryicon.csv";
		readCategoryIcon(table , function(data){
			//alert(data.length);
		});

        	//getLocation();

		//**************
		locflg = localStorage.getItem("MapPosition");

		if(locflg==null || locflg == "default"){
			if(locflg == null){
				localStorage.setItem("MapPosition", "default");
			}

			var lat = DEFAULT_LAT;
			var lng = DEFAULT_LNG;

			$('#loading').hide();
			showGoogleMap(lat , lng);

		}else if(locflg == "gpsset"){
			getPosition(function(pos){
		        	var lat = pos['lat'];
	        		var lng = pos['lng'];

				$('#loading').hide();
				showGoogleMap(lat , lng);
			});

		}else if(locflg == "pre1set" || locflg == "pre2set"){
			var itemLat = locflg + "Lat";
			var itemLng = locflg + "Lng";

			var lat = localStorage.getItem(itemLat);
			var lng = localStorage.getItem(itemLng);

			$('#loading').hide();
			showGoogleMap(lat , lng);

		}else{
			alert("表示位置設定エラー");
		}

		//**************

	});
    }

    function showGoogleMap(initLat, initLng) {
        var latlng = new google.maps.LatLng(initLat, initLng);
        var opts = {
            //zoom: 16,
            //zoom: 14,
	    zoom : DEFAULT_ZOOM,
	    center: latlng,

	    //mapTypeId: google.maps.MapTypeId.ROADMAP,
	    mapTypeControlOptions: {
		mapTypeIds: ['noText',google.maps.MapTypeId.ROADMAP,google.maps.MapTypeId.HYBRID]
	    },

	    mapTypeControl: true,
	    disableDoubleClickZoom: true	//ダブルクリックズームを無効化
        };
        //var map = new google.maps.Map(document.getElementById("map_canvas"), opts);
        googlemap = new google.maps.Map(document.getElementById("map_canvas"), opts);


	//***********
	/* スタイル付き地図
	var styleOptions = [{
		featureType: 'all',
  		elementType: 'labels',
  		stylers: [{ visibility: 'off' }]
	}];
	var lopanType = new google.maps.StyledMapType(styleOptions);
	googlemap.mapTypes.set('noText', lopanType);
	//googlemap.setMapTypeId('noText');
	*/

	//***********
	var styleOptions = [{
		"elementType": "labels.text",
		"stylers": [{ "visibility": "off" }]
	}];

	var styledMapOptions = { name: '簡素' }
	var lopanType = new google.maps.StyledMapType(styleOptions, styledMapOptions);
	googlemap.mapTypes.set('noText', lopanType);
	googlemap.setMapTypeId('noText');
	//***********


        //現在地のピン
        var now_latlng = new google.maps.LatLng(initLat, initLng);
        now_marker = new google.maps.Marker({
            position:now_latlng,
            title: 'ドラッグで移動',
	    icon : "icons/blue-dot.png",
	    draggable : true,
            map: googlemap,

	    zIndex : 10,
        });

	//現在地ピンのinfoWindowとイベント
	var html = "【ピン位置の設定変更】<br />";

	//******
	var def_select = "";
	var pre1_select = "";
	var pre2_select = "";
	var gps_select = "";

	if(locflg == "pre1set"){
		pre1_select = "selected";
	}else if(locflg == "pre2set"){
		pre2_select = "selected";
	}else if(locflg == "gpsset"){
		gps_select = "selected";
	}else{
		def_select = "selected";
	}

	html += "<select id='mapPosition' onChange='selectPosition()'>";
	html += "<option value='default' " + def_select + ">初期設定の位置</option>";
	html += "<option value='pre1set' " + pre1_select + ">お好み設定１</option>";
	html += "<option value='pre2set' " + pre2_select + ">お好み設定２</option>";
	html += "<option value='gpsset' "  + gps_select + ">現在位置を取得</option>";
	html += "</select>";
	//******


	html += "<br /><br />ドラッグまたはダブルクリックで<br />一時的に移動もできます<br />";
	html += "<input type='button' onClick='now_markerHidden()' value='このピンを消す'>";

        now_infowindow = new google.maps.InfoWindow();
        now_infowindow.setContent(html);

        google.maps.event.addListener(now_marker, 'click', function() {
            now_infowindow.open(googlemap, now_marker);
        });

        google.maps.event.addListener(googlemap, 'dblclick', function(ev) {
		var latlng = ev.latLng;
		now_marker.setPosition(latlng);
		//googlemap.setCenter(latlng);
        });

	google.maps.event.addListener(googlemap, 'zoom_changed', function() {
		var zoom  = googlemap.getZoom();
		var nicon = "";

		var cat  = categorysArray;
    		var len  = cat.length;
		for(var i = 0 ; i < len ; i++){
			if(cat[i]['useCategory'] == 1){
				if(cat[i]['level'] != zoom){
					if(cat[i]['chglv'] <= zoom){
						nicon = cat[i]['bicon'];
					}else{
						nicon = cat[i]['dicon'];
					}
				}

				//iconの置き換え
				if(nicon != cat[i]['icon']){
					markersArray[i].setMap(null);
					markersArray[i]['icon'] = nicon;
					markersArray[i].setMap(googlemap);

					//cat[i]['level'] = zoom;
					cat[i]['icon']  = nicon;
				}
			}
			cat[i]['level'] = zoom;
		}


/*	var useCategory = 0;
	var cat    = "";
	var level  = map.getZoom();
	var chglv  = 0;
	var dicon  = icon;	//Default Icon
	var bicon  = "";
*/
  	});

	//各種マップを表示する
        pushPins(googlemap);

    }

    //*****
    function selectPosition(){
	var set = $("#mapPosition").val();

	if(set == "pre1set" || set == "pre2set"){

		//*******
		var itemLat = set + "Lat";
		var itemLng = set + "Lng";

		var nlat = localStorage.getItem(itemLat);
		var nlng = localStorage.getItem(itemLng);
		if(nlat == null || nlng == null){
			var pos = now_marker.getPosition();
			var lat = pos.lat();
			var lng = pos.lng();

			localStorage.setItem(itemLat ,lat);
			localStorage.setItem(itemLng ,lng);
		}else{
			if(confirm("このピン位置に変更しますか？")){
				var pos = now_marker.getPosition();
				var lat = pos.lat();
				var lng = pos.lng();

				localStorage.setItem(itemLat , lat);
				localStorage.setItem(itemLng , lng);

			}else{
				var lat = nlat;
				var lng = nlng;
			}
		}
		//*******

		setPosition(lat,lng);

	}else if(set == "gpsset"){
		var lat,lng;
		getPosition(function(pos){
			lat = pos['lat'];
	        	lng = pos['lng'];

			setPosition(lat,lng);
		});

	}else{
		var lat = DEFAULT_LAT;
		var lng = DEFAULT_LNG;

		setPosition(lat,lng);
	}

	localStorage.setItem("MapPosition", set);
    }

    function setPosition(lat,lng){
	var latlng = new google.maps.LatLng(lat , lng);
	now_marker.setPosition(latlng);
	googlemap.setCenter(latlng);
    }
    //*****

    //現在地ピンを消す
    function now_markerHidden(){
	now_infowindow.close();
	now_marker.setVisible( false ) ;
    }

    function getPosition(cb) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
		function(pos){
        		var rlat = pos.coords.latitude;
        		var rlng = pos.coords.longitude;
			return cb({lat: rlat , lng : rlng});
		} ,
		errorCallback);
        } else {
            errorCallback();
        }
    }

    //未使用 ******
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback,errorCallback);
        } else {
            errorCallback();
        }
    }

    function successCallback(pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;
	/* FARAWAY? 
        //石川から大きく離れた場所の場合、現在地情報を金沢駅に設定
        if (getDistanceFromKanazawaStation(lat, lng) > FARAWAY_DISTANCE) {
            //lat = KANAZAWA_STATION_LAT;
            //lng = KANAZAWA_STATION_LNG;
            lat = DEFAULT_LAT;
            lng = DEFAULT_LNG;
        }
	*/

        $('#loading').hide();
        showGoogleMap(lat, lng);
    }
    //**********


    function errorCallback(error) {
        $('#loading').hide();

	var err_msg = "";
	switch(error.code){
		case 1:
			err_msg = "位置情報の利用が許可されていません";
			break;
		case 2:
			err_msg = "デバイスの位置が判定できません";
			break;
		case 3:
			err_msg = "タイムアウトしました";
			break;
	}

	alert(err_msg);

        //位置情報取得不可の場合も現在地情報を金沢駅に補正
        //showGoogleMap(KANAZAWA_STATION_LAT, KANAZAWA_STATION_LNG);
        showGoogleMap(DEFAULT_LAT, DEFAULT_LNG);
    }

    /*
    function errorCallback() {
        $('#loading').hide();

        //位置情報取得不可の場合も現在地情報を金沢駅に補正
        //showGoogleMap(KANAZAWA_STATION_LAT, KANAZAWA_STATION_LNG);
        showGoogleMap(DEFAULT_LAT, DEFAULT_LNG);
    }
    */

    function getDistance(x1, x2, y1, y2) {
        var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
        return distance;
    }

    function getDistanceFromKanazawaStation(lat, lng)
    {
        //var distance = getDistance(lat, KANAZAWA_STATION_LAT, lng, KANAZAWA_STATION_LNG);
        var distance = getDistance(lat, DEFAULT_LAT, lng, DEFAULT_LNG);
        return distance;
    }

    //var mapNo = 1;
    function pushPins(map)
    {

	//select テーブルで切り替え
	mapConvertTable(mapNo);

	//配列の初期化
	mapPinsArray = new Array();

        readMapData(_MapTable , function(data){
            for (i in data){
                pushPin(map, data[i]);
            }
	    //alert(mapPinsArray.length);
        });
    }


    function pushPin(map, data) {
        //データ対応のピン
        var lat = data[_Lat];
        var lng = data[_Lng];

	if(_DefPin == ''){
		alert(data.length);
		var icon = "icons/red_pin.png";
	}else{
		var icon = _DefPin;
	}
	
	//category icon のチェック ******
	var useCategory = 0;
	var cat    = "";
	var level  = map.getZoom();
	var chglv  = 0;
	var dicon  = icon;	//Default Icon
	var bicon  = "";

	if(_Category != ""){

		//var level  = map.getZoom();
		cat    = data[_Category].trim();

		var to_dir = "../";	//subからの相対ディレクトリ

		for(var i = 0 ; i < iconArray.length ; i++){
			/*
			if(cat == iconArray[i][Cat_name]){
				if(iconArray[i][Big_icon] != "" && level >= iconArray[i][Zoom_level]){
					icon  = to_dir + iconArray[i][Big_dir];
					icon += "/"    + iconArray[i][Big_icon];
				}
				break;
			}
			*/

			if(cat == iconArray[i][Cat_name]){

				useCategory = 1;

				if(iconArray[i][Big_icon] != ""){
					bicon  = to_dir + iconArray[i][Big_dir];
					bicon += "/"    + iconArray[i][Big_icon];
				}

				if(iconArray[i][Def_icon] != ""){
					dicon  = to_dir + iconArray[i][Def_dir];
					dicon += "/"    + iconArray[i][Def_icon];
				}

				chglv = iconArray[i][Zoom_level];

				if( level >= chglv){
					icon  = bicon;
				}else{
					icon  = dicon;
				}

				break;
			}
		}
	}
	//******************************

        var latlng = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({
            position:latlng,
	    title : data[_Name],
	    icon  : icon,
	    draggable : DRAGGABLE,

            map: map
        });

	//*****************
	//文字列の表示試験
	var txt = data[_Name];
	var string_marker = new StringMarker( map, lat, lng , txt);

	stringsArray.push(string_marker);
	//*****************


	//マップごとのinfoWindowを整形
	var html = mapMakeInfo(mapNo,data);
        var infowindow = new google.maps.InfoWindow();
        infowindow.setContent(html);

        google.maps.event.addListener(marker, 'click', function() {
	    if(carrent_infowindow){
		carrent_infowindow.close();
	    }
	    infowindow.open(map, marker);
	    carrent_infowindow = infowindow;
        });

	markersArray.push(marker);

	//categorysArray
	var catdata = new Object({
		useCategory : useCategory,
		cat : cat,
		level : level,
		chglv : chglv,
		dicon : dicon,
		bicon : bicon,
		icon  : icon
	});

	categorysArray.push(catdata);

    }


    function csvToArray(filename, callback) {
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
            callback(ret);
        });
    }


    function readMapData(table , callback){
	csvToArray( table , function(data) {

		mapPinsArray = new Array();

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			mapPinsArray.push(rensou);
		}
		callback(mapPinsArray);
	});
    }

    //cagegoryicon ファイルを読み込み iconArrayに保存
    function readCategoryIcon(table , callback){
	csvToArray( table , function(data) {

		iconArray = new Array();

		//1行目をフィールド名として扱い連想配列にする
		for(var i = 1 ; i < data.length ; i++){
			var rensou = new Object();
			for(var s = 0; s < data[i].length ; s++){
				rensou[data[0][s]] = data[i][s]; 
			}
			iconArray.push(rensou);
		}
		callback(iconArray);
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

//});


//**********************
//String情報保存用の配列
//var stringsArray = new google.maps.MVCArray();
var stringsArray = new Array();
function clearStrings(){
	stringsArray.forEach(function (marker, idx) { marker.setMap(null); });
	stringsArray = new Array();
}



     /* StringMarkerのコンストラクタ。緯度、軽度をメンバ変数に設定する。 */
      function StringMarker(map, lat, lng , txt) {
        this.lat_  = lat;
        this.lng_  = lng;
	this.text_ = txt;
        this.setMap(map);
      }

      /** google.maps.OverlayViewを継承 */
      StringMarker.prototype = new google.maps.OverlayView();

      /** drawの実装。hello, worldと書いたdiv要素を生成 */
      StringMarker.prototype.draw = function() {
        if (!this.div_) {
          // 出力したい要素生成
          this.div_ = document.createElement( "div" );
          this.div_.style.position = "absolute";
          this.div_.style.fontSize = "100%";
          this.div_.style.width = "100%";

          //this.div_.innerHTML = "hello, world";
          this.div_.innerHTML = this.text_;

          // 要素を追加する子を取得
          var panes = this.getPanes();
          // 要素追加
          panes.overlayLayer.appendChild( this.div_ );
        }

        // 緯度、軽度の情報を、Pixel（google.maps.Point）に変換
        var point = this.getProjection().fromLatLngToDivPixel( new google.maps.LatLng( this.lat_, this.lng_ ) );

        // 取得したPixel情報の座標に、要素の位置を設定
        // これで35.5, 140.0の位置を左上の座標とする位置に要素が設定される
        this.div_.style.left = point.x + 'px';
        this.div_.style.top = point.y + 'px';
      }


      /* 削除処理の実装 */
      StringMarker.prototype.remove = function() {
        if (this.div_) {
          this.div_.parentNode.removeChild(this.div_);
          this.div_ = null;
        }
      }


/*
var setHTML = "";
     //* HelloMarkerのコンストラクタ。緯度、軽度をメンバ変数に設定する。
      function HelloMarker(map, lat, lng ) {
        this.lat_ = lat;
        this.lng_ = lng;
        this.setMap(map);
      }

      //** google.maps.OverlayViewを継承 
      HelloMarker.prototype = new google.maps.OverlayView();

      //** drawの実装。hello, worldと書いたdiv要素を生成 
      HelloMarker.prototype.draw = function() {
        // 何度も呼ばれる可能性があるので、div_が未設定の場合のみ要素生成
        if (!this.div_) {
          // 出力したい要素生成
          this.div_ = document.createElement( "div" );
          this.div_.style.position = "absolute";
          this.div_.style.fontSize = "80%";
          //this.div_.innerHTML = "hello, world";
          this.div_.innerHTML = setHTML;

          // 要素を追加する子を取得
          var panes = this.getPanes();
          // 要素追加
          panes.overlayLayer.appendChild( this.div_ );
        }

        // 緯度、軽度の情報を、Pixel（google.maps.Point）に変換
        var point = this.getProjection().fromLatLngToDivPixel( new google.maps.LatLng( this.lat_, this.lng_ ) );

        // 取得したPixel情報の座標に、要素の位置を設定
        // これで35.5, 140.0の位置を左上の座標とする位置に要素が設定される
        this.div_.style.left = point.x + 'px';
        this.div_.style.top = point.y + 'px';
      }

      //* 削除処理の実装 
      HelloMarker.prototype.remove = function() {
        if (this.div_) {
          this.div_.parentNode.removeChild(this.div_);
          this.div_ = null;
        }
      }
*/
