
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$params = $this->form->getFieldsets('params');
?>
<form action='<?php echo JRoute::_('index.php?option=com_service&layout=edit&id='.(int) $this->item->id); ?>' method='post' name='adminForm' id='service-form' class='form-validate'>
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
			<legend><?php echo JText::_( 'COM_SERVICE_SERVICE_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
<?php
	foreach($this->form->getFieldset('details') as $field):
		if ($field->name == 'jform[pros]') {
?>
				<li>
					<?php echo $this->form->getLabel('pros'); ?>
					<input type="hidden" id="jform_pros" name="jform[pros]" value="<?php echo $this->item->pros; ?>" />
					<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_selectpro&view=selectpros&tmpl=component&selectTable=product&showFields=title|项目名称-clienname|客户名称&searchField=title&isset=1" class="to_choise_pros" id="">选择案例</a>
					<script type="text/javascript">
						SqueezeBox.initialize({});
						SqueezeBox.assign($$('a.to_choise_pros'), {
							parse: 'rel'
						});
					</script>
					<div class="clr"></div>
					<div class="choise_pros">
					<?php
						if (count($this->item->pros_array)) {
							foreach ($this->item->pros_array as $pro) {
								echo '<div class="pitem" id="div'.$pro->id.'">'.$pro->title.'('.$pro->id.')--<a class="cartdelete" href="javascript:;" onclick="document.formvalidator.removeItem(this);">删除</a><input type="hidden" value="'.$pro->id.'" name="p[]"/></div>';
							}
						}
					?>
					</div>
					<div class="clr"></div>
				</li>
<?php 
		} else if ($field->name == 'jform[image]') {
			/**
			 * 开始配置文件上传的各项属性
			 */
			$set_swfupload_title = $this->form->getLabel('image'); // 上传标题，比如：坐标点图片
			$file_upload_folder = $upload_uri; // 文件上传的路径
			$file_input_id = 'jform_image'; // 文件上传完成，路径填充的 input id
			$file_input_name = 'jform[image]'; // 文件上传完成，路径填充的 input name
			$file_value = $this->item->image; // input 中默认填写的文件路径，已上传的都会有路径
			$file_view_id = 'view_image'; // 图片预览的 img 标签的 id，不需要预览可以注释！
			$file_norename = 0; // 是否自动对文件进行重命名，默认会进行重命名，所以可以多次上传同名文件！
			$file_unsetwm = 1; // 设置上传的图片是否带水印，1 为不带水印，注释后如果开启了水印则会有水印！
			include(JPATH_SITE.'/administrator/templates/system/html/com_media/upload/upload.php');
		} else {
?>
				<li><?php
				echo $field->label;
				if (strtolower($field->type) == 'editor') {
					echo '<div class="clr"></div>';
				}
				echo $field->input;
				?></li>
<?php 
		}
endforeach; ?>
			</ul>
	</div>
 
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'service-slider'); ?>
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
		<fieldset class="panelform">
			<?php echo $this->loadTemplate('metadata'); ?>
		</fieldset>
 		
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type='hidden' name='task' value='service.edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>