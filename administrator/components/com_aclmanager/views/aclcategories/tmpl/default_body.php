<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_aclmanager&task=aclcategory.edit&id='.$item->id);?>">
			<?php echo $item->title; ?></a>
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
		<td >
			<?php //echo ''; ?>
		</td>
	</tr>
<?php endforeach; ?>
