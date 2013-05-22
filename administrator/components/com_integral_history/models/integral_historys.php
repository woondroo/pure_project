
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * Integral_historyList Model
 */
class Integral_historyModelIntegral_historys extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'pid', 'a.pid',
				'reason', 'a.reason',
				'use', 'a.use',
				'get', 'a.get',
				'last', 'a.last',
				'ordertime', 'a.ordertime',
				'receivetime', 'a.receivetime',
				'completetime', 'a.completetime',
				'state', 'a.state',
				'way', 'a.way',
			);
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

		$get_state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
		$this->setState('filter.state', $get_state);

//		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
//		$this->setState('filter.published', $published);

//		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
//		$this->setState('filter.category_id', $categoryId);

		// List state information.
		parent::populateState('a.id', 'desc'); // 手动修改排序规则 默认为id
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'a.id,a.pid,a.reason,a.use,a.get,a.last,a.ordertime,a.receivetime,a.completetime,a.state,a.way'
			)
		);
		$query->from('#__integral_history AS a');
		
		$query->select('p.title AS ptitle');
		$query->join('LEFT', '#__integral AS p ON p.id = a.pid');
		
		$query->select('u.username AS uusername');
		$query->join('LEFT', '#__users AS u ON u.id = a.uid');
		
//		// Join over the categories.
//		$query->select('c.title AS category_title');
//		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

//		// Filter by published state
//		$published = $this->getState('filter.published');

		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.state = ' . (int) $state);
		}
		else if ($state === '') {
			$query->where('a.state in(-1,0,1,2)');
		}
		
//		// Filter by a single or group of categories.
//		$categoryId = $this->getState('filter.category_id');
//				
//		if (is_numeric($categoryId)) {
//			$query->where('a.catid = '.(int) $categoryId);
//		}
//		else if (is_array($categoryId)) {
//			JArrayHelper::toInteger($categoryId);
//			$categoryId = implode(',', $categoryId);
//			$query->where('a.catid IN ('.$categoryId.')');
//		}
		
		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('(u.username LIKE '.$search.')');
		}
		
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		return $query;
	}
}

