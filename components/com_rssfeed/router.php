<?php
/**
 * @version		$Id: router.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_rssfeed
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

 /* Rssfeed Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_rssfeed
 * @since 1.6
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_rssfeed component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function RssfeedBuildRoute(&$query)
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
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
	}

	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
	$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	if (isset($query['view'])) {
		$view = $query['view'];

		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}

		unset($query['view']);
	}
	
	$array = array();
	if($query['start']) {
		$array[] = 'news-'.floor($query['start']/5+1);
		unset($query['start']);
	} else if (array_key_exists('limitstart',$query)) {
		$array[] = 'news-1';
		unset($query['limitstart']);
	}
	if (count($array)) {
		$segments = array_merge($segments, array_reverse($array));
	}
	
	if (isset($view) and ($view == 'rssfeeds' or $view == 'rssfeed' )) {
		if ($mId != intval($query['id']) || $mView != $view) {
			if ($view == 'rssfeed' && isset($query['catid'])) {
				$catid = $query['catid'];
			}
			else if (isset($query['id'])) {
				$catid = $query['id'];
			}

			$menuCatid = $mId;
			$categories = JCategories::getInstance('Rssfeed');
			$rssfeeds = $categories->get($catid);
			
			if ($advanced) {
				list($tmp, $id) = explode(':', $query['id'], 2);
			} else {
				$id = $query['id'];
			}
			$segments[] = $id;
		}

		unset($query['id']);
		unset($query['catid']);
	}
	
	if (isset($query['layout'])) {
		if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {
				unset($query['layout']);
			}
		} else {
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
function RssfeedParseRoute($segments)
{
	$vars = array();
	
	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = $app->getParams('com_rssfeed');
	$advanced = $params->get('sef_advanced_link', 0);
	
	if(!is_numeric($segments[count($segments)-1]))
	{
		$segmentsStr = explode(':',$segments[count($segments)-1]);
		$vars['limitstart'] = ($segmentsStr[1]-1)*5;
		$cateid = MulanDBUtil::getObjectBySql('SELECT id FROM #__categories WHERE alias ="'.$segmentsStr[0].'" ')->id;
		$vars['view'] = 'rssfeeds';
		$vars['id']	= $cateid;
		return $vars;
	} else {
		$vars['view'] = 'rssfeed';
		
		if (count($segments) > 2 && $segments[count($segments)-2]) $cateid = MulanDBUtil::getObjectBySql('SELECT id FROM #__categories WHERE extension="com_rssfeed" and alias ="'.$segments[count($segments)-2].'" ')->id;
		if ($cateid) {
			$vars['id'] = $cateid;
		} else {
			$vars['id'] = MulanDBUtil::getObjectBySql('SELECT catid FROM #__rssfeed WHERE id ="'.$segments[count($segments)-1].'" ')->catid;
		}
		
		$vars['pid'] = $segments[count($segments)-1];
		return $vars;
	}
}