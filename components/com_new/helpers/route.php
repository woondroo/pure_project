<?php
/**
 * @version		$Id: route.php2012-01-11 09:44:48
 * @package		Joomla.Site
 * @subpackage	com_new
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * New Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_new
 * @since 1.5
 */
abstract class NewHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the new
	 */
	public static function getNewRoute($id, $catid)
	{
		$needles = array(
			'new'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_new&view=new&pid='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('New');
			$news = $categories->get($catid);

			if($news) {
				$needles['news'] = array_reverse($news->getPath());
				$needles['categories'] = $needles['news'];
				$link .= '&id='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		else if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param	int		$id		The id of the new.
	 * @param	string	$return	The return page variable.
	 */
	public static function getFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id) {
			$link = 'index.php?option=com_new&task=new.edit&w_id='. $id;
		}
		else {
			$link = 'index.php?option=com_new&task=new.add&w_id=0';
		}

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	public static function getNewsRoute($catid)
	{
		if ($catid instanceof JNewsNode) {
			$id = $catid->id;
			$news = $catid;
		}
		else {
			$id = (int) $catid;
			$news = JCategories::getInstance('New')->get($id);
		}

		if ($id < 1) {
			$link = '';
		}
		else {
			$needles = array(
				'news' => array($id)
			);

			if ($item = self::_findItem($needles)) {
				$link = 'index.php?Itemid='.$item;
			}
			else {
				//Create the link
				$link = 'index.php?option=com_new&view=news&id='.$id;

				if ($news) {
					$catids = array_reverse($news->getPath());
					$needles = array(
						'news' => $catids,
						'categories' => $catids
					);

					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					}
					else if ($item = self::_findItem()) {
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_new');
			$items		= $menus->getItems('component_id', $component->id);
			
			if ($items) {
				foreach ($items as $item)
				{
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];
	
						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}
	
						if (isset($item->query['id'])) {
							self::$lookup[$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else {
			$active = $menus->getActive();
			if ($active) 
			{
				return $active->id;
			}
		}

		return null;
	}
}
