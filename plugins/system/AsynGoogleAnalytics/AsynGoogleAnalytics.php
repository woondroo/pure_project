<?php
/**
* @version		$version 1.0  $
* @copyright	Copyright (C) 2010 PB Web Development. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* Twitter: @astroboysoup
* Blog: http://www.pbwebdev.com.au/blog/
* Email: peter@pbwebdev.com.au
*
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');

class plgSystemAsynGoogleAnalytics extends JPlugin
{
	function plgAsynGoogleAnalytics(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin( 'system', 'AsynGoogleAnalytics' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	function onAfterRender()
	{
		$app = JFactory::getApplication();
		
		// skip if admin page 
		if($app->isAdmin())
		{
			return;
		}

		// get params
		$trackerCode = $this->params->get( 'code', '' );
		
		//getting body code and storing as buffer
		$buffer = JResponse::getBody();
		
		//embed Google Analytics code
		$javascript = "<script type=\"text/javascript\">
					  var _gaq = _gaq || [];
					  
					  _gaq.push(['_setAccount', '".$trackerCode."']);
						_gaq.push(['_trackPageview']);
					
					  (function() {
						var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
						ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
					  })();
					</script>";

		// adding the Google Analytics code in the header before the ending </head> tag and then replacing the buffer
		$buffer = preg_replace ("/<\/head>/", "\n\n".$javascript."\n\n</head>", $buffer); 
		
		//output the buffer
		JResponse::setBody($buffer);
		
		return true;
	}
}
?>
