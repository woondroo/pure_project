<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * Blank Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_blank
 * @since 1.5
 */
class BlankController extends JController
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
		$id		= JRequest::getInt('w_id');
		$vName	= JRequest::getCmd('view', 'blanks');
		JRequest::setVar('view', $vName);

		if ($user->get('id') ||($_SERVER['REQUEST_METHOD'] == 'POST' && $vName = 'blanks')) {
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
	
	public function leavemessage()
	{
		jimport('mulan.mldb');
		jimport('mulan.mlhtml');
		jimport('mulan.mlstring');
		
		$app = JFactory::getApplication();
		$pagelanguage = $app->getCfg('pagelanguage');
		$site_model = $app->getCfg('site_model');
		
		$language = array('email_check_fail'=>'请填写正确的电子邮件！'
					,'require_check_fail'=>'对不起，提交信息失败，请检查您所填写的信息是否正确！'
					,'submit_success'=>'谢谢！您的信息提交成功！');
		
		$labels = array('username'=>'姓名：'
				,'sex'=>'称谓：'
				,'no'=>'未填写'
				,'mr'=>'先生'
				,'miss'=>'小姐'
				,'ms'=>'女士'
				,'mrs'=>'夫人'
				,'phone'=>'电话：'
				,'fax'=>'传真：'
				,'qq'=>'MSN/QQ：'
				,'email'=>'邮箱：'
				,'company'=>'公司：'
				,'company_p'=>'预算：'
				,'title'=>'主题：'
				,'demand'=>'内容：'
				,'clientip'=>'IP：'
				,'submit'=>'提交'
				,'reset'=>'重填'
				,'titleVal'=>'来自留言中心'
				,'1000'=>'1000以下'
				,'2000'=>'1000-2000'
				,'5000'=>'2000-5000'
				,'5000+'=>'5000以上'
				,'from'=>'来源：');
		
		if ($pagelanguage == 2) {
			$language = array('email_check_fail'=>'Please fill in the correct E-mail!'
						,'require_check_fail'=>'Sorry!Commit message failed,please check the information that you fill in!'
						,'submit_success'=>'Thank you!Successful submission of your message!');
			
			$labels = array('username'=>'Your name:'
					,'sex'=>'Sex:'
					,'no'=>'No specified'
					,'mr'=>'Mr'
					,'miss'=>'Miss'
					,'ms'=>'Ms'
					,'mrs'=>'Mrs'
					,'phone'=>'Contact Phone:'
					,'fax'=>'Fax:'
					,'qq'=>'MSN/QQ:'
					,'email'=>'E-mail:'
					,'company'=>'Company Name:'
					,'company_p'=>'Budget:'
					,'title'=>'Title:'
					,'demand'=>'Content:'
					,'clientip'=>'IP:'
					,'submit'=>'Submit'
					,'reset'=>'Reset'
					,'titleVal'=>'From the message center'
					,'1000'=>'Less than 1000'
					,'2000'=>'1000-2000'
					,'5000'=>'2000-5000'
					,'5000+'=>'More than 5000'
					,'from'=>'Come from:');
		}
		
		$msg = null;
		$data['username'] = JRequest::getVar('username');
		$data['qq'] = JRequest::getVar('qq');
		$data['email'] = JRequest::getVar('email');
		$data['company'] = JRequest::getVar('company');
		$data['title'] = JRequest::getVar('title');
		$data['phone'] = JRequest::getVar('phone');
		$data['demand'] = JRequest::getVar('demand');
		
		$data['scope'] = JRequest::getVar('scope');
		$data['sex'] = JRequest::getVar('sex');
		$data['fax'] = JRequest::getVar('fax');
		$data['country'] = JRequest::getVar('country');
		
		$data['published'] = 0;
		$check_pass = true;
		if ($data['scope'] == 'inquiry' && (!$data['country'] || !$data['phone'])) {
			$check_pass = false;
		}
		if(!$data['username'] || !$data['email'] || !$data['title'] || !$data['demand']) {
			$check_pass = false;
		}
		
		preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/',$data['email'],$matches);
		if (is_array($matches) && count($matches) <= 0) {
			$msg .= $language['email_check_fail'];
			$check_pass = false;
		}
		
		if($check_pass == false) {
			if ($data['scope'] == 'inquiry') {
				$msg .= $language['require_check_fail'];
				$this->setRedirect(MulanHtmlUtil::getUrlByAlias('inquirybox'),$msg);
			} else {
				$msg .= $language['require_check_fail'];
				$this->setRedirect(MulanHtmlUtil::getUrlByAlias('feedback'),$msg);
			}
			return;
		}
		
		date_default_timezone_set('Asia/Shanghai');
		$data['created'] = date('Y-m-d H:i:s');
		$data['clientip'] = MulanStringUtil::getClientIP();
		if(MulanDBUtil::autoExecute('#__leavemessage',$data))
		{
			$hremail=MulanDBUtil::getObjectlistBySql('select email_to from #__contact_details where published=1 and catid=111');
			if (count($hremail)) {
				$mailer =& JFactory::getMailer();
				
				$ask_email='';
				foreach($hremail as $key=>$o){
					if($key==0){
						$ask_email=$o->email_to;
					}else{
						$ask_email.=','.$o->email_to;
					}
				}
				$email=explode(',',$ask_email);
				// Build e-mail message format $mainframe->getCfg( 'mailfrom' )
				$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
				$mailer->setSubject($data['username'].'(E-mail:'.$data['email'].')于'.$data['created'].'留言');
				set_time_limit(0);
				$mailer->IsHTML(true);
				
				$sex = $labels[$data['sex']];
				if ($sex == '') {
					$sex = $labels['no'];
				}
				$company_val = $data['company'];
				if ($site_model) {
					if ($data['company'] == '1000-') {
						$company_val = $labels['1000'];
					} else if ($data['company'] == '2000') {
						$company_val = $labels['2000'];
					} else if ($data['company'] == '5000') {
						$company_val = $labels['5000'];
					} else if ($data['company'] == '5000+') {
						$company_val = $labels['5000+'];
					}
				}
				$body .='<div><b>'.$labels['username'].'</b>'.$data['username'].'</div>';
				if ($data['sex']) $body .='<div><b>'.$labels['sex'].'</b>'.$sex.'</div>';
				if ($data['phone']) $body .='<div><b>'.$labels['phone'].'</b>'.$data['phone'].'</div>';
				if ($data['fax']) $body .='<div><b>'.$labels['fax'].'</b>'.$data['fax'].'</div>';
				if ($data['qq']) $body .='<div><b>'.$labels['qq'].'</b>'.$data['qq'].'</div>';
				if ($data['email']) $body .='<div><b>'.$labels['email'].'</b>'.$data['email'].'</div>';
				if ($company_val) $body .='<div><b>'.($site_model ? $labels['company_p'] : $labels['company']).'</b>'.$company_val.'</div>';
				if ($data['title']) $body .='<div><b>'.$labels['title'].'</b>'.$data['title'].'</div>';
				if ($data['demand']) $body .='<div><b>'.$labels['demand'].'</b>'.$data['demand'].'</div>';
				if ($data['clientip']) $body .='<div><b>'.$labels['clientip'].'</b>'.$data['clientip'].'</div>';
				$body .='<div><b>'.$labels['from'].'</b>'.JURI::root().'</div>';
				
				$mailer->setBody($body);
		 		$mailer->addRecipient($email);
		 		$rs	= $mailer->Send();
			}
			
			if($data['scope'] == 'inquiry') {
				$msg .= $language['submit_success'];
				$this->setRedirect(MulanHtmlUtil::getUrlByAlias('inquirybox'),$msg);
			} else {
				$msg .= $language['submit_success'];
				$this->setRedirect(MulanHtmlUtil::getUrlByAlias('feedback'),$msg);
			}
		}
		else
		{
			if($data['scope'] == 'inquiry') {
				$msg .= $language['require_check_fail'];
				$this->setRedirect(MulanHtmlUtil::getUrlByAlias('inquirybox'),$msg);
			} else {
				$msg .= $language['require_check_fail'];
				$this->setRedirect(MulanHtmlUtil::getUrlByAlias('feedback'),$msg);
			}
		}
	}
}
