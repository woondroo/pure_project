<?php
defined('JPATH_BASE') or die();
// require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mldb.php' );
jimport('mulan.mldb');
class MulanHtmlUtil extends JObject{
	public static $menus = array();
	
	public function __construct(){
		// init content here...
	}
	
	/**
	 * 得到由对象列表组合成的select option $mode为true表示没有选择提示项
	 * $amode =true表情$a为数组，否则为对象
	 * mcs = array(array('id'=>'f店','name'=>'f店'),array('id'=>'创意店','name'=>'创意店'),array('id'=>'其它','name'=>'其它'));
	 * <select name="dptype" id="dptype"><?php echo getSelectOption($mcs,'id','name',$user->dptype,false); ?></select>
	 */
	static function getSelectOption($list=null,$id=null,$name=null,$nowid=null,$mode=false){
		$html = '';
		if(!$mode){
			$html = '<option value="0">选择</option>';	
		}
		if(!$list||!$id||!$name){
			return $html;
		};
		$amode= is_array($list[0]);
		foreach ($list as $o){
			$add = '';
			if($amode){
				if(''.$nowid==(''.$o[$id])){
					$add = ' selected="selected"';
				}
				$html.= '<option'.$add.' value="'.$o[$id].'">'.$o[$name].'</option>';
			}else{
				if($nowid==$o->$id){
					$add = ' selected="selected"';
				}
				$html.= '<option'.$add.' value="'.$o->$id.'">'.$o->$name.'</option>';
			}
		}
		return $html;
	}
	
	/**
	 * 由菜单唯一标识符得到URL
	 * $mode 是否不要友好链接
	 */
	static function getUrlByAlias($alias,$app='',$mode=false){
		if(!$alias){
			return '';
		}
		if(preg_match('/^[^&].+/',$app)){
			$app = '&'.$app;
		}
		if (array_key_exists($alias,self::$menus)) {
			$menu = self::$menus[$alias];
		} else {
			$menu = MulanDBUtil::getObjectBySql('select id,link,`type` from #__menu where ( published = 1 and alias='.MulanDBUtil::dbQuote($alias).') limit 1');
			self::$menus[$alias] = $menu;
		}
		if($menu->id){
			if($menu->type=='menulink'){
				$link = $menu->link.$app; 
			}else{
				$link = $menu->link.'&Itemid='.$menu->id.$app;
			}
			return $mode?$link:JRoute::_($link);
		}else{
			return '';
		}
	}
	
	//----------------------- 分页方法开始 ---------------------
	
