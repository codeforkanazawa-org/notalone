//$(function(){
//    initlaize();

    var googlemap;
    var now_marker;
    var now_infowindow;

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

        	getLocation();
	});
    }

    function showGoogleMap(initLat, initLng) {
        var latlng = new google.maps.LatLng(initLat, initLng);
        var opts = {
            //zoom: 16,
            //zoom: 14,
	    zoom : DEFAULT_ZOOM,
	    center: latlng,
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
	    mapTypeControl: true
        };
        //var map = new google.maps.Map(document.getElementById("map_canvas"), opts);
        googlemap = new google.maps.Map(document.getElementById("map_canvas"), opts);

        //現在地のピン
        var now_latlng = new google.maps.LatLng(initLat, initLng);
        now_marker = new google.maps.Marker({
            position:now_latlng,
            title: 'ドラッグで移動',
	    icon : "icons/blue-dot.png",
	    draggable : true,
            map: googlemap,
        });

	//現在地ピンのinfoWindowとイベント
	var html = "このピンは目印です。<br />ドラッグで移動できます<br />";
	html += "<input type='button' onClick='now_markerHidden()' value='このピンを消す'>";

        now_infowindow = new google.maps.InfoWindow();
        now_infowindow.setContent(html);

        google.maps.event.addListener(now_marker, 'click', function() {
            now_infowindow.open(googlemap, now_marker);
        });


	//各種マップを表示する
        pushPins(googlemap);

    }

    //現在地ピンを消す
    function now_markerHidden(){
	now_infowindow.close();
	now_marker.setVisible( false ) ;
    }


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

    function errorCallback() {
        $('#loading').hide();

        //位置情報取得不可の場合も現在地情報を金沢駅に補正
        //showGoogleMap(KANAZAWA_STATION_LAT, KANAZAWA_STATION_LNG);
        showGoogleMap(DEFAULT_LAT, DEFAULT_LNG);
    }

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

	if(_DefPin == ''){alert(data.length);
		var icon = "icons/red_pin.png";
	}else{
		var icon = _DefPin;
	}
	
	//category icon のチェック ******
	if(_Category != ""){

		var level  = map.getZoom();

		var cat    = data[_Category].trim();

		var to_dir = "../";	//subからの相対ディレクトリ

		for(var i = 0 ; i < iconArray.length ; i++){
			if(cat == iconArray[i][Cat_name]){
				if(iconArray[i][Big_icon] != "" && level >= iconArray[i][Zoom_level]){
					icon  = to_dir + iconArray[i][Big_dir];
					icon += "/"    + iconArray[i][Big_icon];
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


	//マップごとのinfoWindowを整形
	var html = mapMakeInfo(mapNo,data);
        var infowindow = new google.maps.InfoWindow();
        infowindow.setContent(html);

        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });

	markersArray.push(marker);
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

