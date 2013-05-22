
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$params = $this->form->getFieldsets('params');
?>
<form action='<?php echo JRoute::_('index.php?option=com_product&layout=edit&id='.(int) $this->item->id); ?>' method='post' name='adminForm' id='product-form' class='form-validate'>
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
			<legend><?php echo JText::_( 'COM_PRODUCT_PRODUCT_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
<?php
foreach($this->form->getFieldset('details') as $field):
	if ($field->name == 'jform[about]') {
?>
		<li>
			<?php echo $this->form->getLabel('about'); ?>
			<input type="hidden" id="jform_about" name="jform[about]" value="<?php echo $this->item->about; ?>" />
			<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_selectpro&view=selectpros&tmpl=component&selectTable=product&showFields=title|产品型号-brandname|品牌名&searchField=title&isset=1" class="to_choise_pros" id="">选择产品(选3个)</a>
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
				<div class="clr"></div>
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
		$file_unsetwm = 0; // 设置上传的图片是否带水印，1 为不带水印，注释后如果开启了水印则会有水印！
		$upload_count = 0; // 设置是否开启多文件上传，0为不开启只能上传单个文件，其他参数都视为开启上传多文件！
		$uploaded_view_allimgs = 0; // 设置上传后是否显示所有图片的预览，1为开启，0为关闭，默认不开启！
		$upload_topro_pid = 0; // 设置是否开启图片描述，如果不开启这赋值为0即可，如果传递了id，则需要数据库有 imgdesc 字段，默认不开启！
		$upload_topro_table = 'product'; // 设置图片描述所在的数据库表，如果为 product 则表示修改 #__product 表中的 imgdesc 字段，默认为 product！
		include(JPATH_SITE.'/administrator/templates/system/html/com_media/upload/upload.php');
	} else if ($field->name == 'jform[proimgs]') {
		/**
		 * 开始配置文件上传的各项属性
		 */
		$set_swfupload_title = $this->form->getLabel('proimgs'); // 上传标题，比如：坐标点图片
		$file_upload_folder = $upload_uri.'/proimgs'; // 文件上传的路径
		$file_input_id = 'jform_proimgs'; // 文件上传完成，路径填充的 input id
		$file_input_name = 'jform[proimgs]'; // 文件上传完成，路径填充的 input name
		$file_value = $this->item->proimgs; // input 中默认填写的文件路径，已上传的都会有路径
		$file_view_id = ''; // 图片预览的 img 标签的 id，不需要预览可以注释！
		$file_norename = 0; // 是否自动对文件进行重命名，默认会进行重命名，所以可以多次上传同名文件！
		$file_unsetwm = 0; // 设置上传的图片是否带水印，1 为不带水印，注释后如果开启了水印则会有水印！
		$upload_count = 1; // 设置是否开启多文件上传，0为不开启只能上传单个文件，其他参数都视为开启上传多文件！
		$uploaded_view_allimgs = 1; // 设置上传后是否显示所有图片的预览，1为开启，0为关闭，默认不开启！
		$upload_topro_pid = $this->item->id; // 设置是否开启图片描述，如果不开启这赋值为0即可，如果传递了id，则需要数据库有 imgdesc 字段，默认不开启！
		$upload_topro_table = 'product'; // 设置图片描述所在的数据库表，如果为 product 则表示修改 #__product 表中的 imgdesc 字段，默认为 product！
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
endforeach;
?>
			</ul>
	</div>
 
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', 'product-slider'); ?>
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
		<input type='hidden' name='task' value='product.edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>