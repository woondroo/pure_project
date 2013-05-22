<?php

/*------------------------------------------------------------------------
# Copyright (C) 2005-2010 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 


class JCKAuthenticate
{

	static function check()
    {
		  
		$mainframe = JFactory::getApplication();
	
		jckimport('ckeditor.plugins.helper');
		
		//import core plugins first
		JCKPluginsHelper::storePlugins('authenticate');
		JCKPluginsHelper::importPlugin('authenticate');
		return $mainframe->triggerEvent('authorise');

	}
		
}