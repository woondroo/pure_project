<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Backups Controller
 */
class BackupControllerBackups extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Backup', $prefix = 'BackupModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function delete() {
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		
		if (is_array($cid) && count($cid) > 0) {
			jimport('mulan.mldb');
			$backups = MulanDBUtil::getObjectlistBySql('select sqlfile from #__backup where id in('.implode(',',$cid).')');
			if (count($backups)) {
				foreach ($backups as $b) {
					if (file_exists(JPATH_ROOT.$b->sqlfile)) unlink(JPATH_ROOT.$b->sqlfile);
				}
			}
		}
		parent::delete();
	}
}