<?php
/**
 * @version		$Id: controller.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('mulan.mldb');
jimport('mulan.mlimage');

/**
 * Media Manager Component Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @version 1.5
 */
class MediaController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JPluginHelper::importPlugin('content');
		$vName = JRequest::getCmd('view', 'media');
		switch ($vName)
		{
			case 'images':
				$vLayout = JRequest::getCmd('layout', 'default');
				$mName = 'manager';

				break;

			case 'imagesList':
				$mName = 'list';
				$vLayout = JRequest::getCmd('layout', 'default');

				break;

			case 'mediaList':
				$app	= JFactory::getApplication();
				$mName = 'list';
				$vLayout = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

				break;

			case 'media':
			default:
				$vName = 'media';
				$vLayout = JRequest::getCmd('layout', 'default');
				$mName = 'manager';
				break;
		}

		$document = JFactory::getDocument();
		$vType		= $document->getType();

		// Get/Create the view
		$view = $this->getView($vName, $vType);

		// Get/Create the model
		if ($model = $this->getModel($mName)) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}

	function ftpValidate()
	{
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
	}
	
	// 根据路径获取路径下的所有图片
	function viewFolderImgs() {
		$foldersrc = JRequest::getVar('fs');
		if ($foldersrc != null) {
			$get_imgs = MulanImageUtil::images($foldersrc);
			if (count($get_imgs)) {
				foreach ($get_imgs as $img) {
					$img_src = IS_WIN ? iconv("GBK", "UTF-8", $img->name) : $img->name;
					$html .= '<img src="'.JURI::base().MulanImageUtil::thumbimage($foldersrc.'/'.$img_src, 196, 100).'" style="float:left;border:2px solid #DFDFDF;padding:5px;height:70px;"/>';
				}
			}
			echo $html;exit;
		} else {
			echo '';exit;
		}
	}
	
	// 根据路径获取路径下的所有图片，并添加说明
	function viewFolderImgsAndDesc() {
		$user = JFactory::getUser();
		$vid = (int)JRequest::getVar('vid');
		$foldersrc = JRequest::getVar('fs');
		$table = JRequest::getVar('table');
		if ($foldersrc != null && $user->id && $vid) {
			$imgdescs = MulanDBUtil::getObjectBySql('select imgdesc from #__'.$table.' where id='.MulanDBUtil::dbQuote($vid));
			$imgdescs_array = explode('¦',$imgdescs->imgdesc);
			if (count($imgdescs_array)) {
				foreach ($imgdescs_array as $key=>$imgdesc) {
					$imgdescs_array[$key] = str_replace('&brvbar;','¦',$imgdesc);
				}
			}
			$get_imgs = MulanImageUtil::images($foldersrc);
			if (count($get_imgs)) {
				foreach ($get_imgs as $key=>$img) {
					$img_src = IS_WIN ? iconv("GBK", "UTF-8", $img->name) : $img->name;
					$html .= '<div id="one_pro_num'.$key.'" class="one_pro">' .
								'<span class="left no_1">' .
									($key+1) .
									'<a href="javascript:void(0);" onclick="deleteOneImg(\''.$foldersrc.'/'.$img_src.'\',\''.$key.'\')">删除</a>' .
								'</span>' .
								'<span class="right no_2">' .
									'<div class="right no_2_img" style="background:url('.JURI::base().'../'.MulanImageUtil::thumbimage($foldersrc.'/'.$img_src, 196, 100).') center center no-repeat;"></div>' .
									'<div class="right pro_desc"><div class="left"><br/>说明：</div><div class="right"><textarea id="pro_'.$key.'" name="imgdescs[]">'.($imgdescs_array[$key] ? $imgdescs_array[$key] : '').'</textarea></div></div>' .
								'</span>' .
								'<div class="clr"></div>'.
							'</div>';
				}
			}
			$html .= '<div class="clr"></div>';
			echo $html;exit;
		} else if ($foldersrc != null && $user->id) {
			$get_imgs = MulanImageUtil::images($foldersrc);
			if (count($get_imgs)) {
				foreach ($get_imgs as $key=>$img) {
					$img_src = IS_WIN ? iconv("GBK", "UTF-8", $img->name) : $img->name;
					$html .= '<div id="one_pro_num'.$key.'" class="one_pro no_desc">' .
								'<span class="left no_1">' .
									($key+1) .
									'<a href="javascript:void(0);" onclick="deleteOneImg(\''.$foldersrc.'/'.$img_src.'\',\''.$key.'\')">删除</a>' .
								'</span>' .
								'<span class="right no_2">' .
									'<div class="right no_2_img" style="background:url('.JURI::base().'../'.MulanImageUtil::thumbimage($foldersrc.'/'.$img_src, 196, 100).') center center no-repeat;"></div>' .
								'</span>' .
								'<div class="clr"></div>'.
							'</div>';
				}
			}
			$html .= '<div class="clr"></div>';
			echo $html;exit;
		} else {
			echo '';exit;
		}
	}
	
	// 删除文件夹
	function deleteDir() {
		$user = JFactory::getUser();
		$dir = JRequest::getVar('dir');
		$vid = (int)JRequest::getVar('vid');
		$table = JRequest::getVar('table');
		if ($dir && $user->id) {
			if ($vid) {
				MulanDBUtil::executeSql('update #__'.$table.' set imgdesc="" where id='.MulanDBUtil::dbQuote($vid));
			}
			$dir = JPATH_SITE.DS.$dir;
			echo strval($this->deleteDirAndFiles($dir));
		} else {
			echo 'false';
		}
		exit;
	}
	
	// 删除图片
	function deleteOneImg() {
		$user = JFactory::getUser();
		$img = JRequest::getVar('img');
		$num = (int)JRequest::getVar('num');
		$vid = (int)JRequest::getVar('vid');
		$table = JRequest::getVar('table');
		if ($img && $user->id && $vid) {
			$img = JPATH_SITE.DS.$img;
			$result = $this->deleteDirAndFiles($img) ? 'true' : 'false';
			if ($result == 'true') {
				$imgdescs = MulanDBUtil::getObjectBySql('select imgdesc from #__'.$table.' where id='.MulanDBUtil::dbQuote($vid));
				$imgdescs_array = explode('¦',$imgdescs->imgdesc);
				if ($num < count($imgdescs_array)) {
					array_splice($imgdescs_array,$num,1);
					$imgdesc = implode('¦',$imgdescs_array);
					if (!MulanDBUtil::executeSql('update #__'.$table.' set imgdesc='.MulanDBUtil::dbQuote($imgdesc).' where id='.MulanDBUtil::dbQuote($vid))) {
						$result = 'false';
					}
				}
			}
			echo $result;
		} else if ($img && $user->id) {
			$img = JPATH_SITE.DS.$img;
			$result = $this->deleteDirAndFiles($img) ? 'true' : 'false';
			echo $result;
		} else {
			echo 'false';
		}
		exit;
	}
	
	// 根据路径删除文件夹下的所有文件
	function deleteDirAndFiles($dir){
		$result = true;
		$dir = IS_WIN ? iconv("UTF-8", "GBK", $dir) : $dir;
		if (is_dir($dir)) {
			if ($dp = opendir($dir)) {
				while (($file=readdir($dp)) != false) {
					if (is_dir($file) && $file!='.' && $file!='..' && !preg_match('/svn/',$file)) {
						$this->deleteDirAndFiles($file);
					} else if ($file!='.' && $file!='..' && !preg_match('/svn/',$file)) {
						unlink($dir.DS.$file);
					}
				}
				closedir($dp);
				$result = true;
			} else {
				$result = false;
			}
		} else {
			unlink($dir);
			$result = true;
		}
		return $result;
	}
}
