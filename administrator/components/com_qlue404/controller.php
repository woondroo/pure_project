<?php

// no direct access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

class Qlue404Controller extends JController {
			
	function display($cachable = false) {
		
		// check if our 404 plugin is enabled
		if(!JPluginHelper::isEnabled('system', 'qlue404')) {
			JError::raiseNotice(500, JText::_('COM_QLUE404_PLUGIN_DISABLED'));	
		}	
					
		// set the default view if one is not set
		JRequest::setVar('view', JRequest::getCmd('view', 'customs'));
		parent::display($cachable);
	}	
	
}

?>