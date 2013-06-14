<?php
defined('JPATH_BASE') or die();

class MulanStringUtil extends JObject{
	
	public function __construct(){
		// init content here...
	}
	
	/**
	 * 截取字符数（中文）
	 *
	 * @param string $sourcestr	截取的字符
	 * @param int $cutlength	截取的长度
	 * @param string $suffix	截取后增加的后缀
	 * @return unknown
	 */
	static function substr_zh($sourcestr, $cutlength, $suffix='...') {
		$returnstr = ''; $i = 0; $n = 0;
		$str_length=strlen($sourcestr);	//字符串的字节数
		while (($n<$cutlength) and ($i<=$str_length))
		{
			$temp_str = substr($sourcestr,$i,1);
			$ascnum = Ord($temp_str);	//得到字符串中第$i位字符的ascii码
			if ($ascnum >= 224)			//如果ASCII位高与224，
			{
				$returnstr = $returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符         
				$i = $i + 3;			//实际Byte计为3
				$n++;					//字串长度计1
			}
			elseif ($ascnum >= 192)		//如果ASCII位高与192，
			{
				$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
				$i = $i + 2;			//实际Byte计为2
				$n++;					//字串长度计1
			}
			elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
			{
				$returnstr = $returnstr.substr($sourcestr,$i,1);
				$i = $i + 1;			//实际的Byte数仍计1个
				$n++;					//但考虑整体美观，大写字母计成一个高位字符
			}
			else						//其他情况下，包括小写字母和半角标点符号，
			{
				$returnstr = $returnstr.substr($sourcestr,$i,1);
				$i = $i + 1;			//实际的Byte数计1个
				$n = $n + 0.5;			//小写字母和半角标点等与半个高位字符宽...
			}
		}
		if ($str_length > $i){
			$returnstr = $returnstr . $suffix;	//超过长度时在尾处加上省略号
		}
		return $returnstr;
	}
	
	/**
	 * 得到指定字节数的字符串
	 * 如果要截取3个汉字，则cutstr($string,3*2,'..')
	 */
	static function substr_en($string, $length, $dot='...'){
		if(strlen($string) <= $length)
		{
			return $string;
		}
		$strcut = '';
		$n = $tn = $noc = 0;
		while ($n < strlen($string))
		{
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
			{
				$tn = 1; $n++; $noc++;
			}
			elseif(194 <= $t && $t <= 223)
			{
				$tn = 2; $n += 2; $noc += 2;
			}
			elseif(224 <= $t && $t < 239)
			{
				$tn = 3; $n += 3; $noc += 2;
			}
			elseif(240 <= $t && $t <= 247)
			{
				$tn = 4; $n += 4; $noc += 2;
			}
			elseif(248 <= $t && $t <= 251)
			{
				$tn = 5; $n += 5; $noc += 2;
			}
			elseif($t == 252 || $t == 253)
			{
				$tn = 6; $n += 6; $noc += 2;
			}
			else
			{
				$n++;
			}

			if ($noc >= $length)
			{
				break;
			}
		}
		if ($noc > $length)
		{
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		if($string!=$strcut){
			return $strcut.$dot;
		}else{
			return $strcut;
		}
	}
	
	/**
	 * 去掉和修正影响HTML显示的特殊字符
	 */
	static function strReplace($str, $case=''){
		if($case){
			if(is_array($case)){
				if(in_array('>',$case)){
					$str=preg_replace('/>/','&gt;',$str);
				}
				if(in_array('<',$case)){
					$str=preg_replace('/</','&lt;',$str);
				}
				if(in_array("\n",$case)){
					$str=preg_replace("/\n/",'<br/>',$str);
				}
				if(in_array("\s",$case)){
					$str=preg_replace('/\s/','&nbsp;',$str);
				}
				if(in_array('"',$case)){
					$str=preg_replace('/\"/','&quot;',$str);
				}
				if(in_array("'",$case)){
					$str=preg_replace('/\'/','&#39;',$str);
				}
			}else{
				switch ($case){
					case '>':$str=preg_replace('/>/','&gt;',$str);break;
					case '<':$str=preg_replace('/</','&lt;',$str);break;
					case "\n":$str=preg_replace("/\n/",'<br/>',$str);break;
					case "\s":$str=preg_replace('/\s/','&nbsp;',$str);break;
					case '"':$str=preg_replace('/\"/','&quot;',$str);break;
					case "'":$str=preg_replace('/\'/','&#39;',$str);break;
				}
			}
		} else {
			$str=preg_replace('/>/','&gt;',$str);
			$str=preg_replace('/</','&lt;',$str);
			$str=preg_replace("/\n/",'<br/>',$str);
			$str=preg_replace('/\s/','&nbsp;',$str);
			$str=preg_replace('/\"/','&quot;',$str);
			$str=preg_replace('/\'/','&#39;',$str);
		}
		return $str;
	}
	
	/**
	 * 得到随机数
	 *
	 * @param int $len
	 * @param String $str
	 * @return String
	 */
	static function getRandomStr($len=4, $str=''){
		if(!$str){
			$str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$rs = '';
		$alllen=strlen($str);
		for($i=0;$i<$len;$i++){
			$rs .=$str[rand(0,$alllen-1)];
		}
		return $rs;
	}
	
	/**
	 * 整理URL，因为有些用户只喜欢输入www.baidu.com/baidu.com,然后这样的URL，是不能跳转的，要加上http://
	 */
	static function clearUrl($url){
		if(!$url)return '';
		if(preg_match('/^http:\/\//',$url)){
			return $url;
		}else{
			return 'http://'.$url;
		}
	}
	
	/*
	*获得客户端真实IP地址
	*/
	function getClientIP() { 
		$unknown = 'unknown';
		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) )
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) )
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		/**
		 * 处理多层代理的情况
		 * 或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
		 */
		if (false !== strpos($ip,','))$ip = reset(explode(',', $ip));
		return $ip;
	}
	
	/**
	 * 判断字符串是否是utf-8格式
	 */
	static function is_utf8($str) {
		if (empty($str)) return false;

	    $len = strlen($str);
	    for($i = 0; $i < $len; $i++){
	        $c = ord($str[$i]);
	        if ($c > 128) {
	            if (($c > 247)) return false;
	            elseif ($c > 239) $bytes = 4;
	            elseif ($c > 223) $bytes = 3;
	            elseif ($c > 191) $bytes = 2;
	            else return false;

	            if (($i + $bytes) > $len) return false;

	            while ($bytes > 1) {
	                $i++;
	                $b = ord($str[$i]);
	                if ($b < 128 || $b > 191) return false;
	                $bytes--;
	            }
	        }
	    }
	    return true;
	}
}
?>