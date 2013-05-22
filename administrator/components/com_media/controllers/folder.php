<?php
/**
 * @version		$Id: folder.php 21518 2011-06-10 21:38:12Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Folder Media Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.5
 */
class MediaControllerFolder extends JController
{

	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The image directory to delete a file from
	 * @since 1.5
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user	= JFactory::getUser();

		// Get some data from the request
		$tmpl	= JRequest::getCmd('tmpl');
		$paths	= JRequest::getVar('rm', array(), '', 'array');
		$folder = JRequest::getVar('folder', '', '', 'path');

		if ($tmpl == 'component') {
			// We are inside the iframe
			$this->setRedirect('index.php?option=com_media&view=mediaList&folder='.$folder.'&tmpl=component');
		} else {
			$this->setRedirect('index.php?option=com_media&folder='.$folder);
		}

		if (!$user->authorise('core.delete','com_media'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return false;
		}
		else
		{
			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');

			// Initialise variables.
			$ret = true;

			if (count($paths)) {
				JPluginHelper::importPlugin('content');
				$dispatcher	= JDispatcher::getInstance();
				foreach ($paths as $path) {
					/**
					 * 2011-12-12 wengebin 创建！
					 * 要支持中文，作为 windows 服务器，必须将 utf-8 编码的中文字符串转换成 GBK/GB2312 格式！
					 * 否则，在服务器上保存的文件（windows上文件名是GBK/GB2312格式，需要转；
					 * linux上文件名直接保存为UTF-8格式，不用转）是无法找到的！
					 */
					if (IS_WIN) {
						$path = iconv("UTF-8", "GBK", $path);
					}
					
					/**
					 * 2011-12-12 wengebin 注释！
					 * 要支持中文，必须取消安全检查！
					if ($path !== JFile::makeSafe($path)) {
						$dirname = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
						JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_WARNDIRNAME', substr($dirname, strlen(COM_MEDIA_BASE))));
						continue;
					}
					*/

					$fullPath = JPath::clean(COM_MEDIA_BASE . '/' . $folder . '/' . $path);
					$object_file = new JObject(array('filepath' => $fullPath));
					if (is_file($fullPath))
					{
						// Trigger the onContentBeforeDelete event.
						$result = $dispatcher->trigger('onContentBeforeDelete', array('com_media.file', &$object_file));
						if (in_array(false, $result, true)) {
							// There are some errors in the plugins
							JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
							continue;
						}

						$ret &= JFile::delete($fullPath);

						// Trigger the onContentAfterDelete event.
						$dispatcher->trigger('onContentAfterDelete', array('com_media.file', &$object_file));
						
						if (IS_WIN) {
							$return_imgtitle = JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr(iconv('GBK', 'UTF-8', $fullPath), strlen(COM_MEDIA_BASE)));
						} else {
							$return_imgtitle = JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE)));
						}
						$this->setMessage($return_imgtitle);
					}
					else if (is_dir($fullPath))
					{
						if (count(JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS','.DS_Store','__MACOSX'), array('index.html', '^\..*','.*~'))) == 0)
						{
							// Trigger the onContentBeforeDelete event.
							$result = $dispatcher->trigger('onContentBeforeDelete', array('com_media.folder', &$object_file));
							if (in_array(false, $result, true)) {
								// There are some errors in the plugins
								JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
								continue;
							}

							$ret &= !JFolder::delete($fullPath);

							// Trigger the onContentAfterDelete event.
							$dispatcher->trigger('onContentAfterDelete', array('com_media.folder', &$object_file));
							
							if (IS_WIN) {
								$return_imgtitle = JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr(iconv('GBK', 'UTF-8', $fullPath), strlen(COM_MEDIA_BASE)));
							} else {
								$return_imgtitle = JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE)));
							}
							$this->setMessage($return_imgtitle);
						}
						else
						{
							//This makes no sense...
							JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY',substr($fullPath, strlen(COM_MEDIA_BASE))));
						}
					}
				}
			}
			return $ret;
		}
	}

	/**
	 * Create a folder
	 *
	 * @param string $path Path of the folder to create
	 * @since 1.5
	 */
	function create()
	{
		$tofolder		= JRequest::getVar('tofolder');
		$lockfolder		= JRequest::getString('lockfolder');
		// Check for request forgeries
		JRequest::checkToken($tofolder ? 'get' : '') or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();

		$folder			= JRequest::getCmd('foldername', '');
		$folderCheck	= JRequest::getVar('foldername', null, '', 'string', JREQUEST_ALLOWRAW);
		$parent			= JRequest::getVar('folderbase', '', '', 'path');
		$fieldid		= JRequest::getString('fieldid');
		
		if ($tofolder) {
			$type = JRequest::getString('type');  
			if ($type) {
				$type_str = '&type='.$type;
			}
			$eshow = JRequest::getString('e_show');
			if ($eshow) {
				$e_show_str = '&e_show='.$eshow;
			}
			if ($tofolder == 'root') {
				$tofolder = '';
			}
			$parent = $tofolder;
			$this->setRedirect('index.php?option=com_media&view=images&tmpl=component&e_name='.JRequest::getVar('e_name').($eshow ? $e_show_str : '').($type ? $type_str : '').'&asset='.JRequest::getVar('asset').'&author='.JRequest::getVar('author').($tofolder ? '&tofolder='.$tofolder : '').($lockfolder ? '&lockfolder='.$lockfolder : ''), '', 'message');
		} else {
			$this->setRedirect('index.php?option=com_media&folder='.$parent.'&tmpl='.JRequest::getCmd('tmpl', 'index'));
		}
		//echo $parent;exit;
		if (strlen($folder) > 0)
		{
			if (!$user->authorise('core.create','com_media'))
			{
				// User is not authorised to delete
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');

			JRequest::setVar('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck)) {
				$this->setMessage(JText::_('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));
				return false;
			}

			$path = JPath::clean(COM_MEDIA_BASE . '/' . $parent . '/' . $folder);
			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new JObject(array('filepath' => $path));
				JPluginHelper::importPlugin('content');
				$dispatcher	= JDispatcher::getInstance();
				$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.folder', &$object_file));
				if (in_array(false, $result, true)) {
					// There are some errors in the plugins
					JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					continue;
				}

				JFolder::create($path);
				$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
				JFile::write($path . "/index.html", $data);

				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_media.folder', &$object_file, true));
				$this->setMessage(JText::sprintf('COM_MEDIA_CREATE_COMPLETE', substr($path, strlen(COM_MEDIA_BASE))));
			}
			JRequest::setVar('folder', ($parent) ? $parent.'/'.$folder : $folder);
			$this->setRedirect($this->getRedirect().'&fieldid='.$fieldid);
		}
	}
}
