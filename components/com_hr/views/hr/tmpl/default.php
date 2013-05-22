<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$itemid = JRequest::getVar("Itemid");
$cateid = JRequest::getVar('id');
$start = $this->start;

$base = JURI::base();
$listLink = 'index.php?option=com_hr&view=hrs&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid;
$itemLink = 'index.php?option=com_hr&view=hr&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid.'&pid=';
?>
<div class='hr-item-view hr-view'>
详细页ID：<?php echo $this->item->id?>;<br/>
<a href="<?php echo $listLink; ?>">返回列表</a>
</div>