<?php
/**
 * @version		$Id: downloads.php 2012-01-10 04:08:46
 * @package		Joomla.Site
 * @subpackage	com_download
 * @copyright	Copyright (C) 2008 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Download Component Downloads Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_download
 * @since 1.6
 */
class DownloadCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__download';
		$options['extension'] = 'com_download';
		parent::__construct($options);
	}
}
