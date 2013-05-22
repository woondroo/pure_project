<?php
/**
 * @version		$Id: controller.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Hr Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @since 1.5
 */
class HrController extends JController
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

		// Initialise variables.
		$cachable	= true;	// Huh? Why not just put that in the constructor?
		$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using w_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$vName	= JRequest::getCmd('view', 'hrs');
		JRequest::setVar('view', $vName);

		if ($user->get('id') ||($_HR['REQUEST_METHOD'] == 'POST' && $vName = 'hrs')) {
			$cachable = false;
		}

		$safeurlparams = array(
			'id'				=> 'INT',
			'limit'				=> 'INT',
			'limitstart'		=> 'INT',
			'filter_order'		=> 'CMD',
			'filter_order_Dir'	=> 'CMD',
			'lang'				=> 'CMD'
		);
		return parent::display($cachable,$safeurlparams);
	}
	
	function savehr(){
		require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mldb.php' );
		require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mlhtml.php' );
		$data['job'] = JRequest::getVar('job');
		$data['name'] = JRequest::getVar('name');
		$data['sex'] = JRequest::getVar('sex');
		$data['tel'] = JRequest::getVar('tel');
		$data['email'] = JRequest::getVar('email');
		$data['published'] = 1;
		date_default_timezone_set ('Asia/Shanghai');
		$data['time'] = date('Y-m-d H:i:s',time());
		$elink = JRequest::getVar('url');
		if(!$elink){
			$elink = 'index.php';
		}
		
		$file = $_FILES['fujian'];
		preg_match('/.*(\.\w+)$/',$file['name'],$match);
		$file['name'] = preg_replace('/(\.\w+)$/','-'.strtotime('now').$match[1],$file['name']);
		$path = 'images/hr';
		$file_url = $path.'/'.$file['name'];
		
		$ur = MulanHtmlUtil::uploadFileUtil($file, preg_replace('/\\\\/','/',JPATH_SITE).'/'.$file_url,5*1024*1024,array('pdf','doc','docx'));
		
		$app = JFactory::getApplication();
		$pagelanguage = $app->getCfg('pagelanguage');
		
		if ($pagelanguage == 1) {
			$language = array('email_check_fail'=>'请填写正确的电子邮件！'
						,'require_check_fail'=>'对不起，提交信息失败，请检查您所填写的信息是否正确！'
						,'submit_success'=>'感谢您的投递，我们会尽快给您答复！'
						,'format_fail'=>'格式不正确！'
						,'file_too_big'=>'文件太大！'
						,'file_upload_fail'=>'文件上传失败！');
		} else if ($pagelanguage == 2) {
			$language = array('email_check_fail'=>'Please fill in the correct E-mail!'
						,'require_check_fail'=>'Sorry!Commit message failed,please check the information that you fill in!'
						,'submit_success'=>'Thanks for your appling, we will reply to you as soon as possible!'
						,'format_fail'=>'Incorrect format!'
						,'file_too_big'=>'File is too large!'
						,'file_upload_fail'=>'File upload failed!');
		} else if ($pagelanguage == 3) {
			$language = array('email_check_fail'=>'请填写正确的电子邮件！'
						,'require_check_fail'=>'对不起，提交信息失败，请检查您所填写的信息是否正确！'
						,'submit_success'=>'感谢您的投递，我们会尽快给您答复！'
						,'format_fail'=>'格式不正确！'
						,'file_too_big'=>'文件太大！'
						,'file_upload_fail'=>'文件上传失败！');
		}
		
		if ($ur=='1'){			//上传成功
			$data['fujian'] = $file_url;
		} else if($ur=='2'){	//格式不正确
			$this->setRedirect($elink,$language['format_fail']);
		} else if ($ur=='3'){	//文件过大
			$this->setRedirect($elink,$language['file_too_big']);
		} else if ($ur=='4'){	//上传失败
			$this->setRedirect($elink,$language['file_upload_fail']);
		} else if ($ur=='5'){	//没有上传信息
			$this->setRedirect($elink,$language['file_upload_fail']);
		} else {				//资料不完整
			$this->setRedirect($elink,$language['require_check_fail']);
		}
		
		if(MulanDBUtil::autoExecute('#__yingpin',$data) && $ur==1){
			$mailer =& JFactory::getMailer();
			
			$hremail=MulanDBUtil::getObjectlistBySql('select email_to from #__contact_details where published=1 and catid=105');
			
			$hr_email='';
			foreach($hremail as $key=>$o){
				if($key==0){
					$hr_email=$o->email_to;
				}else{
					$hr_email.=','.$o->email_to;
				}
			}
			$email=explode(',',$hr_email);
			// Build e-mail message format $mainframe->getCfg( 'mailfrom' )
			$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
			$mailer->setSubject($data['name'].' 应聘　'.$data['job']);
			set_time_limit(0);
			$mailer->IsHTML(true);
			
			$sex = '未填写';
			if ($data['sex'] == '1') {
				$sex = '先生';
			} else if ($data['sex'] == '0') {
				$sex = '小姐';
			} else {
				$sex = '様';
			}
			
			$body  ='<div><b>职位：</b>'.$data['job'].'</div>';
			$body .='<div><b>姓名：</b>'.$data['name'].'</div>';
			$body .='<div><b>性别：</b>'.$sex.'</div>';
			$body .='<div><b>电话：</b>'.$data['tel'].'</div>';
			$body .='<div><b>邮箱：</b>'.$data['email'].'</div>';
			if($data['fujian']){
				$body .='<div><b>附件：</b><a href="'.JURI::base().$data['fujian'].'" target="_blank">下载/查看</a></div>';
			}
			$mailer->setBody($body);
	 		$mailer->addRecipient($email);
	 		$rs	= $mailer->Send();
	 		$this->setRedirect($elink,$language['submit_success']);
		}
	}
}
