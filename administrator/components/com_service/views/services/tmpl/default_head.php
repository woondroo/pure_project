<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width='5'>
		<?php echo JText::_('COM_SERVICE_SERVICE_HEADING_ID'); ?>
	</th>
	<th width='20'>
		<input type='checkbox' name='toggle' value='' onclick='checkAll(<?php echo count($this->items); ?>);' />
	</th>			
	<th>
		<?php echo JText::_('COM_SERVICE_SERVICE_HEADING_TITLE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_SERVICE_SERVICE_HEADING_IMAGE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_SERVICE_SERVICE_HEADING_PUBLISHED'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_SERVICE_SERVICE_HEADING_ORDERING'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_SERVICE_SERVICE_HEADING_CATID'); ?>
	</th>
</tr>