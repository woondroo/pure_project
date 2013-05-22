
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$params = $this->form->getFieldsets('params');
?>
<style>
	span{line-height:30px;}
</style>
<form action='<?php echo JRoute::_('index.php?option=com_yingpin&layout=edit&id='.(int) $this->item->id); ?>' method='post' name='adminForm' id='yingpin-form' class='form-validate'>
 
	<div class='width-60 fltlft'>
		<fieldset class='adminform'>
			<legend><?php echo JText::_( 'COM_YINGPIN_YINGPIN_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
			<li><label for="jform_job" id="jform_job-lbl">职位：</label><span><?php echo $this->item->job ?></span></li>
			<li><label for="jform_job" id="jform_job-lbl">姓名：</label><span><?php echo $this->item->name ?></span></li>
			<li><label for="jform_job" id="jform_job-lbl">性别：</label><span><?php echo $this->item->sex ? '先生' : '小姐' ?></span></li>
			<li><label for="jform_job" id="jform_job-lbl">电话：</label><span><?php echo $this->item->tel ?></span></li>
			<li><label for="jform_job" id="jform_job-lbl">E-mail：</label><span><?php echo $this->item->email ?></span></li>
			<li><label for="jform_job" id="jform_job-lbl">附件：</label><span><a href="../<?php echo $this->item->fujian ?>">下载</a></span></li>
			<li><label for="jform_job" id="jform_job-lbl">申请时间：</label><span><?php echo $this->item->time ?></span></li>
			
<?php /* foreach($this->form->getFieldset('details') as $field): ?>
		
				<li><?php echo $field->label; echo $field->input;?></li>
<?php endforeach; */ ?>
			</ul>
	</div>

<!--
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'yingpin-slider'); ?>
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
-->
	<div>
		<input type='hidden' name='task' value='yingpin.edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>