	/**
	 * 提取出来的单独的分页方法
	 * @author mulan_lishaowen
	 * @param $start 分页的开始记录数
	 * @param $limit 每页显示几个
	 * @param $allnum 全部记录数
	 * @param $pagename form的name;
	 * @param $sign 当一个页面有多个分页时，避免冲突的标记
	 * @param $isselect 是否要在页面上显示每页记录数的下拉框
	 */
	static function getPageNav($start=0,$limit=5,$allnum,$pagename='pagenav',$isselect=false,$sign=''){
		$nav='';
		if($isselect){
			$nav .='<span class="pagelimit">每页显示:<select name="limit'.$sign.'" id="limit" onchange="javascript:document.'.$pagename.'.submit();">';
			$sel= array(5,8,10,20,50);
			$select='';
			$inSel=false;
			foreach($sel as $s){
				$add='';
				if($s==$limit){
					$add='selected="selected"';
					$inSel=true;
				}
				$select .='<option '.$add.' value="'.$s.'">'.($s?$s:'全部').'</option>';
			}
			if(!$inSel){
				$select ='<option selected="selected" value="'.$limit.'">'.$limit.'</option>'.$select;
			}
			$nav .=$select.'</select>&nbsp;&nbsp;</span>';
		}
		if(($limit==0)||($allnum==0))return $nav;
		$pagesize = (int)(($allnum+$limit-1)/$limit); //总页
		$pagenow = (int)(($start)/$limit)+1; //当前页码 一开始为1
		if($start!=0){
			$thepage=($pagenow-2)<0?0:($pagenow-2);
			$prelimit=$thepage*$limit;
			$nav .='&nbsp;<a href="javascript:void(0);" onclick="javascript: document.'.$pagename.'.start'.$sign.'.value=0; document.'.$pagename.'.submit();return false;">首页</a>';
			$nav .='&nbsp;<a href="javascript:void(0);"  onclick="javascript: document.'.$pagename.'.start'.$sign.'.value='.$prelimit.'; document.'.$pagename.'.submit();return false;">上一页</a>';
		}
		$pagestart=((int)(($start)/(10*$limit))*10+1);
		for($i=$pagestart;$i<(10+$pagestart);$i++){
			if($i>$pagesize) break;
			$addc='';
			if ($i==$pagenow){
				$nav .='&nbsp;<a class="active">'.($i).'</a>';
			} else {
				$thelimit=($i-1)*$limit;
				$nav .='&nbsp;<a href="javascript:void(0);" '.$addc.' onclick="javascript: document.'.$pagename.'.start'.$sign.'.value='.$thelimit.'; document.'.$pagename.'.submit();return false;">'.($i).'</a>';
			}
		}
		if(!($pagenow>=$pagesize)){
			$thepage=($pagenow-2)<0?0:($pagenow-2);
			$nextlimit=($pagenow)*$limit;
			$endlimit=($pagesize-1)*$limit;
			$nav .='&nbsp;<a href="javascript:void(0);" onclick="javascript: document.'.$pagename.'.start'.$sign.'.value='.$nextlimit.'; document.'.$pagename.'.submit();return false;">下一页</a>';
			$nav .='&nbsp;<a href="javascript:void(0);" onclick="javascript: document.'.$pagename.'.start'.$sign.'.value='.$endlimit.'; document.'.$pagename.'.submit();return false;">尾页</a>';
		}
		$nav .=('&nbsp;&nbsp;<span class="pagenow"> 第'.($pagenow).'页 </span><span class="allpage"> 共'.$pagesize.'页');
		$nav .='<input type="hidden" value="'.$start.'" name="start'.$sign.'" />';
		return $nav;
	}
	
	/**
	 * 优化后的分页方法
	 * 不是以前的FORM表示提交，全是链接跳转,附带当前页面的GET参数
	 * $sign 一个页面有多少分布的时候写的　如1,2,3
	 */
	static function getPageNav2($start=0,$limit=5,$allnum,$sign=''){
		$get = JRequest::get('get');
		$url = '';	//拼出当前访问地址的URL;去除start参数
		$startkey = 'start'.$sign;
		$url = MulanHtmlUtil::getCurrentUrl(array($startkey,'limitstart'));
		$url = $url.'&'.$startkey.'=';
		$nav='<div class="pagenav">';
		
		if(($limit==0)||($allnum==0))return '';
		$pagesize = (int)(($allnum+$limit-1)/$limit); //总页
		$pagenow = (int)(($start)/$limit); //当前页码 一开始为0
		
		$start = $pagenow-4;
		if($start<0){
			$start=0;
		}
		$end =$start+8;
		if($end>$pagesize){
			$start = $start-($end-$pagesize);
			if($start<0){
				$start=0;
			}
			$end=$pagesize;
		}
		$pre = ($pagenow-1)<0?'':($pagenow-1);
		$next = ($pagenow+1)>=$pagesize?'':($pagenow+1);
		
		if($pre!==''){//前一个
			$nav .='<a href="'.$url.($pre*$limit).'">上一页</a>';
		}else{
			$nav .='<a href="javascript:;" class="noactive">上一页</a>';
		}
		
		if($start>0){//第一个 首页
			$isfirst=true;
			$nav .='<a href="'.$url.'0">1</a>';
		}
		if($end!=$pagesize){//最后一个 尾页
			$isend=true;
		}
		for($i=0;$start<$end;$start++,$i++){//8个遍历
			if($isfirst&&$i==0&&$pagesize>8&&$start!=$pagenow||$isend&&($start==($end-1))){
				$show='...';
			}else{
				$show=$start+1;
			}
			if($start==$pagenow){
				$nav .='<a class="active" href="javascript:;">'.$show.'</a>';
			}else{
				$nav .='<a href="'.$url.($start*$limit).'">'.$show.'</a>';
			}
		}
		if($isend){//最后一个 尾页
			$nav .='<a href="'.$url.(($pagesize-1)*$limit).'">'.$pagesize.'</a>';
		}
		if($next!==''){//下一个
			$nav .='<a href="'.$url.($next*$limit).'">下一页</a>';
		}else{
			$nav .='<a href="javascript:;" class="noactive">下一页</a>';
		}
		return $nav.'<div class="clr"></div></div>';
	}
	
