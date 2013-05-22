<?php
/**
 * @version		$Id: integral.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Reset controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @version		1.6
 */
class UsersControllerIntegral extends UsersController
{
	/**
	 * Method to exchange the product.
	 *
	 * @since	1.6
	 */
	public function exchange() {
		jimport('mulan.mldb');
		$user = JFactory::getUser();
		if ($user->id) $user_mess = MulanDBUtil::getObjectBySql('select * from #__users where id=\''.$user->id.'\'');
		
		$pid = JRequest::getVar('pid',0,int);
		$pro = MulanDBUtil::getObjectBySql('select id,title,integral from #__integral where published=1 and id=\''.$pid.'\'');
		
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=integral', false));
		
		$integral_last = $user_mess->integral;
		if (!$user->id) {
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOLOGIN'));
			return false;
		}
		if (!$pid || !$pro->id) {
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOPRO'));
			return false;
		}
		if ($integral_last < $pro->integral) {
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOINTEGRAL'));
			return false;
		}
		
		$integral_last = $integral_last - $pro->integral;
		MulanDBUtil::executeSql('insert into #__integral_history(`pid`,`uid`,`reason`,`use`,`get`,`last`,`ordertime`,`receivetime`,`completetime`,`state`,`way`) values (\''.$pid.'\',\''.$user->id.'\',\'兑换产品：'.$pro->title.'。\','.MulanDBUtil::dbQuote($pro->integral).',\'0\','.$integral_last.',\''.date('Y-m-d H:i:s').'\',\'0000-00-00 00:00:00\',\'0000-00-00 00:00:00\',\'0\',\'1\')');
		MulanDBUtil::executeSql('update #__users set integral=\''.$integral_last.'\' where id=\''.$user_mess->id.'\'');
		
		$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_SUCCESS'));
	}
	
	public function cancel() {
		jimport('mulan.mldb');
		$oid = JRequest::getVar('oid',0,int);
		$user = JFactory::getUser();
		if ($user->id) $user_mess = MulanDBUtil::getObjectBySql('select * from #__users where id=\''.$user->id.'\'');
		
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=integral', false));
		$order = MulanDBUtil::getObjectBySql('select * from #__integral_history where id='.$oid);
		
		if (!$user->id) {
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOLOGIN'));
			return false;
		}
		if (!$oid || !$order->id) {
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOPID'));
			return false;
		}
		if ($order->state != 0) {
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOACCESS'));
			return false;
		}
		
		$use_integral = $order->use;
		$last_integral = $user_mess->integral + $use_integral;
		MulanDBUtil::executeSql('update #__integral_history set state=\'-1\',completetime=\''.date('Y-m-d H:i:s').'\' where id='.$order->id);
		MulanDBUtil::executeSql('update #__users set integral=\''.$last_integral.'\' where id='.$user->id);
	}
	
	public function received() {
		jimport('mulan.mldb');
		$oid = JRequest::getVar('oid',0,int);
		$user = JFactory::getUser();
		
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=integral', false));
		$order = MulanDBUtil::getObjectBySql('select * from #__integral_history where id='.$oid);
		
		if (!$user->id) {
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOLOGIN'));
			return false;
		}
		if (!$oid || !$order->id) {
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOPID'));
			return false;
		}
		if ($order->state != 1) {
			$this->setMessage(JText::_('COM_USERS_PROFILE_ORDER_PRODUCT_FAIL_NOACCESS'));
			return false;
		}
		
		MulanDBUtil::executeSql('update #__integral_history set state=\'2\',completetime=\''.date('Y-m-d H:i:s').'\' where id='.$order->id);
	}
}