<?php
/**
 * @version		$Id: helper.php 22152 2012-01-31 10:34:19 $
 * @package		Woondroo
 * @subpackage	mod_weblinks
 */

// no direct access
defined('_JEXEC') or die;

class modTobackHelper {
	public static $cate_array = array();
	
	static function getStart($params) {
		$showlist = $params->get('showlist');
		if (!$showlist) return null;
		
		jimport('mulan.mldb');
		$id = JRequest::getVar('pid');
		$view = JRequest::getVar('view');
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
			
			$ordering = $ordering ? $ordering : MulanDBUtil::getObjectbySql('select * from #__'.$view.' where id='.MulanDBUtil::dbQuote($id))->ordering;
			
			$wheres = array('published=1','catid in('.implode(',',self::$cate_array).')');
			$sql = MulanDBUtil::getPreNextPro(-1,'#__'.$view,'an',$wheres,$ordering,$id,1,false,true,true);
			return MulanDBUtil::getObjectBySql($sql)->count;
		} else {
			return null;
		}
	}
}
?>
