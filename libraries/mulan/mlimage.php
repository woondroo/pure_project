<?php
defined('JPATH_BASE') or die();
jimport('mulan.mlstring');

class MulanImageUtil extends JObject{
	
	public function __construct() {
		// init content here...
	}
	
	static function createFoldersByUtl($url) {
		$folder_list = explode('/',$url);
		if (count($folder_list)) {
			$folder_url = $folder_list[0];
			foreach ($folder_list as $key=>$f) {
				if ($key == 0) {
					continue;
				}
				$folder_url .= '/'.$f;
				if (!file_exists($folder_url)) {
					@mkdir($folder_url);
				}
			}
		}
	}
	
	// thumbimage: $image url like "images/products/123/123456.jpg"
	static function thumbimage($image, $width, $height, $is_utf8=true) {
		/**
		 * 2011-12-9 wengebin Add
		 * 要支持中文，作为 linux 服务器，必须将 GBK/GB2312 格式的字符串转换成 UTF-8，
		 * 因为 linux 上保存的文件名都是 UTF-8 格式的；
		 * 而作为 windows 服务器，因为服务器上保存的文件名默认就是 GBK/GB2312 格式，
		 * 所以 windows 服务器上不需要转换！
		 */
		$is_utf_8 = $is_utf8;
		if ((!IS_WIN && !MulanStringUtil::is_utf8($image)) || !$is_utf_8) {
			$image = iconv("GBK", "UTF-8",$image);
			$is_utf_8 = true;
		} else if (IS_WIN && MulanStringUtil::is_utf8($image) && $is_utf_8) {
			$image = iconv("UTF-8", "GBK",$image);
			$is_utf_8 = false;
		}
		if(!$image) {
			// If no image,return the default image
			return MulanImageUtil::thumbimage('images'.'/'.'stories'.'/'.'default.jpg',$width,$height);
		} else {
			
			// If have image,replace the 'root' uri
			$image = str_replace(JURI::base(),'',$image);
			
			// Decode the image src
			$image = rawurldecode($image);
			
			// If the image is exists and can be resize,return image src,otherwise return the default image src
			$image = preg_replace('/\\\\/','/',$image);
			preg_match('/(^.+)[\/\\\\]{1}.+\.\w+$/',$image,$match);
			$folder = JPATH_SITE.'/'.'images'.'/'.'resized'.'/'.$match[1];
			
			$folder = preg_replace('/\\\\/','/',$folder);
			MulanImageUtil::createFoldersByUtl($folder);
			
			$imagesurl = (file_exists(JPATH_SITE.'/'.$image)) ? MulanImageUtil::resize($image,$width,$height) :  '';
			if(!$imagesurl) {
				return MulanImageUtil::thumbimage('images'.'/'.'stories'.'/'.'default.jpg',$width,$height);
			} else {
				if (!$is_utf_8) {
					$imagesurl = iconv("GBK", "UTF-8",$imagesurl);
				}
				return MulanImageUtil::encodeImageURL($imagesurl);
				//return preg_replace('/\s/','%20',$imagesurl);
			}
		}
	}
	
