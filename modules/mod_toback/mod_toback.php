<?php
/**
 * @version		$Id: mod_toback.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_toback
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';

$start = modTobackHelper::getStart($params);
require JModuleHelper::getLayoutPath('mod_toback', $params->get('layout', 'default'));