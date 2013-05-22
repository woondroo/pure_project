<?php
/**
 * @version		$Id: helper.php 20926 2011-03-09 06:59:31Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_feed
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('mulan.mldb');
class modBannerIndexHelper
{
	static function getAnimationData()
	{
		$sql = 'SELECT * FROM #__bannerindex WHERE published = 1 ORDER BY ordering ASC ,id DESC limit 0,7';
		$items = MulanDBUtil::getObjectlistBySql($sql);
		return $items;
	}
}