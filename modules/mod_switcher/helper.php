<?php
/**
 * @version		$Id: helper.php 20926 2011-03-09 06:59:31Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_feed
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('mulan.mldb');
class modSwitcherHelper
{
	static function getItems(&$params)
	{
		$sourceTableName = $params->get('resource');
		$isdetail = $params->get('isdetail');
		$detailpid = $params->get('detailpid');
		$from = $params->get('from');
		$pimgsdesc = $params->get('pimgsdesc');
		$showlimit = $params->get('showlimit');
		
		if ($isdetail && $detailpid) {
			$images = MulanImageUtil::images($from);
			if ($pimgsdesc) {
				$imgdescs = MulanDBUtil::getObjectBySql('select title,imgdesc from #__'.$sourceTableName.' where id='.$detailpid);
				$imgdescs_array = explode('¦',$imgdescs->imgdesc);
				if (count($imgdescs_array)) {
					foreach ($imgdescs_array as $key=>$imgdesc) {
						$imgdescs_array[$key] = str_replace('&brvbar;','¦',$imgdesc);
					}
				}
			}
			$items = array();
			$shownum = 0;
			foreach ($images as $key=>$img) {
				$shownum ++;
				$item = new stdClass;
				$img_src = IS_WIN ? iconv("GBK", "UTF-8", $img->name) : $img->name;
				$img_desc = $imgdescs_array[$key];
				$img_desc_params = explode('-',$img_desc);
				$img_title = $imgdescs->title;
				if (count($img_desc_params) > 1) {
					$img_title = $img_desc_params[0];
					$img_desc = $img_desc_params[1];
				}
				
				$item->title = $img_title;
				$item->image = $from.'/'.$img_src;
				$item->link = '';
				$item->description = $img_desc;
				$items[] = $item;
				if (isset($showlimit) && $showlimit != -1 && $shownum >= $showlimit) break;
			}
		} else {
			$catid = $params->get('catid');
			$sql = 'SELECT * FROM #__'.$sourceTableName.' WHERE published = 1 and catid='.$catid.' ORDER BY ordering ASC ,id DESC'.(!isset($showlimit) || $showlimit == -1 ? '' : ' limit '.$showlimit);
			$items = MulanDBUtil::getObjectlistBySql($sql);
		}
		return $items;
	}
}