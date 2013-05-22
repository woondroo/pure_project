<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Aclmanagers Controller
 */
class AclmanagerControllerAclcategories extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Aclmanager', $prefix = 'AclmanagerModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function topthis() {
		parent::topthis();
		$this->setRedirect('index.php?option=com_aclmanager&view=aclcategories');
	}
}
