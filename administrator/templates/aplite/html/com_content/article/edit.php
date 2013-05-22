<?php
/**
 * @version		$Id: edit.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'article.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_content&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-100">
		<?php echo JHtml::_('tabs.start','content-tabs-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('tabs.panel',JText::_('COM_CONTENT_FIELDSET_ARTICLE'), 'article-details'); ?>
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li>
				<?php echo $this->form->getLabel('title'); ?><?php echo $this->form->getInput('title'); ?>
				</li>
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
				
				/**
				 * 开始配置文件上传的各项属性
				 */
				$set_swfupload_title = $this->form->getLabel('images'); // 上传标题，比如：坐标点图片
				$file_upload_folder = $upload_uri; // 文件上传的路径
				$file_input_id = 'jform_images'; // 文件上传完成，路径填充的 input id
				$file_input_name = 'jform[images]'; // 文件上传完成，路径填充的 input name
				$file_value = $this->item->images; // input 中默认填写的文件路径，已上传的都会有路径
				$file_view_id = 'view_images'; // 图片预览的 img 标签的 id，不需要预览可以注释！
				$file_norename = 1; // 是否自动对文件进行重命名，默认会进行重命名，所以可以多次上传同名文件，如果注释则文件名会被重命名，因此会导致预览失败，如果需要“预览”，请加上该句代码阻止重命名！
				//$file_unsetwm = 1; // 设置上传的图片是否带水印，1 为不带水印，注释后如果开启了水印则会有水印！
				include(JPATH_SITE.'/administrator/templates/system/html/com_media/upload/upload.php');
				?>
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getInput('articletext'); ?>
		</fieldset>

			<?php echo JHtml::_('tabs.panel',JText::_('COM_CONTENT_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?></li>
	
					<li><?php echo $this->form->getLabel('catid'); ?>
					<?php echo $this->form->getInput('catid'); ?></li>
	
					<li><?php echo $this->form->getLabel('state'); ?>
					<?php echo $this->form->getInput('state'); ?></li>
	
					<li><?php echo $this->form->getLabel('access'); ?>
					<?php echo $this->form->getInput('access'); ?></li>
					<?php if ($this->canDo->get('core.admin')): ?>
						<li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
							<div class="button2-left"><div class="blank">
								<button type="button" onclick="document.location.href='#access-rules';">
									<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
								</button>
							</div></div>
						</li>
					<?php endif; ?>
					<li><?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?></li>
	
					<li><?php echo $this->form->getLabel('featured'); ?>
					<?php echo $this->form->getInput('featured'); ?></li>
	
					<li><?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?></li>
									
					<li><?php echo $this->form->getLabel('created_by'); ?>
					<?php echo $this->form->getInput('created_by'); ?></li>

					<li><?php echo $this->form->getLabel('created_by_alias'); ?>
					<?php echo $this->form->getInput('created_by_alias'); ?></li>

					<li><?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?></li>

					<li><?php echo $this->form->getLabel('publish_up'); ?>
					<?php echo $this->form->getInput('publish_up'); ?></li>

					<li><?php echo $this->form->getLabel('publish_down'); ?>
					<?php echo $this->form->getInput('publish_down'); ?></li>

					<?php if ($this->item->modified_by) : ?>
						<li><?php echo $this->form->getLabel('modified_by'); ?>
						<?php echo $this->form->getInput('modified_by'); ?></li>

						<li><?php echo $this->form->getLabel('modified'); ?>
						<?php echo $this->form->getInput('modified'); ?></li>
					<?php endif; ?>

					<?php if ($this->item->version) : ?>
						<li><?php echo $this->form->getLabel('version'); ?>
						<?php echo $this->form->getInput('version'); ?></li>
					<?php endif; ?>

					<?php if ($this->item->hits) : ?>
						<li><?php echo $this->form->getLabel('hits'); ?>
						<?php echo $this->form->getInput('hits'); ?></li>
					<?php endif; ?>
				</ul>
			</fieldset>

			<?php $fieldSets = $this->form->getFieldsets('attribs');?>
			<?php foreach ($fieldSets as $name => $fieldSet) :?>
				<?php echo JHtml::_('tabs.panel',JText::_($fieldSet->label), $name.'-options');?>
				<?php if (isset($fieldSet->description) && trim($fieldSet->description)) :?>
					<p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
				<?php endif;?>
				<fieldset class="panelform">
					<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
					<?php endforeach; ?>
					</ul>
				</fieldset>
			<?php endforeach; ?>

			<?php echo JHtml::_('tabs.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
			<fieldset class="panelform">
				<?php echo $this->loadTemplate('metadata'); ?>
			</fieldset>
			
			<?php if ($this->canDo->get('core.admin')): ?>
			
							<?php echo JHtml::_('tabs.panel',JText::_('COM_CONTENT_FIELDSET_RULES'), 'access-rules'); ?>
			
							<fieldset class="panelform">
								<?php echo $this->form->getLabel('rules'); ?>
								<?php echo $this->form->getInput('rules'); ?>
							</fieldset>
			
				<?php endif; ?>

		<?php echo JHtml::_('tabs.end'); ?>
	</div>

	<div class="clr"></div>
	
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