	/**
	 * 提取当前路径上的URL，filter为不要保留的变量//limitstart,start
	 * 
	 */
	 static function getCurrentUrl($filter=array()){
	  $get = JRequest::get('get');
	  $url = '';
	  foreach($get as $k=>$v){
	  	if(in_array($k,$filter))continue;
	  	if(is_array($v)){
	  		foreach($v as $a){
	  			if($url){
	  				$url .='&';
	  			}
	  			$url .=$k.'[]='.$a;
	  		}
	  	}else{
	  		if($url){
	  			$url .='&';
	  		}
	  		$url .=$k.'='.$v;
	  	}
	  }
	  $url ='index.php?'.$url;
	  return $url;
	}
	
	//----------------------- 分页方法结束 ---------------------
	
	static function getFlashHtml($src,$w='100%',$h='100%',$falvals='',$wmode='transparent'){
		if(!$w){
			$w='100%';
		}
		if(!$h){
			$h='100%';
		}
		$wh = 'width="'.$w.'" height="'.$h.'"';
		
		$html .=
				'<object '.$wh.' id="flashdv" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" align="middle">
					 <param name="wmode" value="'.$wmode.'" />
					 <param name="movie" value="'.$src.($falvals ? '?'.$falvals : '').'" />
					 <param name="quality" value="high" />
					 <param name="flashvars" value="'.($falvals ? $falvals : '').'" />
					 <param name="allowFullScreen" value="true" />
					 <param name="allowScriptAccess" value="always" />
					 <embed src="'.$src.($falvals ? '?'.$falvals : '').'" '.$wh.' flashvars="'.($falvals ? $falvals : '').'" wmode="'.$wmode.'" quality="high" align="middle" allowscriptaccess="always" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
				 </object>';
		return $html;
	}
	
	/**
	 * 播放器代码
	 */
	static function getPlayerHtml($o,$sign){
		//$way = 'ku6';
		$ccvid = $o->ccid;
		if($sign=='homejp'){//首页精品
			$style = 'NTvzo_Z75kw.';//'cw16yhE2Ags.';
			$w = 382;
			$h = 324;
			$autoplay='false';
		}elseif($sign=='preview'){//预览
			$style = 'NTvzo_Z75kw.';//'cw16yhE2Ags.';
			$w = 482;
			$h = 324;
			$autoplay='false';
		}elseif($sign=='play'){//播放页面
			$style = '6T18XnfDMPs.';//'cZk3El0rCRw.';
			$w = 580;
			$h = 460;
			$autoplay='true';
		}else if($sign=='homeflash'){//首页FLASH
			$style = '_EHNIb6_YQg.';//'u9vsT-mlhV4.';
			$w = 443;
			$h = 359;
			$autoplay='true';
		}
		
		if($ccvid){		//CC通道
			return MulanHtmlUtil::getCCPlayerHtml($ccvid,$autoplay,$w,$h);
		} else {		//本地通道
			$base = JURI::base(true).'/';
			$falvals = 'file='.MulanHtmlUtil::fortestpath($o->filename).'&hign='.MulanHtmlUtil::fortestpath($o->filename_hd).'&image='.$base.$o->picture.'&autostart='.$autoplay.'&loop=false&ID='.$o->id;
			return MulanHtmlUtil::getFlashHtml(MulanHtmlUtil::getFlashPath('player_new.swf'),$w,$h,$falvals,'window');
		}
	}
	
