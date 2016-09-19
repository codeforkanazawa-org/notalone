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

    //var DRAGGABLE;	//ICONの移動設定　true 許可、false 非許可
    var DEFAULT_DRAGGABLE;	//ICONの移動設定　true 許可、false 非許可

    var DEFAULT_CENTERVIEW;
    var DEFAULT_ICONMOVE;
    var DEFAULT_LABELVIEW;
    var DEFAULT_LABELMOVE;

    var DRAGGABLE;	//ICONの移動設定　true 許可、false 非許可
    var CENTERVIEW;
    var ICONMOVE;
    var LABELVIEW;
    var LABELMOVE;

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

		if(parseInt(getSetting("DEFAULT_CENTERVIEW")) == 1){
			DEFAULT_CENTERVIEW = true;
		}else{
			DEFAULT_CENTERVIEW = false;
		}

		if(parseInt(getSetting("DEFAULT_DRAGGABLE")) == 1){
			DEFAULT_DRAGGABLE = true;
		}else{
			DEFAULT_DRAGGABLE = false;
		}

		if(parseInt(getSetting("DEFAULT_ICONMOVE")) == 1){
			DEFAULT_ICONMOVE = true;
		}else{
			DEFAULT_ICONMOVE = false;
		}

		if(parseInt(getSetting("DEFAULT_LABELVIEW")) == 1){
			DEFAULT_LABELVIEW = true;
		}else{
			DEFAULT_LABELVIEW = false;
		}

		if(parseInt(getSetting("DEFAULT_LABELMOVE")) == 1){
			DEFAULT_LABELMOVE = true;
		}else{
			DEFAULT_LABELMOVE = false;
		}
			
		//初期設定項目追加
		//localStorege は　"true" "false" 文字列として保存される
		var cv = localStorage.getItem("CenterView");
		if(cv == null){
	    		CENTERVIEW = DEFAULT_CENTERVIEW;			
		}else if(cv == "true"){
			CENTERVIEW = true;
		}else{
			CENTERVIEW = false;
		}

		cv = localStorage.getItem("IconDrag");
		if(cv == null){
	    		DRAGGABLE = DEFAULT_DRAGGABLE;			
		}else if(cv == "true"){
			DRAGGABLE = true;
		}else{
			DRAGGABLE = false;
		}

		cv = localStorage.getItem("IconMove");
		if(cv == null){
	    		ICONMOVE = DEFAULT_ICONMOVE;			
		}else if(cv == "true"){
			ICONMOVE = true;
		}else{
			ICONMOVE = false;
		}

		var cv = localStorage.getItem("LabelView");
		if(cv == null){
	    		LABELVIEW = DEFAULT_LABELVIEW;			
		}else if(cv == "true"){
			LABELVIEW = true;
		}else{
			LABELVIEW = false;
		}

		var cv = localStorage.getItem("LabelMove");
		if(cv == null){
	    		LABELMOVE = DEFAULT_LABELMOVE;			
		}else if(cv == "true"){
			LABELMOVE = true;
		}else{
			LABELMOVE = false;
		}
		//*************


		//setting を完全に読みだしてから、後処理を実施
		//カテゴリアイコンの読み出し
		var table = "../localhost/categoryicon.csv";
		readCategoryIcon(table , function(data){
			//alert(data.length);
		});

        	//getLocation();

		//**************
		locflg = localStorage.getItem("MapPosition");

		//ver002-004
		var label1 = localStorage.getItem("pre1setLabel");
		var label2 = localStorage.getItem("pre2setLabel");

		//for span
		$("#pre1setlabel").html(label1);
		$("#pre2setlabel").html(label2);

		//for input
		$("#pre1setinput").val(label1);
		$("#pre2setinput").val(label2);

		//**********

		if(locflg==null || locflg == "default"){
			if(locflg == null){
				localStorage.setItem("MapPosition", "default");
			}

			var lat = DEFAULT_LAT;
			var lng = DEFAULT_LNG;

			$('#loading').hide();
			showGoogleMap(lat , lng);

			//ver002-003
			$("#default").prop("checked",true);

		}else if(locflg == "gpsset"){
			getPosition(function(pos){
		        	var lat = pos['lat'];
	        		var lng = pos['lng'];

				$('#loading').hide();
				showGoogleMap(lat , lng);

				//ver002-003
				$("#gpsset").prop("checked",true);

			});

		}else if(locflg == "pre1set" || locflg == "pre2set"){
			var itemLat = locflg + "Lat";
			var itemLng = locflg + "Lng";

			var lat = localStorage.getItem(itemLat);
			var lng = localStorage.getItem(itemLng);

			$('#loading').hide();


			//設定マップの表示
			showGoogleMap(lat , lng);

			//ver002-003
			if(locflg == "pre1set"){
				$("#pre1set").prop("checked",true);
			}else{
				$("#pre2set").prop("checked",true);
			}

		}else{
			$("#default").prop("checked",true);
			alert("表示位置設定エラー(初期値にしました）");
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


        //現在地のマーカー
        var now_latlng = new google.maps.LatLng(initLat, initLng);
        now_marker = new google.maps.Marker({
            position:now_latlng,
            title: 'ドラッグで移動',
	    icon : "icons/blue-dot.png",
	    draggable : true,
            map: googlemap,
	    visible : CENTERVIEW,
	    zIndex : 10,
        });

	//現在地マーカーのinfoWindowとイベント
	var html = "【マップの中央マーカー】<br />";

	//******
	/*
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
	html += "<option value='default' " + def_select + ">初期設定</option>";
	html += "<option value='pre1set' " + pre1_select + ">お好み１</option>";
	html += "<option value='pre2set' " + pre2_select + ">お好み２</option>";
	html += "<option value='gpsset' "  + gps_select + ">現在位置</option>";
	html += "</select>";
	*/
	//******

	html += "（お好み位置の登録用）<br />";
	//html += "このマーカーをドラッグ、または<br />マップ上をダブルクリック<br />することで移動できます<br />";
	//html += "不用な場合は消してください<br />";
	//html += "<br /><br />";
	html += "<input type='button' onClick='now_markerHidden()' value='このマーカーを消す'>";

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
        //pushPins(googlemap);

	//選択したマップ番号を読み出し初期で表示する　ver002-01
	loadSetMap(function (){

		//中央マーカーの初期状態
		if(CENTERVIEW == true){
			$("#cMarkerView").prop("checked",true);
			now_markerVisible();
		}else{
			$("#cMarkerView").prop("checked",false);
			now_markerHidden();
		}

		//アイコンのドラッグの初期状態
		if(DRAGGABLE == true){
			$("#mapIconDrag").prop("checked",true);
			iconDragTrue();
		}else{
			$("#mapIconDrag").prop("checked",false);
			iconDragFalse();
		}

		//アイコン移動の初期状態
		if(ICONMOVE == true){
			$("#mapIconMove").prop("checked",true);
			iconMoveTrue();
		}else{
			$("#mapIconMove").prop("checked",false);
			iconMoveFalse();
		}

		//ラベル表示の初期状態
		if(LABELVIEW == true){
			$("#labelView").prop("checked",true);
			labelViewTrue();
		}else{
			$("#labelView").prop("checked",false);
			labelViewFalse();
		}

		//ラベル移動の初期状態
		if(LABELMOVE == true){
			$("#labelMove").prop("checked",true);
			labelMoveTrue();
		}else{
			$("#labelMove").prop("checked",false);
			labelMoveFalse();
		}

	});

    }


    //*****
    //マップの設定
    function selectPosition(){
	//var set = $("#mapPosition").val();
	var set = $("input[name=center]:checked").val();

	if(set == "pre1set" || set == "pre2set"){

		//*******
		var itemLat = set + "Lat";
		var itemLng = set + "Lng";
		var itemLabel = set + "Label";

		var nlat  = localStorage.getItem(itemLat);
		var nlng  = localStorage.getItem(itemLng);
		var label = localStorage.getItem(itemLabel);

		if(nlat == null || nlng == null){
			alert("「お好み」位置の登録がありません");
			$("#default").prop("checked",true);

			return;
		}else{
			if(confirm("中央マーカーの位置に移動しますか？")){
				setPosition(nlat,nlng);
			}
		}

	}else if(set == "gpsset"){
		var lat,lng;
		getPosition(function(pos){
			lat = pos['lat'];
	        	lng = pos['lng'];

			if(confirm("中央マーカーの位置に移動しますか？")){
				setPosition(lat,lng);
			}
		});

	}else{
		var lat = DEFAULT_LAT;
		var lng = DEFAULT_LNG;

		if(confirm("中央マーカーの位置に移動しますか？")){
			setPosition(lat,lng);
		}
	}

	localStorage.setItem("MapPosition", set);

	$("#mapPosition").val(set);


	//他の設定変更
	now_markerVisibleCheck();
	iconDragCheck();
	iconMoveCheck();
	labelViewCheck();
	labelMoveCheck();

	//alert("設定を更新しました");
    }

    function setPosition(lat,lng){
	var latlng = new google.maps.LatLng(lat , lng);
	now_marker.setPosition(latlng);
	googlemap.setCenter(latlng);
    }
    //*****

    //中央マーカー設定
    function now_markerVisibleCheck(){
	if($("#cMarkerView").prop("checked")){
		now_markerVisible();
		//localStorage.setItem("CenterView", true);
	}else{
		now_markerHidden();
		//localStorage.setItem("CenterView", false);
	}
    }

    //中央マーカーを消す
    function now_markerHidden(){
	now_infowindow.close();
	now_marker.setVisible( false ) ;
	$("#cMarkerView").prop("checked",false);
	localStorage.setItem("CenterView", false);
	CENTERVIEW = false;
    }

    //中央マーカーを表示する
    function now_markerVisible(){
	now_infowindow.close();
	now_marker.setVisible( true ) ;
	$("#cMarkerView").prop("checked",true);
	localStorage.setItem("CenterView", true);
	CENTERVIEW = true;
    }

    //アイコンのドラッグの設定
    function iconDragCheck(){
	if($("#mapIconDrag").prop("checked")){
		iconDragTrue();
	}else{
		iconDragFalse();
	}
    }

    //アイコンをドラッグしない
    function iconDragFalse(){
	$("#mapIconDrag").prop("checked",false);
	for(var i = 0 ; i < markersArray.length ; i++){
		markersArray[i].setDraggable( false );
	}
	localStorage.setItem("IconDrag", false);
	DRAGGABLE = false;
    }

    //アイコンをドラッグする
    function iconDragTrue(){
	$("#mapIconDrag").prop("checked",true);
	for(var i = 0 ; i < markersArray.length ; i++){
		markersArray[i].setDraggable( true );
	}
	localStorage.setItem("IconDrag", true);
	DRAGGABLE = true;
    }


    //アイコンの移動設定
    function iconMoveCheck(){
	if($("#mapIconMove").prop("checked")){
		iconMoveTrue();
	}else{
		iconMoveFalse();
	}
    }

    //アイコンを移動しない
    function iconMoveFalse(){
	$("#mapIconMove").prop("checked",false);

	/* Icon のドラッグ可否は、DRAGGABLE　で決める
	for(var i = 0 ; i < markersArray.length ; i++){
		markersArray[i].setDraggable( false );
	}
	*/	

	localStorage.setItem("IconMove", false);
	ICONMOVE = false;
    }

    //アイコンを移動する
    function iconMoveTrue(){
	$("#mapIconMove").prop("checked",true);

	/* Icon のドラッグ可否は、DRAGGABLE　で決める
	for(var i = 0 ; i < markersArray.length ; i++){
		markersArray[i].setDraggable( true );
	}
	*/

	localStorage.setItem("IconMove", true);
	ICONMOVE = true;
    }

    //ラベルの表示設定
    function labelViewCheck(){
	if($("#labelView").prop("checked")){
		labelViewTrue();
	}else{
		labelViewFalse();
	}
    }
    //ラベルを表示しない
    function labelViewFalse(){
	$("#labelView").prop("checked",false);
	hiddenStrings();

	localStorage.setItem("LabelView", false);
	LABELVIEW = false;
    }
    //ラベルを表示する
    function labelViewTrue(){
	$("#labelView").prop("checked",true);
	visibleStrings();

	localStorage.setItem("LabelView", true);
	LABELVIEW = true;
    }

    //ラベルの移動設定
    function labelMoveCheck(){
	if($("#labelMove").prop("checked")){
		labelMoveTrue();
	}else{
		labelMoveFalse();
	}
    }
    //ラベルを移動しない
    function labelMoveFalse(){
	$("#labelMove").prop("checked",false);
	/*
	for(var i = 0 ; i < stringsArray.length ; i++){
		stringsArray[i].setDraggable( false );
	}
	*/
	localStorage.setItem("LabelMove", false);
	LABELMOVE = false;
    }
    //ラベルを移動する
    function labelMoveTrue(){
	$("#labelMove").prop("checked",true);
	/*
	for(var i = 0 ; i < stringsArray.length ; i++){
		stringsArray[i].setDraggable( true );
	}
	*/
	localStorage.setItem("LabelMove", true);
	LABELMOVE = true;
    }


    //現在地の取得
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


