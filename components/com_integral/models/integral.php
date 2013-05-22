<?php
/**
 * @version		$Id: integral.php 2012-05-19 13:25:06
 * @subpackage	com_integral
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access
defined('_JEXEC') or die;


include_once 'components/com_integral/models/integrals.php';
/**
 * Integral Component Model for a Integral record
 *
 * @package		Joomla.Site
 * @subpackage	com_integral
 * @since		1.5
 */
class IntegralModelIntegral extends IntegralModelIntegrals
{
	public static $cate_array = array();
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_integral.integral';

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
		$id	= JRequest::getInt('pid');

		$this->setState('integral.id', $id);
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
				$id = $this->getState('integral.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Integral', 'IntegralTable');

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
		$id = $this->getState('integral.id');
		$ordering = $this->getState('integral.ordering');
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
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__integral where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('integral.ordering', $ordering ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$result = MulanDBUtil::getPreNextPro(-1,'#__integral','an',$wheres,$ordering,$id,1);
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
		$id = $this->getState('integral.id');
		$ordering = $this->getState('integral.ordering');
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
			
			$ordering = $ordering || $id ? $ordering : MulanDBUtil::getObjectbySql('select * from #__integral where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('integral.ordering', $ordering || $id ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$result = MulanDBUtil::getPreNextPro(1,'#__integral','an',$wheres,$ordering,$id,1);
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
		$id = $this->getState('integral.id');
		$ordering = $this->getState('integral.ordering');
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
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__integral where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('integral.ordering', $ordering ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$sql = MulanDBUtil::getPreNextPro(-1,'#__integral','an',$wheres,$ordering,$id,1,false,true,true);
			return MulanDBUtil::getObjectBySql($sql)->count;
		} else {
			return null;
		}
	}
}