	/**
	 * 本地通道视频文件路径转换
	 * home.php中有对应的JS 
	 */
	static function fortestpath($path){
		if(!$path)return'';
		if(preg_match('/^https?:/',$path)){
			return $path;
		}else{
			$pre = getConfigByKey('serviceip');
			$pre = preg_match('/\/$/',$pre)?$pre:$pre.'/';
			return $pre.$path;
		}
	}
	
	/**
	 * 得到FLASH的存储路径
	 */
	static function getFlashPath($name=''){
		return JURI::root(true).'/templates/rhuk_milkyway/flash/'.$name;
	}
	
	/**
	 * 得到CC播放器代码
	 */
	static function getCCPlayerHtml($ccvid,$autoplay,$w,$h){
		return MulanHtmlUtil::getFlashHtml('http://union.bokecc.com/flash/player.swf?vid='.$ccvid.'&siteid='.getConfigByKey('ccsiteid').'&autoStart='.$autoplay.'&r='.rand(),$w,$h);
	}
	
	/**
	 * 得到CC播放地址
	 */
	static function getCCLink($ccvid,$autoplay='false'){
		return 'http://union.bokecc.com/flash/player.swf?vid='.$ccvid.'&siteid='.getConfigByKey('ccsiteid').'&autoStart='.$autoplay.'&r='.rand();
	}
	
	/**
	 * 替换url中的特殊字符
	 */
	static function replaceURL($url) {
		$url = preg_replace('/\%/','%25',$url);
		$url = preg_replace('/\+/','%2B',$url);
		$url = preg_replace('/\//','%2F',$url);
		$url = preg_replace('/\?/','%3F',$url);
		$url = preg_replace('/\#/','%23',$url);
		$url = preg_replace('/\&/','%26',$url);
		$url = preg_replace('/\=/','%3D',$url);
		return $url;
	}
	
	/**
	 * 得到当前的域名，
	 * 当url为http://www.d-niu.com/时，得到域名为d-niu.com
	 */
	static function getDomain(){
		$url = JURI::base();
		$app=  JURI::base(true);
		$pattern = "/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/";
		preg_match($pattern, $url, $matches);
		if(count($matches) > 0) {
			return $matches[0].$app;
		} else {
			$rs = parse_url($url);
			$main_url = $rs["host"];
			if(!strcmp(long2ip(sprintf("%u",ip2long($main_url))),$main_url)) {
				return $main_url.$app;
			} else {
				$arr = explode(".",$main_url);
				$count=count($arr);
		    	$endArr = array("com","net","org","3322");//com.cn  net.cn 等情况
			    if(count($arr)==1){
			    	$domain = $arr[$count-1];
			    }else{
				    if (in_array($arr[$count-2],$endArr)){
				     $domain = $arr[$count-3].".".$arr[$count-2].".".$arr[$count-1];
				    }else{
				     $domain =  $arr[$count-2].".".$arr[$count-1];
				    }
			    }
		    	return $domain.$app;
			}
		}
	}
	
	/**
	 * 上传
 	 * 	$ur=$this->uploadFileUtil($_FILES['fujian'],JPATH_BASE .DS.'images'.DS.$fujianname,1024*10000);
		$elink = 'index.php?option=com_content&view=others&layout=confirmed&project='.$name;
		if($ur=='2'){		//格式不正确
			$this->setRedirect($elink,'格式不正确');
		}else if($ur=='3'){	//文件过大
			$this->setRedirect($elink,'文件过大');
		}else if($ur=='4'){	//上传失败
			$this->setRedirect($elink,'上传失败');
		}else if($ur=='5'){	//没有上传信息
			
		}else if($ur=='1'){	//上传成功
			$fujiadb = 'images/'.$fujianname;
		}
	*/
	static function uploadFileUtil($fileinfos,$path,$size=0,$type=array()){	
		if($fileinfos['size']){
			$filename=$fileinfos['name'];
			if (!IS_WIN && !MulanStringUtil::is_utf8($path)) {
				$path = iconv("GBK", "UTF-8",$path);
			} else if (IS_WIN && MulanStringUtil::is_utf8($path)) {
				$path = iconv("UTF-8", "GBK",$path);
			}
			
			preg_match('/\.([^\.]+)$/',strtolower($filename),$fileformat);
			if($type&&(!in_array($fileformat[1],$type))){
				return '2';			//格式不正确
			}else{
				if($size&&$fileinfos['size'] > intval($size)){
					return '3';		//文件太大
				}else{
					if(move_uploaded_file($fileinfos['tmp_name'],$path)){
						return '1';//上传成功
					}else{
						return '4';//上传失败
					}
				}
			}
		} else {
			return '5';				//没有上传任何信息
		}
	}
	
