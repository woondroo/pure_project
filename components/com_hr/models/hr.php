<?php
/**
 * @version		$Id: hr.php 21481 2011-06-08 00:38:29Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

include_once 'components/com_hr/models/hrs.php';
/**
 * Hr Component Model for a hr record
 *
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @since		1.5
 */
class HrModelHr extends HrModelHrs
{
	public static $cate_array = array();
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_hr.hr';

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
			$id = $this->getFirstItem()->sid;
		}
		
		$this->setState('hr.id', $id);
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
				$id = $this->getState('hr.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('hr', 'HrTable');

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
	
	public function getPreOne() {
		jimport('mulan.mldb');
		$id = $this->getState('hr.id');
		$ordering = $this->getState('hr.ordering');
		if ($id) {
			$cateid = JRequest::getVar('id');
			if ($cateid && !count(self::$cate_array)) {
				$get_cats = MulanDBUtil::getObjectlistBySql('select c1.id as cid from #__categories as c left join #__categories as c1 on c1.lft >= c.lft and c1.rgt <= c.rgt where c.id='.MulanDBUtil::dbQuote($cateid));
				if (count($get_cats)) {
					foreach ($get_cats as $cat) {
						array_push(self::$cate_array,$cat->cid);
					}
				}
			}
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__hr where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('hr.ordering', $ordering ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$result = MulanDBUtil::getPreNextPro(-1,'#__hr','an',$wheres,$ordering,$id,1);
			if (count($result)) {
				return $result;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	public function getNextOne() {
		jimport('mulan.mldb');
		$id = $this->getState('hr.id');
		$ordering = $this->getState('hr.ordering');
		if ($id) {
			$cateid = JRequest::getVar('id');
			if ($cateid && !count(self::$cate_array)) {
				$get_cats = MulanDBUtil::getObjectlistBySql('select c1.id as cid from #__categories as c left join #__categories as c1 on c1.lft >= c.lft and c1.rgt <= c.rgt where c.id='.MulanDBUtil::dbQuote($cateid));
				if (count($get_cats)) {
					foreach ($get_cats as $cat) {
						array_push(self::$cate_array,$cat->cid);
					}
				}
			}
			
			$ordering = $ordering || $id ? $ordering : MulanDBUtil::getObjectbySql('select * from #__hr where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('hr.ordering', $ordering || $id ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$result = MulanDBUtil::getPreNextPro(1,'#__hr','an',$wheres,$ordering,$id,1);
			if (count($result)) {
				return $result;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	public function getStart() {
		jimport('mulan.mldb');
		$id = $this->getState('hr.id');
		$ordering = $this->getState('hr.ordering');
		if ($id) {
			$cateid = JRequest::getVar('id');
			if ($cateid && !count(self::$cate_array)) {
				$get_cats = MulanDBUtil::getObjectlistBySql('select c1.id as cid from #__categories as c left join #__categories as c1 on c1.lft >= c.lft and c1.rgt <= c.rgt where c.id='.MulanDBUtil::dbQuote($cateid));
				if (count($get_cats)) {
					foreach ($get_cats as $cat) {
						array_push(self::$cate_array,$cat->cid);
					}
				}
			}
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__hr where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('hr.ordering', $ordering ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$sql = MulanDBUtil::getPreNextPro(-1,'#__hr','an',null,$ordering,$id,1,false,true,true);
			return MulanDBUtil::getObjectBySql($sql)->count;
		} else {
			return null;
		}
	}
}