<?php
/**
 * @version		$Id: download.php 2012-01-10 04:08:46
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


include_once 'components/com_download/models/downloads.php';
/**
 * Download Component Model for a Download record
 *
 * @package		Joomla.Site
 * @subpackage	com_download
 * @since		1.5
 */
class DownloadModelDownload extends DownloadModelDownloads
{
	public static $cate_array = array();
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_download.download';

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

		$this->setState('download.id', $id);
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
				$id = $this->getState('download.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Download', 'DownloadTable');

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
		$id = $this->getState('download.id');
		$ordering = $this->getState('download.ordering');
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
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__download where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('download.ordering', $ordering ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$result = MulanDBUtil::getPreNextPro(-1,'#__download','an',$wheres,$ordering,$id,1);
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
		$id = $this->getState('download.id');
		$ordering = $this->getState('download.ordering');
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
			
			$ordering = $ordering || $id ? $ordering : MulanDBUtil::getObjectbySql('select * from #__download where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('download.ordering', $ordering || $id ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$result = MulanDBUtil::getPreNextPro(1,'#__download','an',$wheres,$ordering,$id,1);
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
		$id = $this->getState('download.id');
		$ordering = $this->getState('download.ordering');
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
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__download where id='.MulanDBUtil::dbQuote($id))->ordering;
			$this->setState('download.ordering', $ordering ? $ordering : 0);
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$sql = MulanDBUtil::getPreNextPro(-1,'#__download','an',null,$ordering,$id,1,false,true,true);
			return MulanDBUtil::getObjectBySql($sql)->count;
		} else {
			return null;
		}
	}
}