	/**
	 * 从URL中获取指定参数的值
	 */
	static function getUrlValueByKey($key){
		$url = $_SERVER['REQUEST_URI'];
		$paras = explode('?',$url);
		$para_array=explode('&',$paras[1]);
		foreach($para_array as $p){
			$one_para = explode('=',$p);
			if($one_para[0]==$key){
				return $one_para[1]; 
			}
		}	
		return '';
	}
	
	/**
	 * 获取当前请求地址的别名
	 */
	static function getAlias() {
		$alias_all = explode('/',$_SERVER['REQUEST_URI']);
		$alias = $alias_all[count($alias_all)-1];
		if (strpos($alias,'-') || strpos($alias,'.') || strpos($alias,'?')) {
			$alias_params = explode('-',$alias);
			$alias = $alias_params[0];
			if (strpos($alias,'.')) {
				$alias = substr($alias,0,strpos($alias,'.'));
			}
			if (strpos($alias,'?')) {
				$alias = substr($alias,0,strpos($alias,'?'));
			}
		}
		return $alias;
	}
	
	/**
	* 当前是否为某导航
	* alias 别名　如 home 
	*/
	static function isCurrentNav($alias){
		$menu = MulanDBUtil::getObjectBySql('select id,link,`type` from #__menu where ( published = 1 and alias='.MulanDBUtil::dbQuote($alias).') limit 1');
		$Itemid  = JRequest::getVar('Itemid');
		if($Itemid==$menu->id){
			return $Itemid;
		}
		return false;
	}
	
	/**
	 * 整理为多维数组
	 */
	static function arrangeArrayByTwoElement($key_name,$arrange_name,$parent,$arr,&$notin) {
		if (count($arr)) {
			$result = array();
			foreach ($arr as $a) {
				if (isset($a->$key_name) && !in_array($a->$key_name,&$notin) && $a->$arrange_name == $parent) {
					$get_parent = $a->$key_name;
					$notin[] = $a->$key_name;
					$result[$a->$key_name] = MulanHTMLUtil::arrangeArrayByTwoElement($key_name,$arrange_name,$get_parent,$arr,&$notin);
				}
			}
		}
		return $result;
	}
	
	/**
	 * 整理为多维数组
	 */
	static function inputArrayBySomeElement($set_name,$arrange_name,$arr,$tar_arr) {
		if (count($arr) && count($tar_arr)) {
			$result = array();
			foreach ($tar_arr as $ta_key=>$ta) {
				$tar_arr[$ta_key] = array();
				foreach ($arr as $a2) {
					if ($a2->$arrange_name == $ta_key) {
						$result[$ta_key][] = $a2->$set_name;
					}
				}
			}
		}
		return $result;
	}
	
	static function arrangeArrayBySomeElement($arrange_name,$arr) {
		if (count($arr)) {
			$result = array();
			foreach ($arr as $a) {
				$result[$a->$arrange_name] = 1;
			}
		}
		return $result;
	}
	
	static function isIE6()
	{
		if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') !== false )
		{
			return true;
		}
		else
		{
			return false;
		}

	}
	
	/**
	 * 调整数组的key为指定值
	 */
	static function arrangeArrayKey($key_name,$arr) {
		if (count($arr)) {
			$new_arr = array();
			foreach ($arr as $a) {
				$new_arr[$a->$key_name] = $a;
			}
		}
		return $new_arr;
	}
	