	static function resize($image,$max_width,$max_height) {
		// read image
		$ext = substr(strrchr($image, '.'), 1); // get the file extension
		if (strtolower($ext) == 'mp3') {
			return 'media/media/images/mime-icon-32/mp3.png';
		}
		
		$path = JPATH_SITE;
		$sizeThumb = getimagesize(JPATH_SITE.'/'.$image);
		$width = $sizeThumb[0];
		$height = $sizeThumb[1];
		if(!$max_width && !$max_height) {
			$max_width = $width;
			$max_height = $height;
		}else{
			if(!$max_width) $max_width = 1000;
			if(!$max_height) $max_height = 1000;
		}
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		if (($width <= $max_width) && ($height <= $max_height) ) {
			$tn_width = $width;
			$tn_height = $height;
		} else if (($x_ratio * $height) < $max_height) {
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		} else {
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}
		
		$rzname = substr($image, 0, strpos($image,'.'))."_{$tn_width}_{$tn_height}.{$ext}"; // get the file extension
		//$rzname = preg_replace('/\s/','%20',$rzname);
		$resized = $path.'/'.'images'.'/'.'resized'.'/'.$rzname;
		if(file_exists($resized)){
			$smallImg = getimagesize($resized);
			if (($smallImg[0] <= $tn_width && $smallImg[1] == $tn_height) ||
			($smallImg[1] <= $tn_height && $smallImg[0] == $tn_width)) {
				return 'images'.'/'.'resized'.'/'.$rzname;
			}
		}
		if(!file_exists($path.'/'.'images'.'/'.'resized'.DS) && !@mkdir($path.'/'.'images'.'/'.'resized'.DS,0755)) return '';
		$folders = explode(DS,$image);
		$tmppath = $path.'/'.'images'.'/'.'resized'.DS;
		for($i=0;$i < count($folders)-1; $i++){
			if(!file_exists($tmppath.$folders[$i]) && !@mkdir($tmppath.$folders[$i],0755)) return '';
			$tmppath = $tmppath.$folders[$i].DS;
		}
		if (strcasecmp($ext,'jpg') == 0 || strcasecmp($ext,'jpeg') == 0) {
			$src = imagecreatefromjpeg(JPATH_SITE.'/'.$image);
		} else if (strcasecmp($ext,'png') == 0) {
			$src = imagecreatefrompng(JPATH_SITE.'/'.$image);
		} else if (strcasecmp($ext,'gif') == 0) {
			$src = imagecreatefromgif(JPATH_SITE.'/'.$image);
		}
		
		$dst = imagecreatetruecolor($tn_width,$tn_height);
		if(strcasecmp($ext,'png') == 0){
			imagesavealpha($src,true);
	    	imagealphablending($dst,false);
	    	imagesavealpha($dst,true);
		}
		
		if (function_exists('imageantialias')) {
			imageantialias($dst, true);
		}
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
		if(strcasecmp($ext,'png') == 0){
			imagepng($dst, $resized);
		}else{
			imagejpeg($dst, $resized, 90); // write the thumbnail to cache as well...
		}
		return "images/resized/".$rzname;
	}
	
	// thumbcurimage: $image url like "images/products/123/123456.jpg", change current image to a thumb image
	static function thumbcurimage($image, $width, $height, $isdelete=true, $is_utf8=true) {
		/**
		 * 2011-12-9 wengebin Add
		 * 要支持中文，作为 linux 服务器，必须将 GBK/GB2312 格式的字符串转换成 UTF-8，
		 * 因为 linux 上保存的文件名都是 UTF-8 格式的；
		 * 而作为 windows 服务器，因为服务器上保存的文件名默认就是 GBK/GB2312 格式，
		 * 所以 windows 服务器上不需要转换！
		 */
		$is_utf_8 = $is_utf8;
		if ((!IS_WIN && !MulanStringUtil::is_utf8($image)) || !$is_utf_8) {
			$image = iconv("GBK", "UTF-8",$image);
			$is_utf_8 = true;
		} else if (IS_WIN && MulanStringUtil::is_utf8($image) && $is_utf_8) {
			$image = iconv("UTF-8", "GBK",$image);
			$is_utf_8 = false;
		}
		
		if(!$image) {
			return MulanImageUtil::thumbimage('images'.'/'.'stories'.'/'.'default.jpg',$width,$height);
		} else {
			$image = str_replace(JURI::base(),'',$image);
			$image = rawurldecode($image);
			
			$image = preg_replace('/\\\\/','/',$image);
			preg_match('/(^.+)[\/\\\\]{1}.+\.\w+$/',$image,$match);
			$folder = JPATH_SITE.'/'.'images'.'/'.'resized'.'/'.$match[1];
			
			$folder = preg_replace('/\\\\/','/',$folder);
			MulanImageUtil::createFoldersByUtl($folder);
			
			$imagesurl = (file_exists(JPATH_SITE.'/'.$image)) ? MulanImageUtil::resizecur($image,$width,$height,$isdelete) :  '' ;
			if (!$imagesurl) {
				return MulanImageUtil::thumbimage('images'.'/'.'stories'.'/'.'default.jpg',$width,$height);
			} else {
				if (!$is_utf_8) {
					$imagesurl = iconv("GBK", "UTF-8",$imagesurl);
				}
				return MulanImageUtil::encodeImageURL($imagesurl);
			}
		}
	}
	
