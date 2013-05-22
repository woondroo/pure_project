<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_service
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$itemid = JRequest::getVar("Itemid");
$cateid = JRequest::getVar("catid");
if (!$cateid) {
	$id = $this->item->id;
	$cateid = $id;
}
jimport('mulan.mlimage');
$base = JURI::base();
$itemLink = 'index.php?option=com_service&view=service&Itemid='.$itemid.'&id=';
$recommendLink = 'index.php?option=com_product&view=product&Itemid='.$this->productItemid;
?>
<div class="content-view">
	<div class="frame960 relative">
		<div class="service-memu absolute">
			<?php
			if (count($this->items)) {
				foreach($this->items as $key=>$item){
			?>
				<a class="relative<?php echo $cateid == $item->id ? ' active' : ''; ?>" title="<?php echo $item->title?>" href="<?php echo $itemLink.$item->id?>">
					<span class="icos service-ico<?php echo $key?>"></span>
					&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $item->title?>
				</a>
			<?php
				}
			}
			?>
			<div class="clr"></div>
		</div>
		
		<div class="service-head">
			<span class="service-title"><?php echo $this->item->servicetitle ?></span>
			<span class="service-desc"><?php echo $this->item->servicedesc ?></span>
		</div>
		<div class="clr"></div>
	</div>
</div>