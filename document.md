
## プログラムの構成
このアプリの動作には、原則としてphpが動作するサーバー環境が必要です。  
イベント、マップ、相談、それぞれのデータは、csv形式で構成しています。  
それぞれのcsvデータは、ブラウザで直接編集するか、またはローカルで作成してアップロードします。  
イベント、マップ、相談のデータ閲覧は、html及びjavascriptのみで構成していますので、ローカルPCでも動作します。

---
###### 以下ファイル構成における[name]は、フォルダ名を示す。  フォルダ名のみ記述している場合は、配下のファイル名等を省略しています。

#### データ閲覧部  
  
* index.html --- トップページ  
* events.html --- インベント表示 
* [map] --- マップ
	* map.html --- マップ表示
   * pcwindow.html --- パソコン用表示
   * [js]
   		* main.js
   		* ratichet.js 
   * [css]
   		* main.css
   		* ratchet-theme-android.css
   		* ratchet-theme-android.min.css
   		* ratchet-theme-ios.css
   		* ratchet-css
   		* ratchet.min.css 
   * [icons] --- マップ用アイコン 
* inquiry.html --- 相談  
* about.html --- ノットアローンについて 
* [js] --- javascriptファイル用
	* notalone.js --- メインスクリプト
	* setting.js --- システム情報の読み出し
	* googlemap_api.js --- googleコマンドラインの設定
	* csvdatabase2.jp --- csvデータの操作（データ管理）
	* sha256.js --- 暗号化
	* [fullcalendar-2.4.0] --- フルカレンダー
	* [jquery-ui-1.11.4.custom]
	* jquery-1.11.3.min.js
* [css] --- スタイルシート
	* notalone.css
	* csvdatabase2.css
* [images] --- シンボル、メニュー画像等
* apple-touch-icon-114x114.png --- ホームアイコン（小）
* apple-touch-icon-120x120.png --- ホームアイコン（大） 

#### データ部  
* [events] --- 月別イベント集計用フォルダ
	* yyyymm.csv --- 年月別の集計データファイル 
* [inquiry] --- 相談用フォルダ
	* inquiry.csv --- 相談データファイル 
* [uploads] --- アップロード用フォルダ
	* [events] --- 個別のイベントファイル
	* [mapinfo] --- 個別のマップファイル
	* [images] --- 各種画像ファイル
* [localhost] --- システム用フォルダ
	* area.csv --- イベント開催地域の情報
	* location.csv --- イベント開催場所情報
	* target.csv --- イベント区分情報
	* eventfields.csv --- イベント集計ファイルの構造定義
	* categoryicon.csv --- マップ用アイコン情報
	* setting.csv --- システム設定情報
	* user.csv --- ユーザー登録情報
	  

#### データ管理部(php)   
* [admin] --- ｃｓｖデータ編集スクリプト 
	* index.php --- 管理部トップページ
	* include.php --- 管理部システム設定情報
	* log_in_check.php --- ユーザーログインのチェック
	* log_in.php --- ユーザーログイン
	* log_out.php --- ユーザーログアウト
	* user_login_check.php --- js側でのユーザーログインチェック
	* user_logout.php --- js側でのユーザーログアウト
	* event_total_exec.php --- イベントファイルの集計  
	* savefile.php --- データファイルの保存
	* renamefile.php --- データファイルの名前変更
	* deletefile.php --- データファイルの削除  
	* open_mapinfo.php --- マップのオープンデータページ
	* common.js --- csvデータの読み込み
	* cont_event2.php --- イベントデータ用
	* cont_events_admin2.php --- イベント集計データ用
	* cont_area2.php --- イベント開催地域データ用
	* cont_location2.php --- イベント開催場所データ用
	* cont_target2.php --- イベント区分データ用
	* cont_inquiry2.php --- 相談データ用
	* cont_mapinfo2.php --- マップデータ用
	* cont_categoryicon2.php --- マップアイコンデータ用
	* cont_setting2.php --- システム設定データ用
	* cont_user2.php --- ユーザー登録データ用
	* cont_csvdatacoordinator2.php --- csvデータフィールド編集用  
