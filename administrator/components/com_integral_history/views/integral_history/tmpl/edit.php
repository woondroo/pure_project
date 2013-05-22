
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$params = $this->form->getFieldsets('params');
?>
<form action='<?php echo JRoute::_('index.php?option=com_integral_history&layout=edit&id='.(int) $this->item->id); ?>' method='post' name='adminForm' id='integral_history-form' class='form-validate'>
 
	<div class='width-60 fltlft'>
		<fieldset class='adminform'>
			<legend><?php echo JText::_( 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
<?php
foreach($this->form->getFieldset('details') as $field):
	if ($field->name == 'jform[state]') {
		echo $field->label;
		if ($field->value == -1) {
			echo '<div class="left" style="color:gray;">已取消</div>';
		} else if ($field->value == 2) {
			echo '<div class="left" style="color:green;">已完成</div>';
		} else {
			echo $field->input;
		}
	} else if ($field->name == 'jform[way]') {
		echo $field->label;
		if ($field->value == 0) {
			echo '<div class="left" style="color:green;">积分获得</div>';
		} else if ($field->value == 1) {
			echo '<div class="left" style="color:red;">积分使用</div>';
		}
	} else {
		echo $field->label;
		if (strtolower($field->type) == 'editor') {
			echo '<div class="clr"></div>';
		}
		echo $field->input;
	}
endforeach;
?>
			</ul>
	</div>
 
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'integral_history-slider'); ?>
<?php foreach ($params as $name => $fieldset): ?>
		<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
	<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
		<p class='tip'><?php echo $this->escape(JText::_($fieldset->description));?></p>
	<?php endif;?>
		<fieldset class='panelform' >
			<ul class='adminformlist'>
	<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
	<?php endforeach; ?>
			</ul>
		</fieldset>
<?php endforeach; ?>
		
		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
		<fieldset class='panelform'>
			<?php echo $this->loadTemplate('metadata'); ?>
		</fieldset>
		
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type='hidden' name='task' value='integral_history.edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>