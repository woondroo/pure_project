<?php
/**
 * @version		$Id: default.php 2012-01-11 09:44:48
 * @package		Joomla.Site
 * @subpackage	com_product
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$base = JURI::base();
$itemid = JRequest::getVar("Itemid");
$cateid = JRequest::getVar('id');
$start = $this->start;

$itemLink = 'index.php?option=com_new&view=new&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid.'&pid=';
?>
<div class="new-page-title"><?php echo $this->item->title; ?></div>
<div class="new-page-time-area">
	<span class="new-page-time">发布时间：<?php echo date("Y年m月d日",strtotime($this->item->addtime)); ?></span>
	<?php
	echo MulanDBUtil::loadmod('mod_share','siteshare',array('simg'=>$base.$this->item->image,'stitle'=>$this->item->title,'surl'=>JRoute::_($itemLink.$this->item->id)));
	?>
</div>
<div class="new-desc">
<?php
echo MulanHTMLUtil::replaceKeyWorks($this->item->description);
?>
</div>
<div class="content-bottom-line"></div>