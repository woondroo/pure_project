<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
		<?php echo 'ID'; ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo '目录标题'; ?>
	</th>
	<th>
		<?php echo '包含子权限'; ?>
	</th>
	<th width='10%'>
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
		<?php if ($canOrder && $saveOrder) :?>
			<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'aclmanagers.saveorder'); ?>
		<?php endif; ?>
	</th>
	<th>
		<?php echo '已分配用户组'; ?>
	</th>
</tr>