//ドラッグstart時の位置記憶用
var dragstartIconNo;
var dragstartIconLatLng;
var dragstartIconLat;
var dragstartIconLng;

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
	    //label : data[_Category],
	    icon  : icon,
	    draggable : DRAGGABLE,
	    //draggable : ICONMOVE,
	    //draggable : true,
		
            map: map
        });


	//*****************
	//文字列の表示
	var labelCheck = "#mapName" + mapNo + "_label";
	if($(labelCheck).prop("checked")){
		//個別マップのラベル非表示（マーカーのタイトルを消す）
		marker.setTitle("");
		var txt = "";
	}else{
		var txt = data[_Name];
	}


	//var string_marker = new StringMarker( map, lat, lng , txt);

	//var visi = $("#labelView").prop("checked");
	var visi = LABELVIEW;	//同期通信化することで、初期の　check が遅くなる
	var string_marker = new StringMarker( map, lat, lng , txt , visi);

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
	
	var markerIndex = markersArray.length - 1;

	//マーカーのドラッグstart検出時の動作
        google.maps.event.addListener(marker, 'dragstart', function() {
		//現在のpositionを記憶
		dragstartIconNo     = markerIndex;
		dragstartIconLatLng = marker.getPosition();
		dragstartIconLat = marker.position.lat();
		dragstartIconLng = marker.position.lng();
	});

	//マーカーのドラッグ中の動作
        google.maps.event.addListener(marker, 'drag', function() {
		//現在のpositionを記憶
		if(LABELMOVE == true){
			var dno  = markerIndex;
			var dlat = marker.position.lat();
			var dlng = marker.position.lng();

			moveStrings(dno , dlat , dlng); 
			//alert(marker.position.lat());
		}
	});

	//マーカーのドラッグend検出時の動作
        google.maps.event.addListener(marker, 'dragend', function() {
/*		if(LABELMOVE == false){
			//文字列の位置を元に戻す
			var dno  = dragstartIconNo;
			var dlat = dragstartIconLat;
			var dlng = dragstartIconLng;

			moveStrings(dno , dlat , dlng); 
			//alert(marker.position.lat());
		}
*/
		if(ICONMOVE == false){
			//元の座標に戻す
			//var dno  = markerIndex;
			marker.setPosition(dragstartIconLatLng);			
		}
	});

