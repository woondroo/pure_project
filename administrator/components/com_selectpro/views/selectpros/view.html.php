
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Selectpros View
 */
class SelectproViewSelectpros extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	/**
	 * Selectpros view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		$session = JFactory::getSession();
		$select_table = JRequest::getVar('selectTable');
		$show_fields_names = JRequest::getVar('showFields');
		$search_field = JRequest::getVar('searchField');
		$isset = JRequest::getVar('isset');
		if ($select_table || $isset) {
			$session->set('selectTable',$select_table);
		}
		if ($show_fields_names || $isset) {
			$session->set('showFields_name',$show_fields_names);
			$show_fields_array = explode('-',$show_fields_names);
			$show_fields = '';
			if (count($show_fields_array)) {
				foreach ($show_fields_array as $key=>$field) {
					$field_array = explode('|',$field);
					$show_fields .= ($key == 0 ? '' : '-').$field_array[0];
				}
			}
			$session->set('showFields',$show_fields);
		}
		if ($search_field || $isset) {
			$session->set('searchField',$search_field);
		}
		
		// Get data from the model
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');
		// Check for errors.
		if (count($errors =$this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}
 
		// Set the toolbar
		$this->addToolBar();
		// Display the template
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() {}
	
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_SELECTPRO_ADMINISTRATION'));
		
		$document->addScript(JURI::root() . "media/system/js/modal.js");
		$document->addStyleSheet(JURI::root() . 'media/system/css/modal.css');
	}
}
