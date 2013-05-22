<?php
/**
 * @version		$Id: downloads.php 2012-01-10 04:08:46
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * 
 *
 * @package		Joomla.Site
 * @subpackage	com_download

 */
class DownloadModelDownloads extends JModelList
{


	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$categoryId = $this->getState('category.id');
		
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__download` AS a');
		$query->where('a.published =  1');
		// Filter by category.
		if ($categoryId) {
			jimport('mulan.mldb');
			$cat = MulanDBUtil::getObjectBySql('select * from #__categories where published=1 and id='.MulanDBUtil::dbQuote($categoryId));
			if ($cat->id) {
				$query->select('c.title as ctitle');
				$query->where('c.lft >= '.$cat->lft);
				$query->where('c.rgt <= '.$cat->rgt);
				$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
				//Filter by published category
				$cpublished = $this->getState('filter.c.published');
				if (is_numeric($cpublished)) {
					$query->where('c.published = '.(int) $cpublished);
				}
			}
		}

		// Filter by state

		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.state = '.(int) $state);
		}

		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toMySQL());

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
//		echo $query;
		return $query;
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		$params	= $app->getParams('com_download');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $params->get('limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', 'ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$catid = JRequest::getVar('id', 0, '', 'int');
		$this->setState('category.id', $catid);

		// Load the parameters.
		$this->setState('params', $params);
	}
	
	public function getCurrentCate()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		// Select required fields from the categories.
		$query->select('c.title as ctitle,c.metadesc as metadesc,c.metakey as metakey,c.metadata as metadata');
		$query->from('`#__categories` AS c');
		$query->where('c.id = '.$this->getState('category.id'));
		
		$list = $this->_getList($query);
		if (count($list)) {
			$ret_item = $list[0];
			if (is_string($ret_item->metadata)) {
				$ret_item->metadata = json_decode($ret_item->metadata);
			}
		}
		return $ret_item;
	}
}
