<?php
/**
 * @version		$Id: helper.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_breadcrumbs
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modBreadCrumbsHelper
{
	public static function getList(&$params)
	{
		// Get the PathWay object from the application
		$app		= JFactory::getApplication();
		$pathway	= $app->getPathway();
		$items		= $pathway->getPathWay();

		$count = count($items);
		for ($i = 0; $i < $count; $i ++)
		{
			$items[$i]->name = stripslashes(htmlspecialchars($items[$i]->name, ENT_COMPAT, 'UTF-8'));
			$items[$i]->link = JRoute::_($items[$i]->link);
		}

		if ($params->get('showHome', 1))
		{
			$item = new stdClass();
			$item->name = htmlspecialchars($params->get('homeText', JText::_('MOD_BREADCRUMBS_HOME')));
			$item->link = JRoute::_('index.php?Itemid='.$app->getMenu()->getDefault()->id);
			array_unshift($items, $item);
		}
		
		/**
		 * 2012-01-12 wengebin 增加！
		 * 该段代码可以给以分类为菜单的组件提供面包屑
		 */
		$cateid = JRequest::getVar('id');
		if ($cateid) {
			$option = JRequest::getVar('option');
			$view = JRequest::getVar('view');
			$itemid = JRequest::getVar('Itemid');
			$start = 0;
			$link = 'index.php?option='.$option.'&view='.$view.'&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid;
			
			jimport('mulan.mldb');
			$get_cate = MulanDBUtil::getObjectBySql('select * from #__categories where id='.MulanDBUtil::dbQuote($cateid));
			if ($get_cate->title && $option != 'com_content') {
				$item = new stdClass();
				$item->name = htmlspecialchars($get_cate->title);
				$item->link = JRoute::_($link);
				array_push($items, $item);
			}
			
			$pid = JRequest::getVar('pid');
			if ($pid) {
				$get_pro = MulanDBUtil::getObjectBySql('select * from #__'.str_replace('com_','',$option).' where id='.MulanDBUtil::dbQuote($pid));
				if ($get_pro->title) {
					$item = new stdClass();
					$item->name = htmlspecialchars($get_pro->title);
					$item->link = JRoute::_($link.'&pid='.$pid);
					array_push($items, $item);
				}
			}
		}

		return $items;
	}

	/**
	 * Set the breadcrumbs separator for the breadcrumbs display.
	 *
	 * @param	string	$custom	Custom xhtml complient string to separate the
	 * items of the breadcrumbs
	 * @return	string	Separator string
	 * @since	1.5
	 */
	public static function setSeparator($custom = null)
	{
		$lang = JFactory::getLanguage();

		// If a custom separator has not been provided we try to load a template
		// specific one first, and if that is not present we load the default separator
		if ($custom == null) {
			if ($lang->isRTL()){
				$_separator = JHtml::_('image','system/arrow_rtl.png', NULL, NULL, true);
			}
			else{
				$_separator = JHtml::_('image','system/arrow.png', NULL, NULL, true);
			}
		} else {
			$_separator = htmlspecialchars($custom);
		}

		return $_separator;
	}
}
