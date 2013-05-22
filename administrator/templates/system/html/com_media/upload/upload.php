<?php
/**
 * Created on 2011-12-9
 *
 * Created by wengebin
 * 如何使用Upload，以下是配置文件，必须！
 * 
 * 
 * 初始化配置，只需要调用一次即可，注意引入“jimport('mulan.mldb')”
 * 初始化后会得到当前文件上传的路径（如果是修改则给出对应的文件夹，如果是新建则给出一个临时文件夹并将临时文件夹放入“tempFolder”输入框中，后台获取后可对此文件夹重命名）

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
		
 * 开始配置文件上传的各项属性
	
		$set_swfupload_title = $this->form->getLabel('image'); // 上传标题，比如：坐标点图片
		$file_upload_folder = $upload_uri; // 文件上传的路径
		$file_input_id = 'jform_image'; // 文件上传完成，路径填充的 input id
		$file_input_name = 'jform[image]'; // 文件上传完成，路径填充的 input name
		$file_value = $this->item->image; // input 中默认填写的文件路径，已上传的都会有路径
		$file_view_id = 'view_image'; // 图片预览的 img 标签的 id，不需要预览可以注释！
		$file_norename = 0; // 是否自动对文件进行重命名，默认会进行重命名，所以可以多次上传同名文件！
		$file_unsetwm = 1; // 设置上传的图片是否带水印，1 为不带水印，注释后如果开启了水印则会有水印！
		$file_upload_types = array('compressionfile'); // 设置允许上传文件的类型，数组中的值可以是：noimg(不允许上传图片)/media(允许上传图片、mp4、flv视频)/document(图片、允许上传普通文档)/compressionfile(允许上传图片、压缩文件) 四个，noimg 可以去掉图片文件类型，如果需要，还可以在该文件内进行扩展，默认可以上传图片！
		
		$upload_count = 1; // 设置是否开启多文件上传，0为不开启只能上传单个文件，其他参数都视为开启上传多文件！
		$uploaded_view_allimgs = 1; // 设置上传后是否显示所有图片的预览，1为开启，0为关闭，默认不开启！
		$upload_topro_pid = 0; // 设置是否开启图片描述($this->item->id)，如果不开启这赋值为0即可，如果传递了id，则需要数据库有 imgdesc 字段，默认不开启！
		$upload_topro_table = 'product'; // 设置图片描述所在的数据库表，如果为 product 则表示修改 #__product 表中的 imgdesc 字段，默认为 product！
		
 * 最后引入此文件即可，可多次引入，在同一页面可多次使用上传组件！
		include(JPATH_SITE.'/administrator/templates/system/html/com_media/upload/upload.php');

 */


defined('_JEXEC') or die;
$session = JFactory::getSession();
$session_id = $session->getId();
$session_name = $session->getName();

$upload_num = $upload_num ? ++$upload_num : 1;

$file_upload_baseurl = 'images';
$file_upload_folder = $file_upload_folder ? $file_upload_folder : 'img';
$upload_topro_table = $upload_topro_table ? $upload_topro_table : 'product';

