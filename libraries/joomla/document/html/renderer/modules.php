<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JDocument Modules renderer
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class JDocumentRendererModules extends JDocumentRenderer
{
	/**
	 * Renders multiple modules script and returns the results as a string
	 *
	 * @param   string  $name    The position of the modules to render
	 * @param   array   $params  Associative array of values
	 * 
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($position, $params = array(), $content = null)
	{
		/**
		 * 2012-05-06 wengebin edit
		 * 
		 * 调用 libraries/joomla/document/html/html.php 下的 getBuffer() 方法，
		 * 该方法继承自 libraries/joomla/document/document.php
		 * 
		 * 从 html.php 加载渲染的方法中加载到
		 * libraries/joomla/document/html/renderer/module.php
		 * 加载后可以用 module.php 循环渲染 JModuleHelper::getModules() 方法获取到的渲染模块。
		 * 
		 */
		$renderer	= $this->_doc->loadRenderer('module');
		$buffer		= '';

		foreach (JModuleHelper::getModules($position) as $mod) {
			$buffer .= $renderer->render($mod, $params, $content);
		}
		return $buffer;
	}
	
	/**
	 * 2012-05-07 wengebin add
	 * 
	 * 克隆对象的时候不允许克隆 _doc->_style
	 */
	function __clone() {
		$this->_doc = clone $this->_doc;
		$this->_doc->_style['text/css'] = '';
	}
}