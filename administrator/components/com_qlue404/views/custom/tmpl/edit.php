<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_qlue404&view=custom&layout=edit&id='. (int)$this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	
	<div class="width-60 fltlft">
	<fieldset class="adminform">
        <legend><?php echo JText::_( 'COM_QLUE404_DETAILS' ); ?></legend>
        <ul class="adminformlist">
			<li>
				<?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?>
			</li>
			<li>
				<?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?>
			</li>
			<li>
				<?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?>
			</li>
		</ul>
		
        <div class="clr"></div>
		<?php echo $this->form->getLabel('description'); ?>
		<div class="clr"></div>
		<?php echo $this->form->getInput('description'); ?>
		
    </fieldset>
    </div>
    
    <div class="width-40 fltrt">

		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_QLUE404_PANEL_PARAMS'); ?></legend>
			<ul class="adminformlist">
				<?php foreach ($this->form->getFieldset('params') as $field) : ?>
					<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('HTACCESS_EDIT'); ?></legend>
			<?php echo JText::_('QLUE404_EXTEND_DESC'); ?>
			<?php echo JText::_('QLUE404_EXTEND_MAIN'); ?>
			<br />
			<?php $path = basename(JPATH_ROOT); ?>
			<?php echo JText::sprintf('QLUE404_EXTEND_SUB', $path, $path, $path); ?>
		</fieldset>
	</div>
    
    <div>
    	<?php foreach ($this->form->getFieldset('hidden') as $field) : ?>
			<?php echo $field->input; ?>
		<?php endforeach; ?>
    	<input type="hidden" name="task" value="custom.edit" />
    	<input type="hidden" name="layout" value="edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
        
</form>