/*
	//マーカーのドラッグ検出時の動作
        google.maps.event.addListener(marker, 'dragend', function() {

		if(LABELMOVE == true){
			//文字列の位置を移動する
			var dno  = markerIndex;
			var dlat = marker.position.lat();
			var dlng = marker.position.lng();

			moveStrings(dno , dlat , dlng); 
			//alert(marker.position.lat());
		}
	});
*/



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
		cache: false,
		async:false
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
	stringsArray.forEach(function (marker, idx){
		marker.setMap(null);
	});
	stringsArray = new Array();
}

//hidden
function hiddenStrings(){
	stringsArray.forEach(function (marker, idx){
		marker.hide();
	});
	//stringsArray = new Array();
}

//visible
function visibleStrings(){
	stringsArray.forEach(function (marker, idx){
		marker.show();
	});
	//stringsArray = new Array();
}

//マーカーのドラッグにあわせ文字列を移動する
function moveStrings(dno , dlat , dlng){
	stringsArray[dno].move(dlat , dlng);
}


/* StringMarkerのコンストラクタ。緯度、経度をメンバ変数に設定する。 */
function StringMarker(map, lat, lng , txt , visi) {
        this.lat_  = lat;
        this.lng_  = lng;
	this.text_ = txt;

	this.visi_ = visi;

	this.setMap(map);
}

