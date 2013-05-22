<?php
/**
 * @version		$Id: edit.php 21553 2011-06-17 14:28:21Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

jimport('mulan.mldb');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'banner.cancel' || document.formvalidator.isValid(document.id('banner-form'))) {
			Joomla.submitform(task, document.getElementById('banner-form'));
		}
	}
	window.addEvent('domready', function() {
		document.id('jform_type0').addEvent('click', function(e){
			document.id('image').setStyle('display', 'block');
			document.id('url').setStyle('display', 'block');
			document.id('custom').setStyle('display', 'none');
		});
		document.id('jform_type1').addEvent('click', function(e){
			document.id('image').setStyle('display', 'none');
			document.id('url').setStyle('display', 'block');
			document.id('custom').setStyle('display', 'block');
		});
		if(document.id('jform_type0').checked==true) {
			document.id('jform_type0').fireEvent('click');
		} else {
			document.id('jform_type1').fireEvent('click');
		}
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_banners&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="banner-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_BANNERS_NEW_BANNER') : JText::sprintf('COM_BANNERS_BANNER_DETAILS', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>

				<li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>
				
				<li><?php echo $this->form->getLabel('coms'); ?>
				<?php echo $this->form->getInput('coms'); ?></li>
				
				<li>
				<li id="image">
					<ul style="margin:0;">
					<?php 
					/**
					 * 初始化配置，只需要调用一次即可，注意引入“jimport('mulan.mldb')”
					 * 初始化后会得到当前文件上传的路径（如果是修改则给出对应的文件夹，如果是新建则给出一个临时文件夹并将临时文件夹放入“tempFolder”输入框中，后台获取后可对此文件夹重命名）
					 */
					$upload_root_uri = 'img/';
					$upload_parent_uri = str_replace('com_','',JRequest::getVar('option'));
					if (!$this->item->id) {
						$temp_folder = MulanDBUtil::getTempFolder();
						// 如果是新建，一定要加上该句代码，否则文件夹将无法正确重命名！
						echo '<input type="hidden" id="tempFolder" name="tempFolder" value="'.$upload_root_uri.$upload_parent_uri.'/'.$temp_folder.'" />';
					}
					$upload_uri .= $this->item->id
									? $upload_root_uri.$upload_parent_uri.'/'.$upload_parent_uri.'-'.$this->item->id 
									: $upload_root_uri.$upload_parent_uri.'/'.$temp_folder;
					
					/**
					 * 开始配置文件上传的各项属性
					 */
					$set_swfupload_title = ''; // 上传标题，比如：坐标点图片
					$file_upload_folder = $upload_uri; // 文件上传的路径
					$file_input_id = 'jform_params_imageurl'; // 文件上传完成，路径填充的 input id
					$file_input_name = 'jform[params][imageurl]'; // 文件上传完成，路径填充的 input name
					$file_value = $this->item->params['imageurl']; // input 中默认填写的文件路径，已上传的都会有路径
					$file_view_id = 'view_params_imageurl'; // 图片预览的 img 标签的 id，不需要预览可以注释！
					$file_norename = 0; // 是否自动对文件进行重命名，默认会进行重命名，所以可以多次上传同名文件！
					$file_unsetwm = 1; // 设置上传的图片是否带水印，1 为不带水印，注释后如果开启了水印则会有水印！
					//$upload_count = 'some'; // 此项配置决定是否允许上传多文件，如果打开注释则可以上传多个，否则只能上传单个！
					include(JPATH_SITE.'/administrator/templates/system/html/com_media/upload/upload.php');
					?>
					<?php
					/*
						foreach($this->form->getFieldset('image') as $field):
							echo $field->label;
							echo $field->input;
						endforeach;
					*/
					?>
					</ul>
				</li>
				</li>

				<li><div id="custom">
					<?php echo $this->form->getLabel('custombannercode'); ?>
					<?php echo $this->form->getInput('custombannercode'); ?>
				</div>
				</li>

				<li><div id="url">
				<?php echo $this->form->getLabel('clickurl'); ?>
				<?php echo $this->form->getInput('clickurl'); ?>
				</div>
				</li>
<!--
				<li><?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?></li>
-->
				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>

<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start','banner-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo JHtml::_('sliders.panel',JText::_('COM_BANNERS_GROUP_LABEL_PUBLISHING_DETAILS'), 'publishing-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('publish') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
		<fieldset class="panelform">
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('metadata') as $field): ?>
					<li><?php echo $field->label; ?>
						<?php echo $field->input; ?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.end'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>
