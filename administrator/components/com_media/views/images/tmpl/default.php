<?php
/**
 * @version		$Id: default.php 21051 2011-04-02 05:56:36Z infograf768 $
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
$user = JFactory::getUser();
$type = JRequest::getString('type');  
$unset_watermark = JRequest::getVar('unsetwm');
if ($type) {
	$type_str = '&type='.$type;
}
$get_folder = JRequest::getString('tofolder');
$eshow = JRequest::getString('e_show');
if ($eshow) {
	$e_show_str = '&e_show='.$eshow;
}
if ($get_folder) {
	$this->state->folder = $get_folder;
}
$lock_folder = urldecode(JRequest::getString('lockfolder'));
?>
<script type='text/javascript'>
var lock_folder = '<?php echo $lock_folder; ?>';
var image_base_path = '<?php $params = JComponentHelper::getParams('com_media');
echo $params->get('image_path', 'images');?>/';
function deleteImage(){
	var path = document.getElementById('f_url').value;
	if(path==''){
		alert('<?php echo JText::_('COM_MEDIA_DELETE_SELECT_MESS') ?>');return;
	}
	if(!confirm('<?php echo JText::_('COM_MEDIA_DELETE_SURE_MESS') ?>')){
		return;
	}
	var tofolder = document.getElementById('current_folder_val').value;
	window.location.href='index.php?option=com_media&view=images&tmpl=component&e_name=<?php echo JRequest::getVar('e_name').($eshow ? $e_show_str : '').($type ? $type_str : ''); ?>&asset=<?php echo JRequest::getCmd('asset');?>&author=<?php echo JRequest::getCmd('author');?>&fieldid=<?php echo JRequest::getVar('fieldid'); ?>&task=file.deletefile&filename='+path+'&tofolder'+tofolder+'&lockfolder='+lock_folder;
}
function createFolder(){
	var foldername = document.getElementById('foldername').value;
	if (foldername == null || foldername == '') {
		alert('<?php echo JText::_('COM_MEDIA_CREATE_FOLDER_MESS') ?>');
		return;
	}
	var tofolder = document.getElementById('current_folder_val').value;
	if (tofolder == null || tofolder == '') {
		tofolder = 'root';
	}
	window.location.href='index.php?option=com_media&view=images&tmpl=component&e_name=<?php echo JRequest::getVar('e_name').($eshow ? $e_show_str : '').($type ? $type_str : ''); ?>&asset=<?php echo JRequest::getCmd('asset');?>&author=<?php echo JRequest::getCmd('author');?>&fieldid=<?php echo JRequest::getVar('fieldid'); ?>&<?php echo $this->session->getName().'='.$this->session->getId(); ?>&<?php echo JUtility::getToken();?>=1&task=folder.create&foldername='+foldername+'&tofolder='+tofolder+'&lockfolder='+lock_folder;
}
</script>
<form action="index.php?option=com_media&amp;asset=<?php echo JRequest::getCmd('asset');?>&amp;author=<?php echo JRequest::getCmd('author');?>" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image','media/dots.gif', '...', array('width' =>22, 'height' => 12), true)?>
	</div>
	<fieldset>
		<div class="fltlft">
			<label style="margin:9px 0;" for="folder"><?php echo JText::_('COM_MEDIA_DIRECTORY') ?></label>
			<?php 
				echo $this->folderList;
			?>
			<input type="hidden" id="current_folder_val" value=""/>
			<span id="view_current_folder" class="view_current_folder"></span>
			<button type="button" id="upbutton" title="<?php echo JText::_('COM_MEDIA_DIRECTORY_UP') ?>"><?php echo JText::_('COM_MEDIA_UP') ?></button>
		</div>
		<div class="fltrt">
			<button type="button" onclick="deleteImage();"><?php echo JText::_('COM_MEDIA_DELETE_FILE') ?></button>
			<button type="button" onclick="<?php 
				if ($this->state->get('field.id')):
			?>window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');<?php 
				else:
					if ($type == 'insert'):
			?>ImageManager.onok2();<?php 
					else:
			?>ImageManager.onok();<?php 
					endif;
				endif;
			?>window.parent.SqueezeBox.close();"><?php echo JText::_('COM_MEDIA_INSERT') ?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
		</div>
	</fieldset>

	<iframe id="imageframe" name="imageframe" src="index.php?option=com_media&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo JRequest::getCmd('asset');?>&amp;author=<?php echo JRequest::getCmd('author');?>"></iframe>

	<fieldset>
		<table class="properties">
			<tr>
				<td width="60px"><label for="f_url"><?php echo JText::_('COM_MEDIA_IMAGE_URL') ?></label></td>
				<td><input type="text" id="f_url" value="" /></td>
				<?php if (!$this->state->get('field.id')):?>
					<td><label for="f_align"><?php echo JText::_('COM_MEDIA_ALIGN') ?></label></td>
					<td>
						<select size="1" id="f_align" >
							<option value="" selected="selected"><?php echo JText::_('COM_MEDIA_NOT_SET') ?></option>
							<option value="left"><?php echo JText::_('JGLOBAL_LEFT') ?></option>
							<option value="right"><?php echo JText::_('JGLOBAL_RIGHT') ?></option>
						</select>
					</td>
					<td> <?php echo JText::_('COM_MEDIA_ALIGN_DESC');?> </td>
				<?php endif;?>
			</tr>
			<?php if (!$this->state->get('field.id')):?>
				<tr>
					<td><label for="f_alt"><?php echo JText::_('COM_MEDIA_IMAGE_DESCRIPTION') ?></label></td>
					<td><input type="text" id="f_alt" value="" /></td>
				</tr>
				<tr>
					<td><label for="f_title"><?php echo JText::_('COM_MEDIA_TITLE') ?></label></td>
					<td><input type="text" id="f_title" value="" /></td>
					<td><label for="f_caption"><?php echo JText::_('COM_MEDIA_CAPTION') ?></label></td>
					<td>
						<select size="1" id="f_caption" >
							<option value="" selected="selected" ><?php echo JText::_('JNO') ?></option>
							<option value="1"><?php echo JText::_('JYES') ?></option>
						</select>
					</td>
					<td> <?php echo JText::_('COM_MEDIA_CAPTION_DESC');?> </td>
				</tr>
			<?php endif;?>
		</table>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />
		<input type="hidden" name="type" value="<?php echo $type; ?>"/>
	</fieldset>
</form>

<?php if ($user->authorise('core.create', 'com_media')): ?>
	<fieldset id="uploadform">
		<legend><?php echo JText::_('COM_MEDIA_CREATE_FOLDER') ?></legend>
		<input class="inputbox" type="text" id="foldername" name="foldername" />
		<button type="button" onclick="createFolder()"><?php echo JText::_('COM_MEDIA_CREATE_FOLDER'); ?></button>
	</fieldset>
	
	<form action="<?php echo JURI::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1&amp;asset=<?php echo JRequest::getCmd('asset');?>&amp;author=<?php echo JRequest::getCmd('author');?>&amp;format=<?php echo $this->config->get('enable_flash')=='1' ? 'json' : '' ?>&amp;unsetwm=<?php echo $unset_watermark ? '1' : '0'; ?><?php echo $lock_folder ? '&amp;lockfolder='.$lock_folder : '' ?>" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
		<fieldset id="uploadform">
			<legend><?php echo $this->config->get('upload_maxsize')=='0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
			<fieldset id="upload-noflash" class="actions">
				<input type="hidden" name="type" value="<?php echo $type; ?>"/>
				<label for="upload-file" class="hidelabeltxt"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
				<input type="file" id="upload-file" name="Filedata" />
				<label for="upload-submit" class="hidelabeltxt"><?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></label>
				<input type="submit" id="upload-submit" style="cursor:pointer;" value="<?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>"/>
			</fieldset>
			<div id="upload-flash" class="hide">
				<ul>
					<li><a href="#" id="upload-browse"><?php echo JText::_('COM_MEDIA_BROWSE_FILES'); ?></a></li>
					<li><a href="#" id="upload-clear"><?php echo JText::_('COM_MEDIA_CLEAR_LIST'); ?></a></li>
					<li><a href="#" id="upload-start"><?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></a></li>
				</ul>
				<div class="clr"> </div>
				<p class="overall-title"></p>
				<?php echo JHtml::_('image','media/bar.gif', JText::_('COM_MEDIA_OVERALL_PROGRESS'), array('class' => 'progress overall-progress'), true); ?>
				<div class="clr"> </div>
				<p class="current-title"></p>
				<?php echo JHtml::_('image','media/bar.gif', JText::_('COM_MEDIA_CURRENT_PROGRESS'), array('class' => 'progress current-progress'), true); ?>
				<p class="current-text"></p>
			</div>
			<ul class="upload-queue" id="upload-queue" style="display: none">
				<li style="display: none"></li>
			</ul>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media&view=images&tmpl=component&fieldid='.JRequest::getCmd('fieldid', '').'&e_name='.JRequest::getCmd('e_name').($eshow ? $e_show_str : '').($type ? $type_str : '').'&asset='.JRequest::getCmd('asset').'&author='.JRequest::getCmd('author')); ?>" />
		</fieldset>
	</form>
<?php  endif; ?>
