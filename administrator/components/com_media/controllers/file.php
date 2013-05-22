<?php
/**
 * @version		$Id: file.php 21518 2011-06-10 21:38:12Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Media File Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.5
 */
class MediaControllerFile extends JController
{
	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the user
		$user		= JFactory::getUser();

		// Get some data from the request
		$file		= JRequest::getVar('Filedata', '', 'files', 'array');
		$folder		= JRequest::getVar('folder', '', '', 'path');
		$return		= JRequest::getVar('return-url', null, 'post', 'base64');
		$norename	= JRequest::getVar('norename');
		$lockfolder = JRequest::getString('lockfolder');
		
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		
		// Set the redirect
		if ($return) {
			$this->setRedirect(base64_decode($return).'&folder='.$folder.($lockfolder ? '&lockfolder='.$lockfolder : ''));
		}

		// Make the filename safe
		preg_match('/.*(\.\w+)$/',$file['name'],$match);
		if (!$norename) {
			$file['name'] = preg_replace('/(\.\w+)$/','-'.strtotime('now').$match[1],$file['name']);
		}
		
		// 为了支持中文文件名，必须将文件名转码：
		jimport('mulan.mlstring');
		if (IS_WIN && MulanStringUtil::is_utf8($file['name'])) {
			$file['name'] = iconv("UTF-8", "GBK", $file['name']);
		}
		/**
		 * 2011-12-9 wengebin 注释！
		 * 去掉非法字符验证！
		 * $file['name'] = JFile::makeSafe($file['name']);
		 */
		 
		if (isset($file['name']))
		{
			// The request is valid
			$err = null;
			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				JError::raiseNotice(100, JText::_($err));
				return false;
			}

