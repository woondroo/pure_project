
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$params = $this->form->getFieldsets('params');
?>
<form action='<?php echo JRoute::_('index.php?option=com_hr&layout=edit&id='.(int) $this->item->id); ?>' method='post' name='adminForm' id='hr-form' class='form-validate'>
<?php
/**
 * 初始化配置，只需要调用一次即可，注意引入“jimport('mulan.mldb')”
 * 初始化后会得到当前文件上传的路径（如果是修改则给出对应的文件夹，如果是新建则给出一个临时文件夹并将临时文件夹放入“tempFolder”输入框中，后台获取后可对此文件夹重命名）
 */
$upload_root_uri = 'img/';
$upload_parent_uri = str_replace('com_','',JRequest::getVar('option'));
if (!$this->item->id) {
	jimport('mulan.mldb');
	$temp_folder = MulanDBUtil::getTempFolder();
	echo '<input type="hidden" id="tempFolder" name="tempFolder" value="'.$upload_root_uri.$upload_parent_uri.'/'.$temp_folder.'" />';
}
$upload_uri .= $this->item->id
				? $upload_root_uri.$upload_parent_uri.'/'.$upload_parent_uri.'-'.$this->item->id 
				: $upload_root_uri.$upload_parent_uri.'/'.$temp_folder;
?>
	<div class='width-60 fltlft'>
		<fieldset class='adminform'>
			<legend><?php echo JText::_( 'COM_HR_HR_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
<?php foreach($this->form->getFieldset('details') as $field): ?>
	<?php
	if ($field->name == 'jform[image]') {
		/**
		 * 开始配置文件上传的各项属性
		 */
		$set_swfupload_title = $this->form->getLabel('image');
		$file_upload_folder = $upload_uri;
		$file_input_id = 'jform_image';
		$file_input_name = 'jform[image]';
		$file_value = $this->item->image;
		$file_view_id = 'view_image';
		$file_norename = 0;
		include(JPATH_SITE.'/administrator/templates/system/html/com_media/upload/upload.php');
	} else {
	?>
	
	<?php if ($field->type!='Editor'){ ?>
	<li><?php echo $field->label;echo $field->input;?></li>
	<?php }else{ ?>
	<li><?php echo $field->label;echo '<div class="clr"></div><div>'. $field->input.'</div>';?></li>
	<?php } ?>
	
	<?php
	}
	?>
<?php endforeach; ?>
			</ul>
	</div>
 
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'hr-slider'); ?>
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
		<input type='hidden' name='task' value='hr.edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>