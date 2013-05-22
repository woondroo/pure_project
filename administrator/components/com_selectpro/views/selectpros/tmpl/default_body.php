
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
		<?php echo $item->point; ?>
	</td>
	<td>
		<?php echo $item->name; ?>
	</td>
	<td>
			<?php echo JHtml::_('grid.boolean', $i, $item->published, 'selectpro.published', 'selectpro.unpublished'); ?>
	</td>
	<td>
		<?php echo $item->catid; ?>
	</td>
	</tr>
<?php endforeach; ?>