			$filepath = JPath::clean(COM_MEDIA_BASE . '/' . $folder . '/' . $file['name']);

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}
			$file = (array) $object_file;

			if (JFile::exists($file['filepath']))
			{
				// File exists
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));
				return false;
			}
			elseif (!$user->authorise('core.create', 'com_media'))
			{
				// File does not exist and user is not authorised to create
				JError::raiseWarning(403, JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				/**
				 * 2011-10-xx wengebin 新增！
				 * 改段代码用于为上传的图片添加水印，可以在 media 组件里设置水印开关，png 图片不会被添加水印！
				 */
				jimport('mulan.mlimage');
				jimport('mulan.mldb');
				$issetWaterMark = MulanDBUtil::getConfigByKey('activewm');
				$unset_watermark = JRequest::getVar('unsetwm');
				
				if ($issetWaterMark && !$unset_watermark && $file['type'] != 'image/png') {
					$this->addWatermark($filepath);
				}
				
				$params = JComponentHelper::getParams('com_media');
				$thumb_filepath = JPath::clean($params->get('image_path', 'images').DS.$folder.DS.$file['name']);
				// MulanImageUtil::thumbimage($thumb_filepath,80,80);
				
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file,true));
				
				if (IS_WIN) {
					$return_imgtitle = JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr(iconv("GBK", "UTF-8", $file['filepath']), strlen(COM_MEDIA_BASE)));
				} else {
					$return_imgtitle = JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE)));
				}
				$this->setMessage($return_imgtitle);
				return true;
			}
		}
		else
		{
			$this->setRedirect('index.php', JText::_('COM_MEDIA_INVALID_REQUEST'), 'error');
			return false;
		}
	}
	
	/**
	 * 2012-02-16 wengebin add!
	 * 
	 * Upload a file in flash
	 *
	 * @since 1.5
	 */
	function swfupload()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the user
		$user		= JFactory::getUser();

		// Get some data from the request
		$file		= JRequest::getVar('Filedata', '', 'files', 'array');
		$folder		= JRequest::getVar('folder', '', '', 'path');
		$norename	= JRequest::getVar('norename');
		$lockfolder = JRequest::getString('lockfolder');
		
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		if ($norename == null || !isset($norename) || !$norename) {
			$file['name'] = time().mt_rand().'_'.$file['name'];
		}
		
		// 为了支持中文文件名，必须将文件名转码：
		jimport('mulan.mlstring');
		$is_utf_8 = true;
		if (!IS_WIN && !MulanStringUtil::is_utf8($file['name'])) {
			$file['name'] = iconv("GBK", "UTF-8", $file['name']);
			$is_utf_8 = true;
		} else if (IS_WIN && MulanStringUtil::is_utf8($file['name'])) {
			$file['name'] = iconv("UTF-8", "GBK", $file['name']);
			$is_utf_8 = false;
		}
		/**
		 * 2011-12-9 wengebin 注释！
		 * 去掉非法字符验证！保证中文文件能够上传！
		 * $file['name'] = JFile::makeSafe($file['name']);
		 */
		
		if (isset($file['name']))
		{
			// The request is valid
			$err = null;
			if (!MediaHelper::canUpload($file, $err)) {
				echo '{state:100,mess:'.$err.'}';
				exit;
			}

			$filepath = JPath::clean(COM_MEDIA_BASE . '/' . $folder . '/' . $file['name']);

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file));
			if (in_array(false, $result, true)) {
				echo '{state:100,mess:\''.JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)).'\'}';
				exit;
			}
			$file = (array) $object_file;

			if (JFile::exists($file['filepath'])) {
				echo '{state:100,mess:\''.JText::_('COM_MEDIA_ERROR_FILE_EXISTS').'\'}';
				exit;
			} elseif (!$user->authorise('core.create', 'com_media')) {
				echo '{state:493,mess:\''.JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED').'\'}';
				exit;
			}

			if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
				echo '{state:100,mess:\''.JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE').'\'}';
				exit;
			} else {
				/**
				 * 2011-10-xx wengebin 新增！
				 * 改段代码用于为上传的图片添加水印，可以在 media 组件里设置水印开关，png 图片不会被添加水印！
				 */
				jimport('mulan.mlimage');
				jimport('mulan.mldb');
				$issetWaterMark = MulanDBUtil::getConfigByKey('activewm');
				$unset_watermark = JRequest::getVar('unsetwm');
				
				if ($issetWaterMark && !$unset_watermark && $file['type'] != 'image/png') {
					$this->addWatermark($filepath);
				}
				
				$params = JComponentHelper::getParams('com_media');
				$thumb_filepath = JPath::clean($params->get('image_path', 'images').DS.$folder.DS.$file['name']);
				// MulanImageUtil::thumbimage($thumb_filepath,80,80);
				
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file,true));
				
				$ret_filepath = $file['name'];
				if (!$is_utf_8) {
					$ret_filepath = iconv("GBK", "UTF-8", $ret_filepath);
				}
				
				echo '{state:200,mess:\''.JText::_('COM_MEDIA_UPLOAD_COMPLETE').'\',filename:\''.$ret_filepath.'\'}';
				exit;
			}
		}
		else
		{
			echo '{state:100,mess:\''.JText::_('COM_MEDIA_INVALID_REQUEST').'\'}';
			exit;
		}
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The image directory to delete a file from
	 * @since 1.5
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
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

			if (count($paths))
			{
				JPluginHelper::importPlugin('content');
				$dispatcher	= JDispatcher::getInstance();
				foreach ($paths as $path)
				{
					if (IS_WIN) {
						$path = iconv("UTF-8", "GBK", $path);
					}
					
					/* 
					if ($path !== JFile::makeSafe($path))
					{
						// filename is not safe
						$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
						JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));
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
						
						/**
						 * 2011-12-9 wengebin 修改！
						 * 要支持中文，作为 windows 服务器，必须将 GBK/GB2312 格式的字符串转换成 UTF-8，
						 * 因为 windows 服务器上读取的文件名默认是 GBK/GB2312 格式，
						 * 而 linux 读取到的文件名默认是 UTF-8 格式，
						 * 而我们的程序是 UTF-8，所以得出：windows 需要转换，而 linux 不需要转换！
						 */
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

							$ret &= JFolder::delete($fullPath);

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
	
	function cancel() {
		$this->setRedirect('index.php?option=com_media', JText::_('COM_MEDIA_WATERMARK_CANCEL'), 'message');
	}
	
	/**
	 * 2011-10-xx wengebin 新增！
	 * 
	 * 该方法用于保存水印设置！
	 * 
	 * Save watermark config!
	 */
	function saveWatermark(){
		$published = JRequest::getVar('published');
		$image = JRequest::getVar('image');
		$position = JRequest::getVar('position');
		$x = JRequest::getVar('x');
		$y = JRequest::getVar('y');
		$opacitywm = JRequest::getVar('opacitywm');
		
		jimport('mulan.mldb');
		$r1 = MulanDBUtil::setConfigByKey('offxwm',$x);
		$r2 = MulanDBUtil::setConfigByKey('offywm',$y);
		$r3 = MulanDBUtil::setConfigByKey('positionwm',$position);
		$r4 = MulanDBUtil::setConfigByKey('imagewm',$image);
		$r5 = MulanDBUtil::setConfigByKey('activewm',$published);
		$r6 = MulanDBUtil::setConfigByKey('opacitywm',$opacitywm);
		if(!$r1||!$r2||!$r3||!$r4||!$r5||!$r6){
			$this->setRedirect('index.php?option=com_media', JText::_('COM_MEDIA_WATERMARK_FAIL'), 'error');
		}else{
			$this->setRedirect('index.php?option=com_media', JText::_('COM_MEDIA_WATERMARK_SUCCESS'), 'message');
		}
	}
	
	/**
	 * 2011-10-xx wengebin 新增！
	 * 
	 * 该方法用于添加水印，上传文件后如果需要添加水印这会调用此方法为图片添加水印！
	 * 
	 * Add watermark!
	 */
	function addWatermark($source){
		$source_info	= getimagesize($source);
		$width			= $source_info[0];	//获取宽
		$height			= $source_info[1];	//获取高
		switch($source_info[2])				//新建图片
		{
			case 1 :
				$source_img = imagecreatefromgif($source);
				break;
			case 2 :
				$source_img = imagecreatefromjpeg($source);
				break;
			case 3 :
				$source_img = imagecreatefrompng($source);
				imagesavealpha($source_img, true);
				break;
			default :	//不是图片
				return false;
		}
		
		$wmimage = JPATH_ROOT.'/'.MulanDBUtil::getConfigByKey('imagewm');
		if(file_exists($wmimage)){					//水印文件是否存在,和激活
			$water_info   = getimagesize($wmimage);	//水印信息
	   		$wm_width     = $water_info[0];
	   		$wm_height    = $water_info[1];
			
		  	switch ($water_info[2]) {
		    	case 1 :
			    	$water_img = imagecreatefromgif($wmimage);
			    	break;
			    case 2 :
			    	$water_img = imagecreatefromjpeg($wmimage);
			    	break;
			    case 3 :
			    	$water_img = imagecreatefrompng($wmimage);
			    	imagesavealpha($water_img, true);
			    	break;
			    default :
					return;
			}
			
			//是否非法水印
			$w_pos		= MulanDBUtil::getConfigByKey('positionwm');
			$offxwm		= (int)MulanDBUtil::getConfigByKey('offxwm');
			$offywm		= (int)MulanDBUtil::getConfigByKey('offywm');
			$opacitywm	= (int)MulanDBUtil::getConfigByKey('opacitywm');
			
			switch ($w_pos) {
				case 0: //随机位置
					$wx = rand(0,($width - $wm_width));
					$wy = rand(0,($height - $wm_height));
					break;
				case 1: //左上角
					$wx = $offxwm;
					$wy = $offywm;
					break;
				case 2: //上面中间位置
					$wx = ($width - $wm_width) / 2+$offxwm;
					$wy = $offywm;
					break;
				case 3: //右上角
					$wx = $width - $wm_width-$offxwm;
					$wy = $offywm;
					break;
				case 4: //左面中间位置
					$wx = $offxwm;
					$wy = ($height - $wm_height) / 2+$offywm;
					break;
				case 5: //中间位置
					$wx = ($width - $wm_width) / 2+$offxwm;
					$wy = ($height - $wm_height) / 2+$offywm;
					break;
				case 6: //底部中间位置
					$wx = ($width - $wm_width) / 2+$offxwm;
					$wy = $height - $wm_height-$offywm;
					break;
				case 7: //左下角
					$wx = $offxwm;
					$wy = $height - $wm_height-$offywm;
					break;
				case 8: //右面中间位置
					$wx = $width - $wm_width-$offxwm;
					$wy = ($height - $wm_height) /2+$offywm;
					break;
				case 9: //右下角
					$wx = $width - $wm_width-$offxwm;
					$wy = $height - $wm_height-$offywm ;
					break;
				default: //随机
					$wx = rand(0,($width - $wm_width));
					$wy = rand(0,($height - $wm_height));
					break;
			}
			$this->imagecopymerge_alpha($source_img ,$water_img,$wx,$wy,0,0,$wm_width,$wm_height,$opacitywm);
			//输出到文件或者浏览器
			switch($source_info[2]) {
				case 1 :
					imagegif($source_img, $source);		//以 GIF 格式将图像输出到浏览器或文件
					break;
				case 2 :
					imagejpeg($source_img, $source);	//以 JPEG 格式将图像输出到浏览器或文件
					break;
				case 3 :
					imagepng($source_img, $source);		//以 PNG 格式将图像输出到浏览器或文件
					break;
				default :
					return;
			}
		}
	}
	
	/**
	 * 2011-10-xx wengebin 新增！
	 * 
	 * 该方可以将水印图片和被添加水印的图片进行合并，因为两张png图片合并会出现黑色阴影，所以在水印添加逻辑中不会对png图片添加水印！
	 */
	function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
		$opacity = $pct;
		// creating a cut resource
		$cut = imagecreatetruecolor($src_w, $src_h);
		// copying that section of the background to the cut
		imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
		
		// placing the watermark now
		imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
		imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity);
	}
	
	/**
	 * 2011-10-xx wengebin 新增！
	 * 
	 * 删除文件方法！
	 * 
	 * Delete file!
	 */
	function deletefile(){
		$filename = JRequest::getString('filename');
		$tofolder = JRequest::getString('tofolder');
		$lockfolder = JRequest::getString('lockfolder');
		$fieldid  = JRequest::getString('fieldid');
		
		if (IS_WIN) {
			$filename = iconv("UTF-8", "GBK", $filename);
		}
		
		$type = JRequest::getString('type');  
		if ($type) {
			$type_str = '&type='.$type;
		}
		$eshow = JRequest::getString('e_show');
		if ($eshow) {
			$e_show_str = '&e_show='.$eshow;
		}
		
		if (!$tofolder) {
			preg_match('/^images[\/\\\\]{1}(.+)[\/\\\\]{1}.+\.\w+$/',$filename,$match);
			$tofolder = $match[1];
		}
		$user = JFactory::getUser();
		if(!preg_match('/administrator$/',JPATH_BASE)){	//前台管理的图片目录
			if(!preg_match('/^images\/stories\/users\/'.$user->username.'\//',$filename)){
				JError::raiseWarning('','没有权限！');
				return false;
			}
		}
		$return_result = false;
		$fullpath = JPATH_SITE.DS.preg_replace('/[\/\\\\]/',DS,$filename);
		if($filename&&file_exists($fullpath)&&(!is_dir($fullpath))&&unlink($fullpath)){
			$return_result = true;
		} else {
			$return_result = false;
		}
		$this->setRedirect('index.php?option=com_media&view=images&tmpl=component&e_name='.JRequest::getVar('e_name').($eshow ? $e_show_str : '').($type ? $type_str : '').'&asset='.JRequest::getVar('asset').'&author='.JRequest::getVar('author').($tofolder ? '&tofolder='.$tofolder : '').($lockfolder ? '&lockfolder='.$lockfolder : '').'&fieldid='.$fieldid, $return_result ? '删除成功' : '删除失败，请稍后再试', 'message');
		return $return_result;
	}
}