$upload_others_type = array('noimg'=>'*.jpg;*.jpeg;*.png;*.gif;');
if (is_array($file_upload_types)) {
	foreach ($file_upload_types as $type) {
		switch ($type) {
			case 'media' :
				$upload_others_type[$type] = '*.mp4;*.flv;';
				break;
			case 'document' :
				$upload_others_type[$type] = '*.doc;*.docx;*.pdf;';
				break;
			case 'compressionfile' :
				$upload_others_type[$type] = '*.rar;*.zip;';
				break;
			case 'noimg' :
				unset($upload_others_type[$type]);
				break;
		}
	}
}
?>
<?php
if ($upload_num == 1) {
?>
<script type="text/javascript" src="<?php echo JURI::root(); ?>templates/system/js/swfupload.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>templates/system/js/swfupload.queue.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>templates/system/js/fileprogress.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>templates/system/js/handlers.js"></script>
<?php
}
?>
<script type="text/javascript">
var swfu_<?php echo $upload_num; ?>;
file_upload_baseurl = "<?php echo $file_upload_baseurl; ?>";
file_upload_folder_<?php echo $upload_num; ?> = "<?php echo $file_upload_folder; ?>";
file_input_id_<?php echo $upload_num; ?> = "<?php echo $file_input_id; ?>";
file_view_id_<?php echo $upload_num; ?> = "<?php echo $file_view_id; ?>";
uploaded_view_allimgs_<?php echo $upload_num; ?> = "<?php echo $uploaded_view_allimgs; ?>";
$(document).ready(function(){
	var settings_<?php echo $upload_num; ?> = {
		swfid : <?php echo $upload_num; ?>,
		flash_url : "<?php echo JURI::root(); ?>templates/system/js/swfupload.swf",
		flash9_url : "<?php echo JURI::root(); ?>templates/system/js/swfupload_fp9.swf",
		upload_url: "<?php echo JURI::base(); ?>index.php?option=com_media&task=file.swfupload&tmpl=component&<?php echo $session_name.'='.$session_id; ?>&<?php echo JUtility::getToken();?>=1&asset=<?php echo JRequest::getCmd('asset');?>&author=<?php echo JRequest::getCmd('author');?>&format=<?php echo $file_norename ? '&norename=1' : '';echo $file_unsetwm ? '&unsetwm=1' : ''; ?>&folder=<?php echo $file_upload_folder; ?>",
		post_params: {},
		file_size_limit : "100 MB",
		file_types : "<?php echo implode('',$upload_others_type); ?>",
		file_types_description : "<?php echo JText::_('JTOOLBAR_UPLOAD_CAN_SELECT'); ?>",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress_<?php echo $upload_num; ?>",
			cancelButtonId : "btnCancel_<?php echo $upload_num; ?>"
		},
		debug: false,

		// Button settings
		button_image_url: "<?php echo JURI::root(); ?>templates/system/js/TestImageNoText_65x29.png",
		button_width: "65",
		button_height: "29",
		button_placeholder_id: "spanButtonPlaceHolder_<?php echo $upload_num; ?>",
		button_text: '<span class="theFont"><?php echo JText::_('JTOOLBAR_UPLOAD_SELECT'); ?></span>',
		button_text_style: ".theFont { font-size: 12; }",
		button_text_left_padding: 6,
		button_text_top_padding: 5,
		<?php
		if (!$upload_count) {
			echo 'button_action: SWFUpload.BUTTON_ACTION.SELECT_FILE,';
		} else {
			echo 'button_action: SWFUpload.BUTTON_ACTION.SELECT_FILES,';
		}
		?>
		
		// The event handler functions are defined in handlers.js
		swfupload_preload_handler : preLoad,
		swfupload_load_failed_handler : loadFailed,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete
	};
	swfu_<?php echo $upload_num; ?> = new SWFUpload(settings_<?php echo $upload_num; ?>);
});
</script>
<li>
<?php
if ($set_swfupload_title) {
	echo $set_swfupload_title;
} else {
?>
	<label title="<?php echo JText::_('JTOOLBAR_UPLOAD_ADDRESS'); ?>"><?php echo JText::_('JTOOLBAR_UPLOAD_ADDRESS'); ?></label>
<?php
}

if (!$uploaded_view_allimgs) {
?>
	<input style="margin-right:10px;" type="text" id="<?php echo $file_input_id ? $file_input_id : 'filename'; ?>" name="<?php echo $file_input_name ? $file_input_name : 'filename'; ?>" size="40" value="<?php echo isset($file_value) && $file_value != '' ? $file_value : $default_val; ?>" readonly="readonly" />
<?php
}
?>
	<span id="spanButtonPlaceHolder_<?php echo $upload_num; ?>"></span>
	<input class="btnCancel" id="btnCancel_<?php echo $upload_num; ?>" type="button" value="<?php echo JText::_('JTOOLBAR_UPLOAD_CANCEL'); ?>" onclick="swfu_<?php echo $upload_num; ?>.cancelQueue();" disabled="disabled" style="border: 1px solid #CCC;padding: 0 8px; font-size: 12px; height: 29px;" />
