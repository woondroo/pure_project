<?php
/**
 * @version		$Id: default.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @package		Joomla.Administrator
 * @functionpackage	mod_functionmenu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$sublist = null;
?>
<ul id="functionmenu">
	<?php foreach ($list as $item) { 
			$db = JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('*');
			$query->from('#__aclmanager');
			$query->where('catid='.$item->id);
			$db->setQuery((string)$query);
			$sublist = $db->loadObjectList();
	?>
	<li class="toplist">
		<a href="javascript:;"><?php echo $item->title?></a>
		<?php if($sublist){?>
		<ul >
			<?php foreach($sublist as $subitem){?>
				<li class="sublist"><a title="<?php echo $subitem->title;?>" href="<?php echo $subitem->componenturl;?>"><?php echo $subitem->title;?></a></li>
			<?php } ?>
		</ul>
		<?php }?>
	</li>
	<?php } ?>
</ul>