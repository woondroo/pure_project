<?php
/**
 * @version		$Id: default.php 2011-11-21 08:38:16
 * @package		Joomla.Site
 * @subpackage	com_product
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="home-others">
	<?php
	echo MulanDBUtil::loadmod('mod_homedetail','home-company');
	echo MulanDBUtil::loadmod('mod_homelist','home-products');
	echo MulanDBUtil::loadmod('mod_homelist','home-news');
	?>
</div>
