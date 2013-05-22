<?php

// no direct access
defined('_JEXEC') or die('Restricted Access'); 

jimport('joomla.application.component.controller');

class Qlue404Controller extends JController {
		
	public function __construct($config = array()) {
		if(!array_key_exists('base_path', $config)) {
			$config['base_path'] = JPATH_ROOT.DS.'components'.DS.'com_qlue404';
		}	
		parent::__construct($config);
	}
				
	public function display($cachable = false, $urlparams = false) {
		JRequest::setVar('view', 'qlue404');
		return parent::display();
	}
	
}

?>
