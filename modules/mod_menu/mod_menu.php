<?php
/**
 * @version		$Id: mod_menu.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
jimport('mulan.mltools');

$base			= JURI::base();
$list			= modMenuHelper::getList($params);
$app			= JFactory::getApplication();
$menu			= $app->getMenu();
$active			= $menu->getActive();
$active_id 		= isset($active) ? $active->id : $menu->getDefault()->id;
$path			= isset($active) ? $active->tree : array();
$showAll		= $params->get('showAllChildren');
$class_sfx		= htmlspecialchars($params->get('class_sfx'));
$startLevel 	= $params->get('startLevel',1);
$endLevel 		= $params->get('endLevel',100);
$endLevel 		= $endLevel == 0 ? 100 : $endLevel;
$menutype		= $params->get('menutype');
$childtitle		= $params->get('childtitle');

$mainmenu_css_type = $params->get('mainmenu_css_type');
$mainmenupos 	= $params->get('mainmenupos',1);
$mainmenualign 	= $params->get('mainmenualign',1);
$mainmenuopen 	= $params->get('mainmenuopen',1);
$mainmenuhide 	= $params->get('mainmenuhide');
$mainmenufade 	= $params->get('mainmenufade');
$mainmenuoffset = $params->get('mainmenuoffset');
$manimtime		= $params->get('manimtime');
$mbg 			= $params->get('mbg');
$mbg_pos		= MulanToolsUtil::getMaterialPos($mbg);
$mbg			= $mbg_pos['img'];
$musebg 		= $params->get('musebg');
$mmargin 		= $params->get('mmargin');
$mwidth 		= $params->get('mwidth');
$mheight 		= $params->get('mheight');
$mcbg 			= $params->get('mcbg');
$mcbg_pos		= MulanToolsUtil::getMaterialPos($mcbg);
$mcbg			= $mcbg_pos['img'];
$mcusebg 		= $params->get('mcusebg');
$mcmargin 		= $params->get('mcmargin');
$mcwidth 		= $params->get('mcwidth');
$mcheight 		= $params->get('mcheight');
$mcbreak		= $params->get('mcbreak');
$mcbreakwidth	= $params->get('mcbreakwidth');

$mctopbg 		= $params->get('mctopbg');
$mctopbg_pos	= MulanToolsUtil::getMaterialPos($mctopbg);
$mctopbg		= $mctopbg_pos['img'];
$mctopheight 	= $params->get('mctopheight');
$mctopm 		= $params->get('mctopm');
$mainmenu_child_top_bg_str = '';
$mainmenu_child_top_style = '';
$show_mct = $menutype == 'mainmenu' && $mctopbg && intval($mctopheight) > 0;
if ($show_mct) {
	$mainmenu_child_top_style .= '#'.$mainmenu_css_type.' .menu li ul #mc-top{top:'.$mctopm.';height:'.$mctopheight.';background-image:url('.$base.$mctopbg.');background-position:center '.(-1*$mctopbg_pos['y']).'px;}';
	$mainmenu_child_top_bg_str .= '<li id="mc-top"></li><div class="clr"></div>';
}

$showmenutitle 		= $params->get('showmenutitle');
$showmenuchildtitle = $params->get('showmenuchildtitle');
$showmenupre 		= $params->get('showmenupre');
$menupre 			= $params->get('menupre');
$showchildmenupre 	= $params->get('showchildmenupre');
$childmenupre 		= $params->get('childmenupre');
$showchild2menupre 	= $params->get('showchild2menupre');
$child2menupre 		= $params->get('child2menupre');
$showchildtoptitle 	= $params->get('showchildtoptitle');
$mainmenualign 		= $params->get('mainmenualign');
$toptitlebg			= $params->get('toptitlebg');
$toptitlebg_pos		= MulanToolsUtil::getMaterialPos($toptitlebg);
$toptitlebg			= $toptitlebg_pos['img'];
$toptitleprebg		= $params->get('toptitleprebg');
$toptitleprebg_pos	= MulanToolsUtil::getMaterialPos($toptitleprebg);
$toptitleprebg		= $toptitleprebg_pos['img'];

if(count($list)) {
	require JModuleHelper::getLayoutPath('mod_menu', $params->get('layout', 'default'));
}