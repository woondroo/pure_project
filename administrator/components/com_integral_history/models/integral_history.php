
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * Integral_history Model
 */
class Integral_historyModelIntegral_history extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authoriseInCustom('core.edit', 'com_integral_history.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Integral_history', $prefix = 'Integral_historyTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_integral_history.integral_history', 'integral_history', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_integral_history/models/forms/integral_history.js';
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_integral_history.edit.integral_history.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($data)
	{
		jimport('mulan.mldb');
		if ($data['uid']) $user_mess = MulanDBUtil::getObjectBySql('select * from #__users where id=\''.$data['uid'].'\'');
		$order = MulanDBUtil::getObjectBySql('select * from #__integral_history where id=\''.$data['id'].'\'');
		if ($order->state == -1 && $data['state'] != -1) {
			$last_integral = $user_mess->integral;
			if ($last_integral < $order->use) {
				JError::raiseWarning(403, JText::_('COM_INTEGRAL_ORDER_PRODUCT_FAIL_NOINTEGRAL'));
				return false;
			} else {
				$last_integral = $last_integral - $order->use;
				MulanDBUtil::executeSql('update #__users set integral=\''.$last_integral.'\' where id=\''.$user_mess->id.'\'');
				$data['receivetime'] = '';
				$data['completetime'] = '0000-00-00 00:00:00';
			}
		}
		
		if ($data['state'] == -1 && $data['completetime'] == '') {
			$use_integral = $order->use;
			$last_integral = $user_mess->integral + $use_integral;
			MulanDBUtil::executeSql('update #__users set integral=\''.$last_integral.'\' where id='.$data['uid']);
			
			$data['completetime'] = date('Y-m-d H:i:s');
		}
		
		if ($data['state'] == 1 && $data['receivetime'] == '') {
			$data['receivetime'] = date('Y-m-d H:i:s');
		}
		
		if ($data['state'] == 2 && $data['completetime'] == '') {
			$data['completetime'] = date('Y-m-d H:i:s');
		}
		
		$result = parent::save($data);
		return $result;
	}
}