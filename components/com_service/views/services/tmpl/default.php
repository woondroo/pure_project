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
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$itemid = JRequest::getVar("Itemid");
$cateid = JRequest::getVar("id");
$itemLink = 'index.php?option=com_service&view=service&Itemid='.$itemid.'&id=';
?>
<div class="services-list-view content-view">
	<div class="frame960 relative">
		<div class="service-list">
			<?php foreach($this->items as $o){?>
				<div class="service-item">
					<a class="title" href="<?php echo $itemLink.$o->id;?>" title="<?php echo $o->title?>"><?php echo $o->title?></a>
				</div>
			<?php }?>
			<div class="clr"></div>
		</div>
	</div>
</div>
