<?php

//no direct access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.base.object');

class Qlue404Helper extends JObject {
		
	public static function getActions() {
			
		$user		= JFactory::getUser();
		$result		= new JObject();
		$assetName	= 'com_qlue404';

		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete');

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}		
}
?>