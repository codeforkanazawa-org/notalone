<?php
//ini_set( 'display_errors', "1" ); 
//ini_set( 'display_startup_errors', "1" ); 


include_once( "include.php");


user_header("csv database！ index");


?>

<a href="template.php?db=common_user">ユーザ管理</a><br>
<br />
<a href="template.php?db=fgroup">グループ管理</a><br>
	<div>
	<!-- group     -->
	　・<select name="groupDb" id="groupDb">
		<!-- option        -->
	</select>
	<input type="button" onClick="selectGroup()" value="グループ管理簿" />
	</div>
<br />
　・<a href="groupdatamente.php">一般市民投稿用データベースの整備</a><br />
<br />
<a href="template.php?db=report">レポート管理</a><br>
<br />
<br />

<!--
<a href="template.php?db=manager">管理担当</a><br>
<br />
<a href="template.php?db=lightdivi">設備の区分</a><br>
<br />
<a href="template.php?db=lightclass">灯りの種別</a><br>
<br />
<a href="template.php?db=facilities">灯りの位置</a><br>
-->

<br />
サーバーデータ・メンテ<br>
　・<a href="http://apli.nono1.jp/nightview/loadfiles.php?dir=files/apprise&ftype=text/xml&key=" target="nv_mente">評価結果（ｘｍｌ）</a><br>
　・<a href="http://apli.nono1.jp/nightview/loadfiles.php?dir=files/group&ftype=text/csv&key=" target="nv_mente">グループの灯り（ｃｓｖ）</a><br>
　・<a href="http://apli.nono1.jp/nightview/loadfiles.php?dir=log&ftype=text/csv&key=" target="nv_mente">ログ（ｃｓｖ）</a><br>
　・<a href="http://apli.nono1.jp/nightview/loadfiles.php?dir=uploads&ftype=image&key=" target="nv_mente">投稿画像</a><br>
<br>
<br>

<a href="../index.html">終了</a><br>
<br>
<br>



<!-- jquery ライブラリ -->
<script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>

<script type="text/javascript">

//グループ情報の読み出し
getGroupData('fgroup','no');

var GroupData   = [];
var GroupDataNO = 0;

function selectGroup(){
	var emt = document.getElementById("groupDb");

	for(var i = 0 ; i < emt.options.length ; i++){
		var eop = emt.options[i];

		if(eop.selected){ 
			var gname  = GroupData[eop.value]['gname'];
			var gtable = GroupData[eop.value]['gtable'];
			break;
		}		
	}
	//<a href="template.php?db=facilities">グループ管理簿</a><br>
	location.href = "template.php?db=facilities&sub=" + gtable + "&name=" + gname; 
}

function getGroupData(table,sort){
	//
	//とりあえずすべてのデータを取得する

	/*
         * Ajax通信メソッド
         * @param type      : HTTP通信の種類
         * @param url       : リクエスト送信先のURL
         * @param dataType  : データの種類
	 * @param data      : パラメーター
         */
        $.ajax({
            type: "POST",
            url: "../getdata.php",
            dataType: "json",
	    data : { "table" : table , "sort" : sort },
            /**
             * Ajax通信が成功した場合に呼び出されるメソッド
             */
            success: function(data, dataType) 
            {
                //結果が0件の場合
                if(data == null){
			alert('データが0件でした');
			return;
		}

		//返ってきたデータをGroupDataに格納
		GroupData = []; //初期化

		var keys = [];
		for(var i = 0 ; i < data.length ; i++){
			if( i == 0 ){
				// 配列のキーを取り出す
				var s = 0;
				for(keys[s++] in data[i]){};
			}

			GroupData[i] = new Array();
			for(var m = 0 ; m < keys.length ; m++){
				GroupData[i][keys[m]] = data[i][keys[m]];
			}
		}


		//******
		var len = GroupData.length;
		var emt = document.getElementById("groupDb");

		//alert(len + " : " + GroupData[0].ident + " / " + GroupData[0].gname);

		for(var i=0 ; i < len ; i++){
			if(GroupData[i].activ == 1){
				wopt = document.createElement('option');
				wopt.setAttribute('value' , i);
				$(wopt).append(GroupData[i].gname);
				$("#groupDb").append(wopt);
			}
		}
		//opt : oselected を設定するoptins	
		//emt.options[opt].selected = true;
		//******

            },
            /**
             * Ajax通信が失敗場合に呼び出されるメソッド
             */
            error: function(XMLHttpRequest, textStatus, errorThrown) 
            {
                //通常はここでtextStatusやerrorThrownの値を見て処理を切り分けるか、単純に通信に失敗した際の処理を記述します。

                //this;
                //thisは他のコールバック関数同様にAJAX通信時のオプションを示します。

                //エラーメッセージの表示
                alert('Error : ' + errorThrown);
            }
        });
}


function loadFile( dir , fname ){
	alert(dir + "/" + fname + "が選択されましたが、処理は設定されていません");
	//必要な処理を行うことが可能

}

</script>

</body>
</html>

