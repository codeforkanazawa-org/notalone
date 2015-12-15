<?php
// エラー出力する場合
//ini_set( 'display_errors', "1" ); 
//ini_set( 'display_startup_errors', "1" ); 

print("ここまできました");

/*
define('FILE_PATH','uploads/images/'); //保存するパスを指定
define('FILE_PATH_N','uploads'); //保存するパス / なし

// json_encode()関数が存在しないなら
if (!function_exists('json_encode')) {
	// JSON.phpを読み込んで
	require_once 'include/JSON.php';
	// json_encode()関数を定義する
	function json_encode($value) {
		$s = new Services_JSON();
		return $s->encodeUnsafe($value);
	}
	// json_decode()関数を定義する
	function json_decode($json, $assoc = false) {
		$s = new Services_JSON($assoc ? SERVICES_JSON_LOOSE_TYPE : 0);
		return $s->decode($json);
	}
}
//********************************

echo $FILES[0];

if ( !empty($_FILES) ) {
	$output = array();
	for ( $i=0; $i<count($_FILES["upfile"]["tmp_name"]); $i++ ) {
        	if ( is_uploaded_file($_FILES["upfile"]["tmp_name"][$i]) ) {
 
			//ファイル名を小文字に変換 
            		$name = mb_strtolower($_FILES['upfile']['name'][$i]);
            		$tempFile = $_FILES['upfile']['tmp_name'][$i];
 
            		// Validate the file type
            		$fileTypes = array('jpg','jpeg','gif','png','JPG');   // File extensions
            		$fileParts = pathinfo($_FILES['upfile']['name'][$i]);

			//print_r($fileParts);
			//print("<br />");

            		// ファイル名がアルファベットのみかをチェック
            		if ( preg_match("/^([a-zA-Z0-9\.\-\_])+$/ui", $name) == "0" ) {
                		// アルファベット以外を含む場合はファイル名を日時とする
                		$saveFileName = date("Ymd_His", time());
            		} else {
				//拡張子を除いたファイル名を取得
                		if ( preg_match("/\.jpg$/ui", $name) == true ) {
                    			$ret = explode('.jpg', $name);
                		} elseif ( preg_match("/\.gif$/ui", $name) == true ) {
                    			$ret = explode('.gif', $name);
                		} elseif ( preg_match("/\.png$/ui", $name) == true ) {
                    			$ret = explode('.png', $name);
                		}
                		$saveFileName = $ret[0]; // 拡張子を除いたそのまま
            		}
 
            		// マイクロ秒をファイル名に付加
            		//$saveFileName = FILE_PATH . '[' . (microtime()*1000000) . ']' . $saveFileName;
			$realFileName = '[' . (microtime()*1000000) . ']' . $saveFileName;

			$saveFileName = FILE_PATH . $realFileName;

			//拡張子のチェック
            		if ( in_array($fileParts['extension'], $fileTypes) ) {

 				//tempフォルダから実際のフォルダへの格納が成功した場合
                		if ( move_uploaded_file($_FILES["upfile"]["tmp_name"][$i], $saveFileName . '.' . 						$fileParts['extension']) ) {
                    			//chmod($fileName, 0644);

                    			//echo $_FILES["upfile"]["name"][$i] . "をアップロードしました。<br>";

					$realFileName .= '.' . $fileParts['extension'];



	//画像サイズ調整する
	//とりあえず横サイズのみ規制、今後縦も規制。回転も必要
	//$new_width = 300;
	//resizeImage(FILE_PATH . $realFileName,$new_width,FILE_PATH_N);
	//**************

					$output[$i] = array(
						"fname" => $realFileName
					);

                		} else {
                   			 echo "アップロードエラー";
               			}


            		} else {
              			echo "アップロードの対象は画像ファイル（.jpg/.gif/.png）のみです。<br />";
            		}

        	}
    	}
	$json_value	= json_encode( $output );
	//print_r($output);
	//print("<br />");

	//return value
	echo $json_value; 
}


//画像のリサイズ関数
	//$image:元のファイル名　$new_width:出力サイズ　$dir:出力するフォルダ（省略時は、この関数を実行したフォルダ）
	function resizeImage($image,$new_width,$dir = "."){
		//list($width,$height,$type) = getimagesize($image["tmp_name"]);
		list($width,$height,$type) = getimagesize($image);

		$new_height = round($height*$new_width/$width);
		$emp_img = imagecreatetruecolor($new_width,$new_height);
		switch($type){
			case IMAGETYPE_JPEG:
				$new_image = imagecreatefromjpeg($image["tmp_name"]);
				break;
			case IMAGETYPE_GIF:
				$new_image = imagecreatefromgif($image["tmp_name"]);
				break;
			case IMAGETYPE_PNG:
				imagealphablending($emp_img, false);
				imagesavealpha($emp_img, true);
				$new_image = imagecreatefrompng($image["tmp_name"]);
				break;
		}
		imagecopyresampled($emp_img,$new_image,0,0,0,0,$new_width,$new_height,$width,$height);
		$date = date("YmdHis");
		switch($type){
			case IMAGETYPE_JPEG:
				imagejpeg($emp_img,$dir."/".$date.".jpg");
				break;
			case IMAGETYPE_GIF:
				$bgcolor = imagecolorallocatealpha($new_image,0,0,0,127);
				imagefill($emp_img, 0, 0, $bgcolor);
				imagecolortransparent($emp_img,$bgcolor);
				imagegif($emp_img,$dir."/".$date.".gif");
				break;
			case IMAGETYPE_PNG:
				imagepng($emp_img,$dir."/".$date.".png");
				break;
		}
		imagedestroy($emp_img);
		imagedestroy($new_image);
	}

*/

?>