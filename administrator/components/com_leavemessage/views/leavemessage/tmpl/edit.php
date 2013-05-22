
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$params = $this->form->getFieldsets('params');

$app = JFactory::getApplication();
$site_model = $app->getCfg('site_model');
?>
<form action='<?php echo JRoute::_('index.php?option=com_leavemessage&layout=edit&id='.(int) $this->item->id); ?>' method='post' name='adminForm' id='leavemessage-form' class='form-validate'>
 
	<div class='width-60 fltlft'>
		<fieldset class='adminform'>
			<legend><?php echo JText::_( 'COM_LEAVEMESSAGE_LEAVEMESSAGE_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
<?php
foreach($this->form->getFieldset('details') as $field):
	if ($field->name == 'jform[company]') {
		if ($site_model) {
			$val = $field->value;
?>
	<li>
		<label id="jform_company-lbl" for="jform_company" title="">预算</label>
		<input type="radio" name="jform[company]" class="radio-input" value="1000-" id="1000"<?php echo $val == '1000-' ? ' checked' : ''; ?> /><label class="radio-label" for="1000">1000以下</label>
		<input type="radio" name="jform[company]" class="radio-input" value="1000-2000" id="2000"<?php echo $val == '1000-2000' ? ' checked' : ''; ?>  /><label class="radio-label" for="2000">1000-2000</label>
		<input type="radio" name="jform[company]" class="radio-input" value="2000-5000" id="5000"<?php echo $val == '2000-5000' ? ' checked' : ''; ?>  /><label class="radio-label" for="5000">2000-5000</label>
		<input type="radio" name="jform[company]" class="radio-input" value="5000+" id="5000+"<?php echo $val == '5000+' ? ' checked' : ''; ?>  /><label class="radio-label" for="5000+">5000以上</label>
	</li>
<?php
		} else {
?>
	<li><?php echo $field->label;echo $field->input;?></li>
<?php
		}
	} else {
?>
	<li><?php echo $field->label;echo $field->input;?></li>
<?php
	}
endforeach;
?>
			</ul>
	</div>
 
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'leavemessage-slider'); ?>
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
 
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type='hidden' name='task' value='leavemessage.edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>