	static function resizecur($image,$max_width,$max_height,$isdelete=true) {
		$path = JPATH_SITE;
		$sizeThumb = getimagesize(JPATH_SITE.'/'.$image);
		$width = $sizeThumb[0];
		$height = $sizeThumb[1];
		if(!$max_width && !$max_height) {
			$max_width = $width;
			$max_height = $height;
		}else{
			if(!$max_width) $max_width = 1000;
			if(!$max_height) $max_height = 1000;
		}
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		if (($width <= $max_width) && ($height <= $max_height) ) {
			$tn_width = $width;
			$tn_height = $height;
		} else if (($x_ratio * $height) < $max_height) {
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		} else {
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}
		// read image
		$ext = substr(strrchr($image, '.'), 1); // get the file extension
		$rzname = substr($image, 0, strpos($image,'.'))."_{$tn_width}_{$tn_height}.{$ext}"; // get the file extension
		//$rzname = preg_replace('/\s/','%20',$rzname);
		$resized = $path.'/'.$rzname;
		if(file_exists($resized)){
			$smallImg = getimagesize($resized);
			if (($smallImg[0] <= $tn_width && $smallImg[1] == $tn_height) ||
			($smallImg[1] <= $tn_height && $smallImg[0] == $tn_width)) {
				return $rzname;
			}
		}
		if(!file_exists($path) && !@mkdir($path,0755)) return '';
		$folders = explode(DS,$image);
		$tmppath = $path;
		for($i=0;$i < count($folders)-1; $i++){
			if(!file_exists($tmppath.$folders[$i]) && !@mkdir($tmppath.$folders[$i],0755)) return '';
			$tmppath = $tmppath.$folders[$i].DS;
		}
		if (strcasecmp($ext,'jpg') == 0 || strcasecmp($ext,'jpeg') == 0) {
			$src = imagecreatefromjpeg(JPATH_SITE.'/'.$image);
		} else if (strcasecmp($ext,'png') == 0) {
			$src = imagecreatefrompng(JPATH_SITE.'/'.$image);
		} else if (strcasecmp($ext,'gif') == 0) {
			$src = imagecreatefromgif(JPATH_SITE.'/'.$image);
		}
		
		$dst = imagecreatetruecolor($tn_width,$tn_height);
		if(strcasecmp($ext,'png') == 0){
			imagesavealpha($src,true);
	    	imagealphablending($dst,false);
	    	imagesavealpha($dst,true);
		}
		
		if (function_exists('imageantialias')) {
			imageantialias($dst, true);
		}
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
		if(strcasecmp($ext,'png') == 0){
			imagepng($dst, $resized);
		}else{
			imagejpeg($dst, $resized, 90); // write the thumbnail to cache as well...
		}
		if ($isdelete) {
			unlink(JPATH_SITE.'/'.$image);
		}
		return $rzname;
	}
	
