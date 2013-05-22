<?php
/**
 * @version		$Id: helper.php 22152 2011-09-25 18:52:19Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_homedetail
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modHomedetailHelper
{
	static function getItem($params)
	{
		$result = MulanDBUtil::getObjectBySql('select * from #__switchcontent where published=1 and catid='.$params->get('catid').' order by ordering,id desc limit 1');
		return $result;
	}
}
