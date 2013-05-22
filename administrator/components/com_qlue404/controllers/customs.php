<?php 

// no direct access
defined('_JEXEC') or die('Restricted Access');

// import joomla controller admin library
jimport('joomla.application.component.controlleradmin');

class Qlue404ControllerCustoms extends JControllerAdmin {
			
	public function getModel($name = 'Custom', $prefix = 'Qlue404Model') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
}

?>