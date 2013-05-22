<?php
/**
 * @version		$Id: integral.php 2012-05-19 13:25:06
 * @subpackage	com_integral
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';

$controller	= JController::getInstance('Integral');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