/** google.maps.OverlayViewを継承 */
StringMarker.prototype = new google.maps.OverlayView();

/** drawの実装。hello, worldと書いたdiv要素を生成 */
StringMarker.prototype.draw = function() {
        if (!this.div_) {
	   //文字数の確認
	   var textSize = this.text_.length;
	   var fontSize = 12;
	   var divSize  = textSize * fontSize; 

          // 出力したい要素生成
          this.div_ = document.createElement( "div" );
          this.div_.style.position = "absolute";

          //this.div_.style.fontSize = "120%";
	  this.div_.style.fontSize = fontSize + "px";
          //this.div_.style.width = "100%";
	  //this.div_.style.width  = "100px";
	  this.div_.style.width  = divSize + "px";

	  this.div_.style.height = "20px"; 
	  this.div_.style.overflow = "hidden";
	  this.div_.style.color = "black";

	  //this.div_.draggable="true";		//HTML5で可能　Map上では動きが悪い


          //this.div_.style.textShadow = "1px 1px 1px #00F,-1px 1px 1px #00F,1px -1px 1px #00F,-1px -1px 1px #00F";

	  if(this.visi_ == true){
	  	this.div_.style.visibility = "visible";
	  }else{
	  	this.div_.style.visibility = "hidden";		
	  } 

          //this.div_.innerHTML = "hello, world";
          this.div_.innerHTML = this.text_;

          // 要素を追加する子を取得
          var panes = this.getPanes();

          // 要素追加
          //panes.overlayLayer.appendChild( this.div_ );	//ペイン１
          panes.overlayImage.appendChild( this.div_ );		//ペイン３　ここ以上でイベント検出可能
          //panes.floatPane.appendChild( this.div_ );		//ペイン６（最上部）
        }

        // 緯度、経度の情報を、Pixel（google.maps.Point）に変換
        var point = this.getProjection().fromLatLngToDivPixel( new google.maps.LatLng( this.lat_, this.lng_ ) );

        // 取得したPixel情報の座標に、要素の位置を設定
        // これで35.5, 140.0の位置を左上の座標とする位置に要素が設定される
        this.div_.style.left = point.x + 'px';
        this.div_.style.top = point.y + 'px';

	//クリックの実験
	google.maps.event.addDomListener(this.div_,"click", function(){
		//alert("クリックしました：" + this.innerHTML);
		this.style.visibility = "hidden";
	});

}

/* 削除処理の実装 */
StringMarker.prototype.remove = function() {
        if (this.div_) {
          this.div_.parentNode.removeChild(this.div_);
          this.div_ = null;
        }
}

/* ver2. 10-00 表示・非表示 */
StringMarker.prototype.hide = function() {
  	if (this.div_) {
    		this.div_.style.visibility = 'hidden';
		//alert("hidden");
  	}
}
StringMarker.prototype.show = function() {
  	if (this.div_) {
    		this.div_.style.visibility = 'visible';
		//alert("visible");
  	}
}

StringMarker.prototype.move = function(lat,lng) {
  	if (this.div_) {
    		this.lat_ = lat;
		this.lng_ = lng;

	        // 緯度、経度の情報を、Pixel（google.maps.Point）に変換
        	var point = this.getProjection().fromLatLngToDivPixel( new google.maps.LatLng( this.lat_, this.lng_ ) );

        	// 取得したPixel情報の座標に、要素の位置を設定
	        this.div_.style.left = point.x + 'px';
        	this.div_.style.top = point.y + 'px';
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
