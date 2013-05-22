<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width='5'>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_ID'); ?>
	</th>
	<th width='20'>
		<input type='checkbox' name='toggle' value='' onclick='checkAll(<?php echo count($this->items); ?>);' />
	</th>			
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_PID'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_REASON'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_USE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_GET'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_LAST'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_STATE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_WAY'); ?>
	</th>
</tr>