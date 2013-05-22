<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of Comcreater component
 */
class ComcreaterController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'Comcreaters'));
 
		// call parent behavior
		parent::display($cachable);
 
		// Set the submenu
		ComcreaterHelper::addSubmenu('messages');
	}
	
	public function checkNameByAjax()
	{
		$filePath = JPATH_ROOT.'/administrator/components/com_'.JRequest::getVar('comname');
		echo $this->checkName($filePath);exit;
		
	}
	
	protected function checkName($folder_url)
	{
		if (!is_dir($folder_url)) {
			return true;
		} else {
			return false;
		}
	}

}
