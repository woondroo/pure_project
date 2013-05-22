<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * Integral_history Controller
 */
class Integral_historyControllerIntegral_history extends JControllerForm
{
	public function cancel() {
		$this->setRedirect('index.php?option=com_integral_history&view=integral_historys');
	}
	
	public function save($data) {
		parent::save($data);
		$task = JRequest::getVar('task');
		if ($task == 'save') {
			$this->setRedirect('index.php?option=com_integral_history&view=integral_historys');
		}
	}
}