<?php
if ($uploaded_view_allimgs) {
?>
	<input type="hidden" name="<?php echo $file_input_name ? $file_input_name : 'filename'; ?>" value="<?php echo $file_upload_baseurl.'/'.$file_upload_folder; ?>"/>
	<script type="text/javascript">
		var foldersrc = file_upload_baseurl+'/<?php echo $file_upload_folder; ?>';
		var vid = <?php echo $upload_topro_pid; ?>;
		function chngImage(src) {
			jQuery('#comp_preview_div').html('<div class="execute_now">处理中...</div>');
			jQuery.ajax({
				type: "POST",
				url: "<?php echo JURI::base(); ?>index.php?option=com_media&task=viewFolderImgsAndDesc&table=<?php echo $upload_topro_table; ?>&fs="+foldersrc<?php echo $upload_topro_pid ? '+"&vid="+vid' : ''; ?>,
				data: "",
				success: function(get_data){
					jQuery('#comp_preview_div').html(get_data);
				}
			});
		}
		
		function deleteOneImg(img,num) {
			if (confirm('删除后不可恢复，确认要删除？')) {
				jQuery('#comp_preview_div').html('<div class="execute_now">处理中...</div>');
				jQuery.ajax({
					type: "POST",
					url: "<?php echo JURI::base(); ?>index.php?option=com_media&task=deleteOneImg&table=<?php echo $upload_topro_table; ?>&img="+img+"&num="+num<?php echo $upload_topro_pid ? '+"&vid="+vid' : ''; ?>,
					data: "",
					success: function(get_data){
						if (get_data == 'false') {
							alert('删除失败，请稍后再试');
						}
						chngImage('nothing');
					}
				});
			}
		}
		
		function deleteDir() {
			if (confirm('删除后不可恢复，确认要删除？')) {
				jQuery('#comp_preview_div').html('<div class="execute_now">处理中...</div>');
				jQuery.ajax({
					type: "POST",
					url: "<?php echo JURI::base(); ?>index.php?option=com_media&task=deleteDir&table=<?php echo $upload_topro_table; ?>&dir="+foldersrc<?php echo $upload_topro_pid ? '+"&vid="+vid' : ''; ?>,
					data: "",
					success: function(get_data){
						if (get_data == 'false') {
							alert('删除失败，请稍后再试');
						}
						chngImage('nothing');
					}
				});
			}
		}
		chngImage('nothing');
	</script>
	<a class="to_view_imgs" href="javascript:;" onclick="chngImage('nothing')">刷新图片</a>
	<a class="to_delete_imgs" href="javascript:;" onclick="deleteDir('nothing')">删除所有</a>
<?php
} else {
?>
<a class="to_delete_imgs" href="javascript:;" onclick="document.getElementById('<?php echo $file_input_id ? $file_input_id : 'filename'; ?>').value='';">清除地址</a>
<?php
}
?>
	<div class="clr"></div>
	
	<div class="fieldset flash" style="display:none;" id="fsUploadProgress_<?php echo $upload_num; ?>">
		<span class="legend"><?php echo JText::_('JTOOLBAR_UPLOAD_PROGRESS'); ?></span>
	</div>
	<div id="divStatus"></div>
	<div class="clr"></div>
</li>
<li>
<?php
if ($file_view_id && !$uploaded_view_allimgs) {
	if (!isset($file_value) || $file_value == '') $file_value = $default_val;
?>
	<label class="hasTip required" title="<?php echo JText::_('JTOOLBAR_UPLOAD_PREVIEW'); ?>" aria-invalid="true"><?php echo JText::_('JTOOLBAR_UPLOAD_PREVIEW'); ?></label>
	<?php echo $file_value ? '<img style="width:120px;" class="'.$file_view_id.'" id="'.$file_view_id.'" src="../'.$file_value.'" />' : '<img style="width:120px;" class="'.$file_view_id.'" id="'.$file_view_id.'" />'; ?>
	<div class="clr"></div>
<?php
}
?>

<?php
if ($uploaded_view_allimgs) {
?>
<div id="comp_preview_div"></div>
<?php
}
?>
</li>