	// get the images in the folder
	static function images($folder, $types = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG')) {
		$files	= array();
		$images	= array();
		
		//$dir = JPATH_BASE.'/'.$folder;
		$dir = JPATH_SITE.'/'.$folder;
		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html' ) {
						$files[] = $file;
					}
				}
			}
			closedir($handle);
			asort($files);
			$i = 0;
			foreach ($files as $img)
			{
				if (!is_dir($dir .'/'. $img))
				{
					$type = substr($img, strrpos($img, '.') + 1);
					if (in_array($type, $types)) {
						$images[$i]->name 	= $img;
						$images[$i]->folder	= $folder;
						++$i;
					}
				}
			}
		}
		return $images;
	}
	
	static function encodeImageURL($image_url) {
		$img = substr($image_url, strripos($image_url, '/')+1, strlen($image_url));
		$encode_img = str_replace('+', '%20', urlencode($img));
		return str_replace($img, $encode_img, $image_url);
	}
	
	// clear folder
	static function folder($folder) {
		$LiveSite = JURI::base();
		// if folder includes livesite info, remove
		if ( JString::strpos($folder, $LiveSite) === 0 ) {
			$folder = str_replace( $LiveSite, '', $folder );
		}
		// if folder includes absolute path, remove
		if ( JString::strpos($folder, JPATH_SITE) === 0 ) {
			$folder = str_replace( JPATH_BASE, '', $folder );
		}
		$folder = str_replace('\\',DS,$folder);
		$folder = str_replace(DS,DS,$folder);
		return $folder;
	}
	
	// Function wrappers for TriggerEvent usage
	static function onCaptcha_Display() {
		
		/* -------- 新版复杂验证码，全部注释未注释的部分，然后取消简单验证码注释的部分即可 --------- */
		$session =& JFactory::getSession();
		// 生成的验证码中包含的字符，可增减
		$str = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
		$val = '';
		$captcha = imagecreatefrompng(JPATH_ROOT.'/templates/system/images/captcha.png');
		
		// 图片背景色
		$bg = imagecolorallocate($captcha,200+(rand(0,50)),200+(rand(0,50)),200+(rand(0,50)));
		// 图片填充背景色
		imagefill($captcha,0,0,$bg);
		
		// 加入100个干扰象素
		for($i = 0; $i < 100; $i++) {
			// 验证码干扰像素颜色
			$randcolor = imagecolorallocate($captcha,rand(0,255),rand(0,255),rand(0,255));
			// 将干扰像素画入图片
			imagesetpixel($captcha, rand()%70, rand()%30, $randcolor);
		} 
		
		// 加入2条干扰线条
		for ($i = 0; $i < 2; $i++) {
			if ($i == 0) {
				// 干扰线条的颜色
				$line = imagecolorallocate($captcha,rand(100,200), rand(100,200), rand(100,200));
				// 画干扰线条
				imageline($captcha,0,3+(rand(0,6)),56,3+(rand(0,6)),$line);
			} else if ($i == 1) {
				$line = imagecolorallocate($captcha,rand(100,200), rand(100,200), rand(100,200));
				imageline($captcha,10,12+(rand(0,6)),100,12+(rand(0,6)),$line);
			}
		}
		
		// 设置验证码字体
		$arial = JPATH_ROOT.'/templates/system/images/arial.ttf';
		// 循环绘制验证码，一般为4个
		for ($i = 0; $i < 4; $i++) {
			// 获取一个验证码字符
			$one_str = $str{rand(0, strlen($str)-1)};
			// 验证码字符串，保存到session用来验证
			$val .= $one_str;
			// 验证码颜色
			$font_color = imagecolorallocate($captcha, rand(0,100), rand(0,100), rand(0,100));
			// 将验证码画入图片
			imagettftext($captcha, 12+(rand(0,6)), rand(-15,15), 12*($i+1), 16+rand(0,8), $font_color, $arial, $one_str); 
		}
		// 将生成的验证字符放入session
		$session->set('bigo_uid',$val); // secret word
		// 生成图片输出
		header("Content-type: image/png");
		imagepng($captcha);
		exit();
		
		/* -------- 旧版简单验证码，取消注释即可 ---------
		$session =& JFactory::getSession();
		$str="0123456789";
		$val = '';
		for ( $i = 0; $i < 4; $i++ ) {
			$val .= $str { rand ( 0, strlen ( $str ) - 1 ) };
		}
		$captcha = imagecreatefrompng(JPATH_PLUGINS.DS.'system'.DS."captcha.png");
		$white = imagecolorallocate($captcha, 255, 255, 255);
		$line = imagecolorallocate($captcha,233,239,239);
		
		$arial = JPATH_PLUGINS.DS.'system'.DS.'arial.ttf';
		imagettftext($captcha, 12, 0, 12, 19, $white, $arial, $val); 
		$session->set('bigo_uid',$val); // secret word
		
		header("Content-type: image/png");
		imagepng($captcha);
		exit(); 
		*/
	}

	//Function 
	static function onCaptcha_confirm($word, &$return) {	
		//require_once(JPATH_PLUGINS.DS.'system'.DS.'Captcha04'.DS."Functions.php");
		$session =& JFactory::getSession();
		
		// guessing protection
		$tries = 0; 
		$tries = $session->get('attempts');		
		$session->set('attempts', ++$tries);
		//if (!$word || $tries > $this->max_tries) {
		if (!$word) {
			return false;
		}
		
  		//$correct = md5_decrypt ( $session->get('bigo_uid') );
  		$correct =  $session->get('bigo_uid') ;
  		$session->set('bigo_uid', null); 
  		
  		$word=preg_replace('/０/','0',$word);
  		$word=preg_replace('/１/','1',$word);
  		$word=preg_replace('/２/','2',$word);
  		$word=preg_replace('/３/','3',$word);
  		$word=preg_replace('/４/','4',$word);
  		$word=preg_replace('/５/','5',$word);
  		$word=preg_replace('/６/','6',$word);
  		$word=preg_replace('/７/','7',$word);
  		$word=preg_replace('/８/','8',$word);
  		$word=preg_replace('/９/','9',$word);
  		if (strtolower($word) == strtolower($correct)) {
  			$session->set('attempts',0); 
  			$return = true;
  		} else $return = false;  		
		
		return $return;
	}
}
?>