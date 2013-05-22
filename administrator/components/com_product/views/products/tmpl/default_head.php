<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width='5'>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_ID'); ?>
	</th>
	<th width='20'>
		<input type='checkbox' name='toggle' value='' onclick='checkAll(<?php echo count($this->items); ?>);' />
	</th>			
	<th>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_TITLE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_BRANDNAME'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_COUNTRY'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_CATID'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_PUBLISHED'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_PRODUCT_PRODUCT_HEADING_ORDERING'); ?>
	</th>
</tr>