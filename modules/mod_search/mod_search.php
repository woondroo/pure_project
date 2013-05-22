<?php
/**
 * @version		$Id: mod_search.php 21597 2011-06-21 13:14:15Z chdemko $
 * @package		Joomla.Site
 * @subpackage	mod_search
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

jimport('mulan.mltools');
$lang = JFactory::getLanguage();

$upper_limit = $lang->getUpperLimitSearchWord();

$button			= $params->get('button', '');
$button_text	= htmlspecialchars($params->get('button_text', ''));
$maxlength		= $upper_limit;
$text			= htmlspecialchars($params->get('text', JText::_('MOD_SEARCH_SEARCHBOX_TEXT')));
$set_Itemid		= intval($params->get('set_itemid', 0));
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$left			= $params->get('left', '0px');
$right			= $params->get('right', 0);
$top			= $params->get('top', '0px');
$bottom			= $params->get('bottom', 0);

$width			= $params->get('width', '230px');
$height 		= $params->get('height', '30px');
$inputwidth		= $params->get('inputwidth', '200px');
$inputheight	= $params->get('inputheight', '20px');

$inputbg		= $params->get('inputbg');
$inputpadding	= $params->get('inputpadding', '10px');
$input_bg_pos	= MulanToolsUtil::getMaterialPos($inputbg);

$search_bt_bg	= $params->get('search_bt_bg');
$search_bg_pos	= MulanToolsUtil::getMaterialPos($search_bt_bg);
$searchbg		= $search_bg_pos['img'];

$search_style = array();
array_push($search_style, 'width:'.$width);
array_push($search_style, 'height:'.$height);
array_push($search_style, $right ? 'right:'.$right : 'left:'.$left);
array_push($search_style, $bottom ? 'bottom:'.$bottom : 'top:'.$top);

$input_style = array();
array_push($input_style, 'width:'.(intval($inputwidth)-intval($inputpadding)).'px');
array_push($input_style, 'height:'.$inputheight);

$input_outer_style = array();
array_push($input_outer_style, 'width:'.(intval($inputwidth)-intval($inputpadding)).'px');
array_push($input_outer_style, 'height:'.$inputheight);
array_push($input_outer_style, 'padding:'.((intval($height)-intval($inputheight))/2).'px 0 '.((intval($height)-intval($inputheight))/2).'px '.$inputpadding);
if ($searchbg) array_push($input_outer_style, 'background:url('.JURI::base().$searchbg.') '.(-1*$input_bg_pos['x']).'px '.(-1*$input_bg_pos['y']).'px');

$button_style = array();
array_push($button_style, 'width:'.(intval($width)-intval($inputwidth)).'px');
array_push($button_style, 'height:'.$height);
if ($searchbg) array_push($button_style, 'background:url('.JURI::base().$searchbg.') '.(-1*$search_bg_pos['x']).'px '.(-1*$search_bg_pos['y']).'px');

$insert_style = '.main-search{'.implode(';',$search_style).'}#search_key_words_container{'.implode(';',$input_outer_style).'}#search_key_words{'.implode(';',$input_style).'}#search-submit{'.implode(';',$button_style).'}'.($searchbg ? '#search-submit:hover{background-position:'.(-1*$search_bg_pos['x']).'px '.(-1*$search_bg_pos['y'] - intval($height)).'px' : '').'}';
$document = JFactory::getDocument();
$document->addStyleDeclaration($insert_style);

$mitemid = $set_Itemid > 0 ? $set_Itemid : JRequest::getInt('Itemid');
require JModuleHelper::getLayoutPath('mod_search', $params->get('layout', 'default'));
