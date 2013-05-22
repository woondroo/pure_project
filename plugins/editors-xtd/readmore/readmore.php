<?php
/**
 * @version		$Id: readmore.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Editor Readmore buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.readmore
 * @since 1.5
 */
class plgButtonReadmore extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * readmore button
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		/**
		 * 2012-04-12 wengebin 修改！
		 * 修改“阅读更多”插入编辑器代码，为了兼容多个编辑器，必须给不同编辑器的调用函数重新命名！
		 */
		$app = JFactory::getApplication();

		$doc		= JFactory::getDocument();
		$template	= $app->getTemplate();

		// button is not active in specific content components

		$getContent = $this->_subject->getContent($name);
		$present = JText::_('PLG_READMORE_ALREADY_EXISTS', true) ;
		$alias_name = str_replace(array('jform','[params]','[',']'),'',$name);
		$js = "
			function insertReadmore_".$alias_name."(editor) {
				var content = $getContent
				if (content.match(/<hr\s+id=(\"|')system-readmore(\"|')\s*\/*>/i)) {
					alert('$present');
					return false;
				} else {
					jInsertEditorText_".$alias_name."('<hr id=\"system-readmore\" />', editor);
				}
			}
			";

		$doc->addScriptDeclaration($js);

		$button = new JObject;
		$button->set('modal', false);
		$button->set('onclick', 'insertReadmore_'.$alias_name.'(\''.$name.'\');return false;');
		$button->set('text', JText::_('PLG_READMORE_BUTTON_READMORE'));
		$button->set('name', 'readmore');
		// TODO: The button writer needs to take into account the javascript directive
		//$button->set('link', 'javascript:void(0)');
		$button->set('link', '#');

		return $button;
	}
}
