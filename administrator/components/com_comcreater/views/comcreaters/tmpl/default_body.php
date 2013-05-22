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
			<a href="?option=com_comcreater&view=comcreater&layout=edit&id=<?php echo $item->id;?>"><?php echo $item->title; ?></a>
		</td>
		<td>
			<?php echo $item->description; ?>
		</td>
	</tr>
<?php endforeach; ?>
