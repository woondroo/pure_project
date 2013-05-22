
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * Product Model
 */
class ProductModelProduct extends JModelAdmin
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
		return JFactory::getUser()->authoriseInCustom('core.edit', 'com_product.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	public function getTable($type = 'Product', $prefix = 'ProductTable', $config = array()) 
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
		$form = $this->loadForm('com_product.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
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
		return 'administrator/components/com_product/models/forms/product.js';
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
		$data = JFactory::getApplication()->getUserState('com_product.edit.product.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			
			// Prime some default values.
			if (!$this->getState('filter.category_id')) {
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_product.products.filter.category_id')));
			}
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
		// Alter the title for save as copy
		if (JRequest::getVar('task') == 'save2copy') {
			list($title,$alias) = $this->generateNewTitle($data['alias'], $data['title']);
			$data['title']	= $title;
			$data['alias']	= $alias;
		}
		
		$metadescs = JRequest::getVar('imgdescs', array(), 'post', 'array');
		if (count($metadescs)) {
			foreach ($metadescs as $key=>$metadesc) {
				$metadescs[$key] = str_replace('¦','&brvbar;',$metadesc);
			}
			$metadescs_str = implode('¦',$metadescs);
			$data['imgdesc'] = $metadescs_str;
		}
		
		$result = parent::save($data);
		return $result;
	}
	
	/**
	 * Method to change the title & alias.
	 *
	 * @param	int     The value of the parent category ID.
	 * @param   sting   The value of the category alias.
	 * @param   sting   The value of the category title.
	 *
	 * @return	array   Contains title and alias.
	 * @since	1.7
	 */
	function generateNewTitle(&$alias, &$title)
	{
		// Alter the title & alias
		$table = $this->getTable();
		if ($table->load(array('alias'=>$alias))) {
			$m = null;
			if (preg_match('#-(\d+)$#', $alias, $m)) {
				$alias = preg_replace('#-(\d+)$#', '-'.($m[1] + 1).'', $alias);
			} else {
				$alias .= '-2';
			}
			if (preg_match('#\((\d+)\)$#', $title, $m)) {
				$title = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $title);
			} else {
				$title .= ' (2)';
			}
		}

		return array($title, $alias);
	}
	
	function published(&$pks, $value = 1)
	{
		// Initialise variables.
		
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
        // Check if I am a Super Admin
		$iAmSuperAdmin	=$user->authoriseInCustom('core.admin');
		$table		= $this->getTable();
		$pks		= (array) $pks;

		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk)) {
				$old	= $table->getProperties();
				$allow	= $user->authoriseInCustom('core.edit.state', 'com_product');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				// Prepare the logout options.
				$options = array(
					'clientid' => array(0, 1)
				);

				if ($allow) {
					// Skip changing of same state
					if ($table->published == $value) {
						unset($pks[$i]);
						continue;
					}

					$table->published = (int) $value;

					// Allow an exception to be thrown.
					try
					{
						if (!$table->check()) {
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						$result = $dispatcher->trigger('onUserBeforeSave', array($old, false, $table->getProperties()));
						if (in_array(false, $result, true)) {
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!$table->store()) {
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
				}
				else {
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		return true;
	}
	
	
}