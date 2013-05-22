
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * SelectproList Model
 */
class SelectproModelSelectpros extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'category_title',
				'id',
				'a.id'
			);
		}
		
		$session = JFactory::getSession();
		$show_fields = $session->get('showFields');
		if ($show_fields) {
			$fields = explode('-',$show_fields);
			if (count($fields)) {
				foreach ($fields as $field) {
					$config['filter_fields'][] = 'a.'.$field;
				}
			}
		}
		parent::__construct($config);
	}
	
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		// List state information.
		parent::populateState('a.id', 'asc'); // 手动修改排序规则 默认为id
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		$session = JFactory::getSession();
		$select_table = $session->get('selectTable');
		$show_fields = $session->get('showFields');
		$search_field = $session->get('searchField');
		
		$fs_str = 'a.id';
		if ($show_fields) {
			$fields = explode('-',$show_fields);
			if (count($fields)) {
				foreach ($fields as $key=>$field) {
					$fs_str .= ',a.'.$field;
					$fields[$key] = 'a.'.$field;
				}
			}
		}
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				$fs_str
			)
		);
		$query->from('#__'.$select_table.' AS a');
		
		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search) && $search_field) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.'.$search_field.' LIKE '.$search.')');// 注意手动修改搜索目标 title 为默认，如果无 title 字段则会出错
			}
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$fields_order = $fields;
		$fields_order[] = 'id';
		$fields_order[] = 'category_title';
		$fields_order[] = 'a.id';
		$fields_order[] = 'a.category_title';
		
		if (in_array($orderCol,$fields)) {
			if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
				$orderCol = 'category_title '.$orderDirn.', a.ordering';
			}
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		} else {
			$orderCol = 'a.id';
			$orderDirn = 'asc';
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		}
		return $query;
	}
}

