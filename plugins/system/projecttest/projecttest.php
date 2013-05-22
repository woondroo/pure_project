<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

class plgSystemProjecttest extends JPlugin {
	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}
	
	function onAfterDispatch() {
		jimport('joomla.html.parameter');
		$plugin = JPluginHelper::getPlugin( 'system', 'projecttest' );
		$params = new JParameter( $plugin->params );
		
		$s = JFactory::getSession();
		$testtipfirst = $s->get('testtipfirst');
		
		$expire = $params->get('expiredata');
		$showtype = $params->get('options');
		$db = JFactory::getDBO();
		$query = 'select `id` from #__menu where home=1 limit 1';
		$db->setQuery($query);
		$menu = $db->loadObject();
		$itemid = JRequest::getVar('Itemid');
		$ishome=false;
		if($menu->id&&$itemid&&$itemid==$menu->id){
			$ishome=true;	
		}
		$isshow=$showtype=='0'?(!$testtipfirst):($showtype=='1'?'1':$ishome);//如果是０则看是否为第一次访问，如果是１一定要显示，如果是２,刚看是不是首页
		
		if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$expire)&&(!preg_match("/administrator.index.php$/", $_SERVER[SCRIPT_NAME]))){
			$expiredate = strtotime($expire);
			$now	=	mktime();
			if(($now-$expiredate-24*60*60)>0){//到期
				$makesure = $params->get('makesure');
				$path = JURI::root().'plugins/system/projecttest/expire.php?url='.urlencode($makesure);
				$app =& JFactory::getApplication();
				$app->redirect($path);
			}else if($isshow){
				$s->set('testtipfirst',true);
				$haveday = (int)(($expiredate-$now+24*60*60)/(24*60*60)+1);
				$app =& JFactory::getDocument();
				$app->addScript(JURI::root().'plugins/system/projecttest/jquery.js');
				//$app->addScript(JURI::root().'plugins/system/projecttest/projectjs.js');
				$makesure = $params->get('makesure');
				$advise = $params->get('advise');
				$mshtml = '';
				if(preg_match('/^(http(s)?:\/\/)(\S)+$/',$makesure)){
					$mshtml = '<div style="padding-top:10px;padding-left:75px;"><a href="'.$makesure.'" id="testpastbut" target="_blank">&nbsp;</a></div>';
				}
				$adhtml = '';
				if(preg_match('/^(http(s)?:\/\/)(\S)+$/',$advise)){
					$adhtml = '<div style="padding-left:75px;"><a href="'.$advise.'" id="testnopast" target="_blank">&nbsp;</a></div>';
				}
				$baseurl = JURI::root();
				closetippanel;
				$app->addStyleDeclaration('#closetippanel{background:url('.$baseurl.'plugins/system/projecttest/testclose.jpg) no-repeat;}#closetippanel:hover{background-position:-16px 0;}#testpastbut{width:189px;height:26px;display:block;background:url('.$baseurl.'/plugins/system/projecttest/testyes.jpg) no-repeat;}#testpastbut:hover{background-position:-189px 1px;}#testnopast{width:189px;height:26px;display:block;background:url('.$baseurl.'/plugins/system/projecttest/testno.jpg) no-repeat;}#testnopast:hover{background-position:-189px -1px;}');
				$app->addScriptDeclaration(
				'
				jQuery(function(){
					addTestTip();
				});
				function addTestTip(){
					var div = jQuery(\'<div style="overflow:hidden;z-index:9999;width:276px;height:166px;right:20px;top:-166px;position:absolute;bottom:2px;background:#fff url('.$baseurl.'plugins/system/projecttest/testbox.jpg) no-repeat;"><div style="height:20px;padding-top:3px;"><a style="float:right;padding-right:2px;width:14px;height:16px;cursor:pointer;display:block;" id="closetippanel" title="关闭">&nbsp;</a></div><div style="padding-top:30px;padding-left:10px;color:#111;">测试截止到'.$expire.'，请尽快完成测试。</div><div style="text-align:center;color:#111;padding-top:10px;">还有<span style="color:red;">'.$haveday.'</span>天</div><div style="padding-top:10px;">'.$mshtml.$adhtml.'</div></div>\');
					jQuery(\'body:first\').append(div);
					div.find(\'#closetippanel\').click(function(){
						div.animate({top:"-166px"},2000);
					});
					div.animate({top:\'20px\'},2000);
				}
				'
				);
			}
		}
	}
}
?>