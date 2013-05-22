<?php 

// no direct access
defined('_JEXEC') or die('Restricted Access');

// import the table library
jimport('joomla.database.table');

class Qlue404TableCustom extends JTable {
		
	function __construct(&$db) {
		parent::__construct('#__qlue404', 'id', $db);
	}
	
	public function bind($array, $ignore = '') {
		
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
		
		return parent::bind($array, $ignore);
	}
	
	public function load($keys = null, $reset = true) {
				
		$operators = array('equals' => '=', 'like' => 'LIKE');	
		
		if (empty($keys)) {
			// If empty, use the value of the current key
			$keyName = $this->_tbl_key;
			$keyValue = $this->$keyName;
			$operator = '=';

			// If empty primary key there's is no need to load anything
			if (empty($keyValue)) {
				return true;
			}

			$keys = array($keyName => $keyValue);
		}
		else if (!is_array($keys)) {
			// Load by primary key.
			$keys = array($this->_tbl_key => $keys, 'operator' => '=');
		}
		
		if(array_key_exists('operator', $keys)) {
			$operator = isset($operators[$keys['operator']]) ? $operators[$keys['operator']] : '=';
			unset($keys['operator']);
		} else {
			$operator = '=';
		}

		if ($reset) {
			$this->reset();
		}

		// Initialise the query.
		$query	= $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_tbl);
		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value) {
				
			if($operator == 'LIKE') {
				$value = $this->_db->quote('%'.$value.'%');
			} else {
				$value = $this->_db->quote($value);
			}
			
			// Check that $field is in the table.
			if (!in_array($field, $fields)) {
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_CLASS_IS_MISSING_FIELD', get_class($this), $field));
				$this->setError($e);
				return false;
			}
			// Add the search tuple to the query.
			$query->where($this->_db->nameQuote($field).' '. $operator .' '.$value);
		}

		$this->_db->setQuery($query);
		$row = $this->_db->loadAssoc();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}

		// Check that we have a result.
		if (empty($row)) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
			$this->setError($e);
			return false;
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}
}
?>