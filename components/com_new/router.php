<?php
/**
 * @version		$Id: router.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_new
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

 /* New Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_new
 * @since 1.6
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_new component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function NewBuildRoute(&$query)
{
	$segments = array();

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$params		= $app->getParams('com_new');
	$advanced	= $params->get('sef_advanced_link', 0);

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}

	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	if (isset($query['view'])) {
		$view = $query['view'];
		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}
		unset($query['view']);
	} else {
		$view = $mView;
	}

	if (isset($view) and ($view == 'news' or $view == 'new' )) {
		if (isset($query['id'])) {
			$catid = $query['id'];
		} else {
			$catid = $mId;
		}

		$categories = JCategories::getInstance('New');
		$news = $categories->get($catid);
		if($news){
			$path = $news->getPath();
			$path = array_reverse($path);
			
			$array = array();
			foreach($path as $id) {
				if ($id) {
					$aliaslink = explode(':', $id);
					if ($view == 'new' && $query['pid']) {
						$array[] = $aliaslink[1];
						if($query['start']) {
							unset($query['start']);
						} else if($query['limitstart']==0) {
							unset($query['limitstart']);
						}
					} else {
						if($query['start']) {
							$array[] = $aliaslink[1].'-'.floor($query['start']/$params->get('limit')+1);
							unset($query['start']);
						} else if($query['limitstart']==0) {
							$array[] = $aliaslink[1].'-1';
							unset($query['limitstart']);
						} else {
							$array[] = $aliaslink[1].'-1';
						}
					}
				}
			}
			$segments = array_merge($segments, array_reverse($array));
		}

		if ($view == 'new') {
			if ($advanced) {
				list($tmp, $pid) = explode(':', $query['pid'], 2);
			} else {
				$pid = $query['pid'];
			}
			
			if ($pid) {
				if ($query['alias']) {
					$pid .= '-'.$query['alias'];
				}
				unset($query['alias']);
				$segments[] = $pid;
			}
		}
		
		unset($query['view']);
		unset($query['id']);
		unset($query['pid']);
	}

	if (isset($query['layout'])) {
		if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {
				unset($query['layout']);
			}
		}
		else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	};

	return $segments;
}
/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function NewParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params	= $app->getParams('com_new');
	$advanced = $params->get('sef_advanced_link', 0);
	$seg_last = explode(':',$segments[count($segments)-1]);
	$seg_last_num = $seg_last[0];
	
	$is_tolist = true;
	if(is_numeric($seg_last_num) && count($segments) >= 2 && $segments[count($segments)-2]) {
		$is_tolist = false;
		$segmentStr = str_replace(':','-',$segments[count($segments)-2]);
		$cateid = MulanDBUtil::getObjectBySql('SELECT id FROM #__categories WHERE extension="com_new" and alias ="'.$segmentStr.'" ')->id;
		if ($cateid) {
			$vars['id'] = $cateid;
		}
		
		if ($vars['id']) {
			$vars['view'] = 'new';
			
			$obj = MulanDBUtil::getObjectBySql('SELECT id,catid FROM #__new WHERE id ="'.$seg_last_num.'" ');
			$vars['pid'] = $obj ? $obj->id : 0;
		} else {
			$is_tolist = true;
		}
		
		if (!$vars['id'] || !$vars['pid']) {
			header('location: '.MulanHtmlUtil::getUrlByAlias('404'));
		}
		
		return $vars;
	}
	
	if ($is_tolist == true) {
		$segmentsStr = explode(':',$segments[count($segments)-1]);
		$segmentsStr_num = $segmentsStr[count($segmentsStr)-1];
		array_pop($segmentsStr);
		$segmentsStr_alias = implode('-',$segmentsStr);
		$vars['limitstart'] = ($segmentsStr_num-1)*$params->get('limit');
		$cateid = MulanDBUtil::getObjectBySql('SELECT id FROM #__categories WHERE extension="com_new" and alias ="'.$segmentsStr_alias.'" ')->id;
		$vars['view'] = 'news';
		$vars['id']	= $cateid;
		
		if (!$vars['id']) {
			header('location: '.MulanHtmlUtil::getUrlByAlias('404'));
		}
		
		return $vars;
	}
}