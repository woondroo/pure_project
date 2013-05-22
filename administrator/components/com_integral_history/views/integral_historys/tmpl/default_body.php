
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class='row<?php echo $i % 2; ?>'>
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
	<td>
		<?php echo $item->pid; ?>
	</td>
	<td>
		<?php echo $item->reason; ?>
	</td>
	<td>
		<?php echo $item->use; ?>
	</td>
	<td>
		<?php echo $item->get; ?>
	</td>
	<td>
		<?php echo $item->last; ?>
	</td>
	<td>
		<?php echo $item->state; ?>
	</td>
	<td>
		<?php echo $item->way; ?>
	</td>
	</tr>
<?php endforeach; ?>