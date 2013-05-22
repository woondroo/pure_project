<?php 

class JCKSession
{
	static function & getSessionInstance()
	{
		static $instance;
		
		if($instance)
		{
			return $instance;
		}	
		$client = 'administrator';
		
		//$client = JRequest::getInt('client',1); conflict when using Joomla JRequest
		
		//Lets client directly from $_GET variable
		$client = ( array_key_exists( 'client', $_GET ) ? $_GET['client'] : NULL ); //This should always be set
		$client = ( isset($client) && $client == 1 ? 'administrator' : 'site' );
		
		$mainframe = JFactory::getApplication($client);		
		jimport('joomla.plugin.helper');
		$plugins = JPluginHelper::getPlugin('system');
		for($i = 0; $i <  count($plugins); $i++)
		{
			$plugins[$i]->type = 'none';
		}
		$mainframe->initialise();
		
		for($i = 0; $i <  count($plugins); $i++)
		{
			$plugins[$i]->type = 'system';
		}
		$instance =  JFactory::getSession();		
		
		return 	$instance;
	}
	

}