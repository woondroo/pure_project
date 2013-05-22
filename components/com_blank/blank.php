<?php
/**
 * @version		$Id: blank.php 2011-11-21 08:38:16
 * @package		Joomla.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2008 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Blank');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
