<?php
/**
 * @version		$Id: download.php 2012-01-10 04:08:46
 * @package		Joomla.Site
 * @subpackage	com_download
 * @copyright	Copyright (C) 2008 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';

$controller	= JController::getInstance('Download');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
