<?php
/**
 * @version		$Id: news.php 2012-01-11 09:44:48
 * @package		Joomla.Site
 * @subpackage	com_new
 * @copyright	Copyright (C) 2008 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * New Component News Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_new
 * @since 1.6
 */
class NewCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__new';
		$options['extension'] = 'com_new';
		parent::__construct($options);
	}
}
