<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * Yingpin Controller
 */
class YingpinControllerYingpin extends JControllerForm
{
	
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('published',		'changepublished');
		$this->registerTask('unpublished',		'changepublished');
	}
	
	public function getModel($name = 'Yingpin', $prefix = 'YingpinModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	
	public function changepublished()
	{
		
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('published' => 1, 'unpublished' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('COM_YINGPIN_YINGPIN_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();
		
			// Change the state of the records.
			if (!$model->published($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1){
					$this->setMessage(JText::plural('COM_YINGPIN_N_YINGPIN_PUBLISED', count($ids)));
				} else if ($value == 0){
					$this->setMessage(JText::plural('COM_YINGPIN_N_YINGPIN_UNPUBLISED', count($ids)));
				}
			}
		}

		$this->setRedirect('index.php?option=com_yingpin&view=yingpins');
	}
	
}
