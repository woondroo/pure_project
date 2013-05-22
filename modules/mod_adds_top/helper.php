<?php
/**
 * @version		$Id: helper.php 21766 2011-07-08 12:20:23Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	mod_articles_archive
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modAddsTopHelper
{
	static function hasShow()
	{
		//get database
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__addsmanager');
		date_default_timezone_set ('Asia/Shanghai');

		$query->where('"'.date('Y-m-d H:i:s').'"> startdate AND "'.date('Y-m-d H:i:s').'" < enddate AND catid = 108 AND published = 1 ORDER BY ordering ASC,id DESC limit 1');
		$db->setQuery($query);
		$item = $db->loadObject();

		if($item->id)return $item;
		else return null;
		
	}
}
