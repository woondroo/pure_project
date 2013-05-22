<?php

// no direct access
defined('_JEXEC') or die('Restricted Access');

// import joomla model list library
jimport('joomla.application.component.modellist');

class Qlue404ModelCustoms extends JModelList {
		
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
			'id', 'a.id',
			'title', 'a.title',
			'published', 'a.published',
			'created', 'a.created',
			'modified', 'a.modified'
			);
		}

		parent::__construct($config);
	}
	
			
	protected function getListQuery() {
				
		// Get instance of JDatabase	
		$db =& JFactory::getDBO();
		
		// Get a clean query object
		$query = $db->getQuery(true);
		
		// Create the query
		$query->select('a.*, u.name AS editor');
		$query->from('#__qlue404 AS a');
		$query->leftjoin('#__users AS u ON u.id = a.checked_out');
		
		// Return our query object
		return $query;
	}	
	
}

?>