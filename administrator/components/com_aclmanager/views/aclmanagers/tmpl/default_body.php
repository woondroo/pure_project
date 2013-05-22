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
			<a href="<?php echo JRoute::_('index.php?option=com_aclmanager&task=aclmanager.edit&id='.$item->id);?>">
			<?php echo $item->aname; ?></a>
		</td>
		<td>
			<?php echo $item->componenturl ?>
		</td>
		<td>
			<?php echo $item->cname?$item->cname:'顶级分类'; ?>
		</td>
	</tr>
<?php endforeach; ?>
