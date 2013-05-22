<?php
/**
 * @version		$Id: thumbs_doc.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
$user = JFactory::getUser();
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));

$bg_src = 'media/'.(str_replace('media/','media/images/',$this->_tmp_doc->icon_32));
if (!file_exists(preg_replace('/\\\\/','/',JPATH_SITE).'/'.$bg_src)) {
	$bg_src = 'media/media/images/con_info.png';
}
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a title="<?php echo $this->_tmp_doc->name; ?>" style="display: block; width: 100%; height: 100%;background:url(<?php echo str_replace('/administrator','',JURI::base()).$bg_src; ?>) center center no-repeat;" title="<?php echo $this->_tmp_doc->name; ?>" >
					</a>
				</div>
			</div>
			<div class="controls">
			<?php if ($user->authorise('core.delete','com_media')):?>
				<a class="delete-item" target="_top" href="index.php?option=com_media&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_doc->name; ?>" rel="<?php echo $this->_tmp_doc->name; ?>"><?php echo JHtml::_('image','media/remove.png', JText::_('JACTION_DELETE'), array('width' => 16, 'height' => 16), true); ?></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			<?php endif;?>
			</div>
			<div class="imginfoBorder" title="<?php echo $this->_tmp_doc->name; ?>" >
				<?php echo $this->_tmp_doc->title; ?>
			</div>
		</div>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
