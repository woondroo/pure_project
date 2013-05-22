<?php
/**
 * @version		$Id: products.php 2012-01-11 06:49:50
 * @package		Joomla.Site
 * @subpackage	com_product
 * @copyright	Copyright (C) 2008 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Product Component Products Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_product
 * @since 1.6
 */
class ProductCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__product';
		$options['extension'] = 'com_product';
		parent::__construct($options);
	}
}