* [include] --- php拡張スクリプト
	* json.php
* event_total.php --- 個別イベントデータの集計 
* loadfiles.php ---　csvファイルの選択、アップロード等   
* download.php --- csvファイルのダウンロード 
* opendata.php --- opendataファイルの選択、ダウンロード等 

## 組み込みしている外部ライブラリ等
#### javascriptのページ全般
* [[jquery-ui-1.11.4.custom]](http://jquery.org/license)
* [jquery-1.11.3.min.js](http://jquery.org/license)

#### イベントページのカレンダー
* [[fullcalendar-2.4.0]](https://fullcalendar.io/license/)  

#### マップページのスタイル設定  
* [ratichet.js](https://github.com/twbs/ratchet/blob/master/LICENSE)
* ratchet-theme-android.css
* ratchet-theme-android.min.css
* ratchet-theme-ios.css
* ratchet-css
* ratchet.min.css

#### パスワードの暗号化  
* [sha256.js](http://www.webtoolkit.info/javascript_sha256.html)  

## リンクしている外部ライブラリ等
#### マップの表示
* [googlemaps javascript api](https://developers.google.com/maps/documentation/javascript/?hl=ja)  
 （サイト構築時に　ApiKey の取得が必要です）
   
## アプリの使い方
![タイトル](images/notalone_header_jy.png)  
タイトルをクリックするとトップページに戻ります。  

![イベント](images/notalone_event.png)  

子育てイベント情報を集めて、開催地域ごとに色別してイベントタイトルを表示（月単位）しています。  
下部のカレンダーには、日別のイベント数を表示しています。 
 
<div style="background:#ECDE6C">　オレンジ色　　能登町</div>
<div style="background:#87BCE1">　青色　　　　　輪島市</div>
<div style="background:#E99B9D">　ピンク色　　　珠洲市</div>
<div style="background:#D2DF95">　黄緑色　　　　穴水町</div>  

イベントタイトルをクリックすると、詳細なイベント情報が表示されます。

<dl>
<dd>●ボタン：カレンダーの非表示/表示を切り替える。</dd>
<dd>＊ボタン：イベントを絞り込む。</dd>
<dd>＜｜今月｜＞：イベント月を移動する。</dd>  
</dl>

![マップ](images/notalone_map.png)  
初期設定では、輪島市内を中心に子育て応援情報が表示されます。  

* 「中央変更」：お好み位置（２箇所登録可能）や現在位置を中心に表示することができる。 
* 「マップ選択」：下記の項目ごとにマップアイコンを表示する。

1. 児童館、図書館、公民館、他(屋内で遊べるところ）
2. 公園、他（屋外で遊べるところ）
3. お茶やご飯がしやすいところ（飲食店・お弁当スポット）
4. 病院・学校・保育所、幼稚園、他
5. 病児・病後児保育・一時預かり 
6. トイレ休憩なら（道の駅、赤ちゃんの駅、他）
7. プレミアム・パスポートの使えるところ  
[「プレミアムパスポート」について](http://www.i-oyacomi.net/prepass/page/prepass.php)

* 「Option」： マップの表示設定を変更する。また、パソコン専用機能として、表示されたマップアイコンをA4、A3版サイズの拡大マップに表示（別ウインドウ）することができる。  


![相談](images/notalone_inquiry.png)  
のとノットアローンのメンバーが独自におすすめする相談先リストを紹介します。

***
#### おまけもついてるよ・・
<img src="images/notalone_image.png" width="150" />  

* 誕生日の設定で年齢が表示できます。
* お気に入りの画像も登録できます。
* 設定する時は画像をクリック！！ 
 
トップページの画像をクリックすると、  
<img src="images/notalone_icon.png" width="100" />  
小さい画像が表示されます。  
この小さい画像をクリックすると、家族３人までの誕生日や、お気に入りの写真１枚を登録することができます。  

誕生後の日数（成長経過により歳ケ月数）や出産予定までの日数が表示されます。
写真は、縦サイズに合わせて縮小されます。横幅が画面よりはみ出す場合は、画像ソフトなどでサイズを調整してください。
######（注）端末のブラウザに情報を保存しますので、ブラウザ初期化などにより、情報が消える場合があります。

## 使用しているデータとデータ提供元
（２０１６年４月現在）
#### イベント情報  
<ul>
<li>輪島市子育て支援センター・児童館（輪島市社会福祉協議会）</li>
<li>ほっとサロン（みらい子育てネット輪島）</li>
<li>どんぐりクラブ（みらい子育てネット輪島どんぐりクラブ）</li>
<li>輪島親子昆虫クラブ（輪島親子昆虫クラブ）</li>
<li>こどもみらいセンター（能登町健康福祉課）</li>
<li>まつなみキッズセンター （能登町健康福祉課）</li>
<li>珠洲市子育て支援センター（プロジェクトNNA！）</li>
<li>穴水町子育てイベント（穴水町）</li>
<li>奥能登の子育て応援イベント（のとフェアリィ）</li>
</ul>

#### マップ
<ul>
<li>輪島の子育て安心マップ（子育てメイト）</li>
<li>輪島市内の公園（輪島市生涯学習課）</li>
<li>珠洲市内の保育所（珠洲市オープンデータ）</li>
<li>子育て便利帳２０１５（公益財団法人いしかわ子育て支援財団）</li>
</ul>

##### 相談
<ul>
<li>能登ノットアローンのおすすめ相談先リスト（プロジェクトNNA！）</li>
</ul>

  
  
## よくある質問
<h4>Q:イベント情報を載せたいんだけど・・</h4>
A:２通りあります。<br />
１：プロジェクトメンバーにご相談いただいて掲載を依頼する<br />
２：プロジェクトメンバーとして参加して、掲載していく<br />
詳しくはお問い合わせください！<br />
<br />
<h4>Q:イベント情報の掲載基準はありますか？</h4>
A:子育てを応援するイベントは掲載対象です。行政主催、民間主催、営利非営利問わずイベント情報は掲載対象となります。<br />
ただし場合によっては掲載不可になることがあります。現時点では一律の基準は設けていませんが、例えば下記のようなイベント内容についてはプロジェクトメンバーで相談して決め、場合によっては掲載不可になります。<br />
<br />
<ul>
<li>明らかに高額なセミナーなどの場合</li>
<li>大人だけ、子供だけが参加する習いごとなどの場合</li>
<li>展示がメインでその場に行っても人と会えない可能性がある場合</li>
<li>参加者同士の交流の場がないイベント</li>
<li>子供達や保護者たちの出会い・交流の場として機能していないイベント</li>
</ul>
<h4>Q:開発に参加できるの？</h4>
A:はい。ぜひお問い合わせください！<br />
「のとノットアローン」は子育て応援ウェブアプリとして多くの方のささやかな力が集まって運営されています。<br />
情報を載せたい方、こんな開発してほしい！というご意見もお待ちしてます。

<h4>Q:のとノットアローンはどうやって作っているの？</h4>
A：きっかけは、一般社団法人コードフォーカナザワがよびかけたアプリ開発のコンテスト（アーバンデータチャレンジ）です。そこで子育て情報が不足しているという課題が上がり、興味のある方がプロジェクトメンバーに自主的に参加して開発を進めることになりました。<br />
日々の開発の様子はfacebookなどでも時々発信していますので、ぜひご覧になってみてくださいね！<br />
<a href="https://www.facebook.com/projectNNA/">https://www.facebook.com/projectNNA/</a><br />
