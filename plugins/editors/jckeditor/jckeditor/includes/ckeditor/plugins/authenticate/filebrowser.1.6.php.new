<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');


class plgAuthenticateFilebrowser extends JPlugin 
{
		
  	function plgAuthenticateFilebrowser(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function authorise()
	{		
				
		//set component option in session
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		
		$option = $session->get('jckoption');
		
		if($user->authorise('core.create', $option)) 
		 return true;
				
		if($user->authorise('core.create', 'com_content')) 
		 return true;
	
		return false;
	}

}