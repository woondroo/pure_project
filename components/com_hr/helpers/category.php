<?php
/**
 * @version		$Id: hrs.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Hr Component Hrs Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @since 1.6
 */
class HrCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__hr';
		$options['extension'] = 'com_hr';
		parent::__construct($options);
	}
}
