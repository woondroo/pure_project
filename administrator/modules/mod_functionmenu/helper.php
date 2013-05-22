<?php
/**
 * @version		$Id: helper.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @functionpackage	mod_functionmenu
 * @since		1.6
 */
abstract class modFunctionmenuHelper
{
	/**
	 * Get the member items of the functionmenu.
	 *
	 * @return	mixed	An arry of menu items, or false on error.
	 */
	public static function getItems()
	{

		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__categories');
		$query->where('published = 1 AND parent_id = 1 AND extension="com_aclmanager"');
		$db->setQuery((string)$query);
		$list = $db->loadObjectList();

		if (!is_array($list) || !count($list)) {
			return false;
		}

		return $list;
	}

}