<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authoriseInCustom('core.edit.state', 'com_aclmanagers.category');
$saveOrder	= $listOrder == 'a.ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_aclmanager&view=aclcategories'); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>			
				<th>
					<?php echo JHtml::_('grid.sort', '目录标题', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo '包含子权限'; ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_ACLMANAGER_ACLMANAGER_HEADING_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width='10%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'aclcategories.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th>
					<?php echo '已分配用户组'; ?>
				</th>
				<th width="5">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($this->items as $i => $item) {
				$ordering	= $listOrder == 'a.ordering';
				$canCreate	= $user->authoriseInCustom('core.create',		'com_aclmanager.category.'.$item->catid);
				$canEdit	= $user->authoriseInCustom('core.edit',			'com_aclmanager.category.'.$item->catid);
				$canCheckin	= $user->authoriseInCustom('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn	= $user->authoriseInCustom('core.edit.own',		'com_aclmanager.category.'.$item->catid) && $item->created_by == $userId;
				$canChange	= $user->authoriseInCustom('core.edit.state',	'com_aclmanager.category.'.$item->catid) && $canCheckin;
				
				$link = JRoute::_('index.php?option=com_aclmanager&task=aclcategory.edit&id='.$item->id);
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<a href="<?php echo $link;?>">
							<?php echo $item->title; ?>
							<a class='top-this' href='index.php?option=com_aclmanager&task=aclcategories.topthis&cid=<?php echo $item->id?>'>置顶</a>
						</a>
					</td>
					<td>
						<ul>
							<?php foreach($item->submenu as $sub){?>
							<li>
								<?php echo $sub->title ?>
							</li>
							<?php }?>
						</ul>
					</td>
					<td class='center'>
						<?php echo JHtml::_('grid.boolean', $i, $item->published, 'aclcategory.published', 'aclcategory.unpublished'); ?>
					</td>
					<td class='order'>
						<?php if ($canChange) : ?>
							<?php if ($saveOrder) :?>
								<?php if ($listDirn == 'asc') : ?>
									<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'aclmanagers.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'aclmanagers.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php elseif ($listDirn == 'desc') : ?>
									<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'aclmanagers.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'aclmanagers.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php endif; ?>
							<?php endif; ?>
							<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type='text' name='order[]' size='5' value='<?php echo $item->ordering;?>' <?php echo $disabled ?> class='text-area-order' />
						<?php else : ?>
							<?php echo $item->ordering; ?>
						<?php endif; ?>
					</td>
					<td >
						<?php //echo ''; ?>
					</td>
					<td>
						<?php echo $item->id; ?>
					</td>
				</tr>
			<?php
				}
			?>
		</tbody>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
	</table>
	<div>
		<input type='hidden' name='task' value='' />
		<input type='hidden' name='boxchecked' value='0' />
		<input type='hidden' name='filter_order' value='<?php echo $listOrder; ?>' />
		<input type='hidden' name='filter_order_Dir' value='<?php echo $listDirn; ?>' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
