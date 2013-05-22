<?php
/**
 * @version		$Id: list.php 21566 2011-06-19 12:56:14Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('mulan.mlstring');

/**
 * Media Component List Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.5
 */
class MediaModelList extends JModel
{
	function getState($property = null, $default = null)
	{
		static $set;

		if (!$set) {
			$folder = JRequest::getVar('folder', '', '', 'path');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	function getImages()
	{
		$list = $this->getList();
		//var_dump($list);
		/**
		 * 2011-12-9 wengebin 创建！
		 * 要支持中文，作为 windows 服务器，必须将 GBK/GB2312 格式的字符串转换成 UTF-8，
		 * 因为 windows 服务器上读取的文件名默认是 GBK/GB2312 格式，
		 * 而 linux 读取到的文件名默认是 UTF-8 格式，
		 * 而我们的程序是 UTF-8，所以得出：windows 需要转换，而 linux 不需要转换！
		 */
		if (count($list['images']) && IS_WIN) {
			foreach ($list['images'] as $key=>$file) {
				$list['images'][$key]->name = iconv("GBK", "UTF-8", $file->name);
				$list['images'][$key]->title = iconv("GBK", "UTF-8", $file->title);
				$list['images'][$key]->path = iconv("GBK", "UTF-8", $file->path);
				$list['images'][$key]->path_relative = iconv("GBK", "UTF-8", $file->path_relative);
			}
		}
		
		return $list['images'];
	}

	function getFolders()
	{
		$list = $this->getList();

		return $list['folders'];
	}

	function getDocuments()
	{
		$list = $this->getList();
		if (count($list['docs']) && IS_WIN) {
			foreach ($list['docs'] as $key=>$doc) {
				$list['docs'][$key]->name = iconv("GBK", "UTF-8", $doc->name);
				$list['docs'][$key]->title = iconv("GBK", "UTF-8", $doc->title);
				$list['docs'][$key]->path = iconv("GBK", "UTF-8", $doc->path);
				$list['docs'][$key]->path_relative = iconv("GBK", "UTF-8", $doc->path_relative);
			}
		}

		return $list['docs'];
	}

	/**
	 * Build imagelist
	 *
	 * @param string $listFolder The image directory to display
	 * @since 1.5
	 */
	function getList()
	{
		static $list;

		// Only process the list once per request
		if (is_array($list)) {
			return $list;
		}

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined') {
			$current = '';
		}

		// Initialise variables.
		if (strlen($current) > 0) {
			$basePath = COM_MEDIA_BASE.'/'.$current;
		}
		else {
			$basePath = COM_MEDIA_BASE;
		}

		$mediaBase = str_replace(DS, '/', COM_MEDIA_BASE.'/');

		$images		= array ();
		$folders	= array ();
		$docs		= array ();

		if (!is_dir($basePath)) {
			mkdir($basePath);
		}

		// Get the list of files and folders from the given folder
		$fileList	= JFolder::files($basePath);
		$folderList = JFolder::folders($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file)
			{
				if (is_file($basePath.'/'.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
					$tmp = new JObject();
					$tmp->name = $file;
					$tmp->title = $file;
					$tmp->path = str_replace(DS, '/', JPath::clean($basePath . '/' . $file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
					$tmp->size = filesize($tmp->path);

					$ext = strtolower(JFile::getExt($file));
					switch ($ext)
					{
						// Music
						case 'mp3':
						// Image
						case 'jpg':
						case 'png':
						case 'gif':
						case 'xcf':
						case 'odg':
						case 'bmp':
						case 'jpeg':
						case 'ico':
							$info = @getimagesize($tmp->path);
							$tmp->width		= @$info[0];
							$tmp->height	= @$info[1];
							$tmp->type		= @$info[2];
							$tmp->mime		= @$info['mime'];

							if (($info[0] > 60) || ($info[1] > 60)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 60);
								$tmp->width_60 = $dimensions[0];
								$tmp->height_60 = $dimensions[1];
							}
							else {
								$tmp->width_60 = $tmp->width;
								$tmp->height_60 = $tmp->height;
							}

							if (($info[0] > 16) || ($info[1] > 16)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 16);
								$tmp->width_16 = $dimensions[0];
								$tmp->height_16 = $dimensions[1];
							}
							else {
								$tmp->width_16 = $tmp->width;
								$tmp->height_16 = $tmp->height;
							}

							$images[] = $tmp;
							break;

						// Non-image document
						default:
							$tmp->icon_32 = "media/mime-icon-32/".$ext.".png";
							$tmp->icon_16 = "media/mime-icon-16/".$ext.".png";
							$docs[] = $tmp;
							break;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false) {
			foreach ($folderList as $folder)
			{
				if (basename($folder) == 'resized') {
					continue;
				}
				$tmp = new JObject();
				$tmp->name = basename($folder);
				$tmp->path = str_replace(DS, '/', JPath::clean($basePath . '/' . $folder));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
				$count = MediaHelper::countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'docs' => $docs, 'images' => $images);

		return $list;
	}
}