	/**
	 * 替换文章关键词并增加链接！
	 */
	function replaceKeyWorks( $content ) {
		if ( JString::strpos( $content, '{sitelinkxoff}' ) !== false ) {
			$content = str_replace('{sitelinkxoff}','',$content);
			return true;
		}
		$wurdeersetzt = 0;
		$wieoft = 0;
		$daba =& JFactory::getDBO();
		
		$anfr = "SELECT * FROM `#__sitelinkx_config`";
		$daba->setQuery($anfr);
		$ergeb = $daba->loadObjectList();
		$hinweis = $ergeb[0]->hinweis;
		$rel = '';
		if($hinweis == 1) {
			$rel = 'rel="nofollow"';
		}
		$anzahl = $ergeb[0]->anzahl;
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM `#__sitelinkx` ORDER BY wort";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$count = count( $rows );
		$suche = '`<[^>]*>`';
		$derzz =& JFactory::getDate()->toFormat();
		
		for($i = 0 ; $i<$count ; $i++) {
			if ($rows[$i]->endpub == "0000-00-00 00:00:00") $rows[$i]->endpub ="9999-12-31 23:59:59";
			switch($derzz) {
				case ($derzz < $rows[$i]->begpub);
					break;
				case ($derzz > $rows[$i]->endpub);
					break;
				default:
					$ausgabe = preg_split($suche, $content, -1, PREG_SPLIT_OFFSET_CAPTURE);
					preg_match_all($suche, $content, $anker);
					$de = $rows[$i]->anzahl;
					$wort = $rows[$i]->wort;
					$wordboundary = '';
					if ($rows[$i]->suchm == '0') $wordboundary = '([><\s.?!:,;\'=~_"/(){}\[\]\\\\]+)'; //changed by Marc: local and unicode-independent word boundary
					
					/* start inserted Marc Mittag for wordboundary */
					$search_arr = array();
					$search_arr[] = '`'.$wordboundary.$wort.$wordboundary.'`';
					$search_arr[] = '`'.$wordboundary.$wort.'$`';
					$search_arr[] = '`^'.$wort.$wordboundary.'`';
					$search_arr[] = '`^'.$wort.'$`';
					$ersatz = '<a class="sitelinkx" href="'.$rows[$i]->ersatz.'" title="'.$rows[$i]->schlagwort.'" target="'.$rows[$i]->fenster.'" '.$rel.' >'.$wort.'</a>';
					$replace_arr = array();
					$replace_arr[] = '\\1'.$ersatz.'\\2';
					$replace_arr[] = '\\1'.$ersatz;
					$replace_arr[] = $ersatz.'\\1';
					$replace_arr[] = $ersatz;
					/* end inserted Marc Mittag for wordboundary */
					$insgesamt = 0;					
					for ($j=0; $j < count($ausgabe); $j++) {
						if($insgesamt < $anzahl) {
						  $ausgabe[$j][0] = preg_replace( $search_arr, $replace_arr, $ausgabe[$j][0], $anzahl, $wieoft );
						  $insgesamt += $wieoft;
						}
						if ($wieoft > 0) {
							$wurdeersetzt = 1;
						}
					}
					
					$zusammen = '';
					for ($k=0; $k < count($ausgabe); $k++) {
						$anker_hinzu = "";
						if(isset($anker[0][$k])) {
							$anker_hinzu = $anker[0][$k];
						}
						$zusammen = $zusammen . $ausgabe[$k][0] . $anker_hinzu; 
					}
					$content = $zusammen;
					
					$wort='';
					$linkwort='';
					$ersatz='';
					$ausgabe='';
					$zusammen='';
			}
		}
		return $content;
	}
	
	static function getImgsInString($str) {
		$list = array(); //这里存放结果map
		$c1 = preg_match_all('/<img\s.*?>/', $str, $m1); //先取出所有img标签文本 
		for($i=0; $i<$c1; $i++) { //对所有的img标签进行取属性 
		    $c2 = preg_match_all('/(\w+)\s*=\s*(?:(?:(["\'])(.*?)(?=\2))|([^\/\s]*))/', $m1[0][$i], $m2); //匹配出所有的属性 
		    for($j=0; $j<$c2; $j++) { //将匹配完的结果进行结构重组 
		        $list[$i][$m2[1][$j]] = !empty($m2[4][$j]) ? $m2[4][$j] : $m2[3][$j];
		    }
		}
		return $list;
	}
}
?>