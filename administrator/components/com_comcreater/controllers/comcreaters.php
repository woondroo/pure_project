<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Comcreaters Controller
 */
class ComcreaterControllerComcreaters extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Comcreater', $prefix = 'ComcreaterModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function delete()
	{
		$post = JRequest::get('post');
		$cids = $post['cid'];
		if (count($cids)) {
			jimport('mulan.mldb');
			foreach ($cids as $cid) {
				$com = MulanDBUtil::getObjectBySql('select id,title from #__comcreater where id='.MulanDBUtil::dbQuote($cid));
				if ($com->id) {
					MulanDBUtil::executeSql('delete from #__comcreater where id='.MulanDBUtil::dbQuote($cid));
					MulanDBUtil::executeSql('delete from #__extensions where type='.MulanDBUtil::dbQuote('component').' and element='.MulanDBUtil::dbQuote('com_'.$com->title));
					MulanDBUtil::executeSql('delete from #__categories where extension='.MulanDBUtil::dbQuote('com_'.$com->title));
					MulanDBUtil::executeSql('drop table if exists #__'.$com->title);
					$this->deleteDirAndFile(JPATH_ROOT.'/administrator/components/com_'.$com->title);
					$this->deleteDirAndFile(JPATH_ROOT.'/components/com_'.$com->title);
					$this->deleteFile(JPATH_ROOT.'/language/zh-CN/zh-CN.com_'.$com->title.'.ini');
					$this->deleteFile(JPATH_ROOT.'/administrator/language/zh-CN/zh-CN.com_'.$com->title.'.ini');
					$this->deleteFile(JPATH_ROOT.'/administrator/language/zh-CN/zh-CN.com_'.$com->title.'.sys.ini');
					
				}
			}
			$this->setRedirect('index.php?option=com_comcreater&view=comcreaters','删除组件成功','message');
		} else {
			$this->setRedirect('index.php?option=com_comcreater&view=comcreaters','请选择需要删除的组件','error');
		}
	}
	
	function deleteDirAndFile($dirName)
	{
		if ($handle = opendir("$dirName")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dirName/$item")) {
						$this->deleteDirAndFile("$dirName/$item");
					} else {
						unlink("$dirName/$item");
					}
				}
			}
			closedir($handle);
			rmdir($dirName);
		}
	}
	
	function deleteFile($file) {
		if (!is_dir($file) && file_exists($file)) {
			unlink($file);
		}
	}
}
