<?php
/**
 * @version		$Id: rssfeeds.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_rssfeed
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Rssfeed Component Rssfeeds Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_rssfeed
 * @since 1.6
 */
class RssfeedCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__rssfeed';
		$options['extension'] = 'com_rssfeed';
		parent::__construct($options);
	}
}
