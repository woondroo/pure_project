<?php
/**
 * @version		$Id: service.php 21481 2011-06-08 00:38:29Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once 'components/com_service/models/services.php';
/**
 * Service Component Model for a service record
 *
 * @package		Joomla.Site
 * @subpackage	com_service
 * @since		1.5
 */
class ServiceModelService extends ServiceModelServices
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_service.service';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		// Load the object state.
		$id	= JRequest::getInt('id');
		if (!$id) {
			$firstItem = $this->getFirstItem();
			$id = $firstItem->sid;
		}
		
		$this->setState('service.id', $id);
		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('service.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Service', 'ServiceTable');

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			}
			else if ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}
	
	public function getFirstItem() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('s.id as sid');
		$query->from('`#__service` as s');
		$query->order('s.ordering asc,s.id asc');
		$query->limit('limit 1');
		$items = $this->_getList($query);
		return $items[0];
	}
	
	public function getProductItemid() {
		return $this->getItemidByAlias('cases','mainmenu');
	}
	
	public function getItemidByAlias($alias,$type) {
		jimport('mulan.mldb');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('m.id as mid');
		$query->from('`#__menu` as m');
		$query->where('m.alias='.MulanDBUtil::dbQuote($alias).' and m.menutype='.MulanDBUtil::dbQuote($type));
		$query->limit('limit 1');
		$items = $this->_getList($query);
		return $items[0]->mid;
	}
	
	public function getRecommendPros() {
		$item = $this->getItem();
		return $this->getRecommendList($item->pros);
	}
	
	public function getRecommendList($pros) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('`#__product` as p');
		$query->order('p.ordering desc,p.id desc');
		$query->where('p.id in('.$pros.')');
		$query->limit('limit 3');
		return $this->_getList($query);
	}
	
	public function getPoints() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('`#__map` as m');
		$query->order('m.ordering desc,m.id desc');
		$query->where('m.catid='.$this->getState('service.id'));
		return $this->_getList($query);
	}
}