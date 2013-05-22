<?php
/**
 * @version		$Id: route.php2012-01-10 04:08:46
 * @package		Joomla.Site
 * @subpackage	com_download
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Download Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_download
 * @since 1.5
 */
abstract class DownloadHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the download
	 */
	public static function getDownloadRoute($id, $catid)
	{
		$needles = array(
			'download'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_download&view=download&pid='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('Download');
			$downloads = $categories->get($catid);

			if($downloads) {
				$needles['downloads'] = array_reverse($downloads->getPath());
				$needles['categories'] = $needles['downloads'];
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
	 * @param	int		$id		The id of the download.
	 * @param	string	$return	The return page variable.
	 */
	public static function getFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id) {
			$link = 'index.php?option=com_download&task=download.edit&w_id='. $id;
		}
		else {
			$link = 'index.php?option=com_download&task=download.add&w_id=0';
		}

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	public static function getDownloadsRoute($catid)
	{
		if ($catid instanceof JDownloadsNode) {
			$id = $catid->id;
			$downloads = $catid;
		}
		else {
			$id = (int) $catid;
			$downloads = JCategories::getInstance('Download')->get($id);
		}

		if ($id < 1) {
			$link = '';
		}
		else {
			$needles = array(
				'downloads' => array($id)
			);

			if ($item = self::_findItem($needles)) {
				$link = 'index.php?Itemid='.$item;
			}
			else {
				//Create the link
				$link = 'index.php?option=com_download&view=downloads&id='.$id;

				if ($downloads) {
					$catids = array_reverse($downloads->getPath());
					$needles = array(
						'downloads' => $catids,
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

			$component	= JComponentHelper::getComponent('com_download');
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
