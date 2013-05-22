<?php
include('../../../includes.php');
jimport('joomla.filesystem.file');

if(!jckimport('ckeditor.authenticate'))
	die(false);



require('nodes.php');

//Get Node list for tree control

$extension = JRequest::getCmd('extension','content'); 

if($extension == 'content')
{
	$extFile =  'contentnodes.php';
}
elseif($extension == 'menu')
{
	$extFile =  'menunodes.php';
}
else
{
	$root = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'plugins';
	$extFile = $root.DS.$extension.DS.'links'.DS.$extension.'nodes.php';
}

if(!JFile::exists($extFile))
	die('JLink extsnsion '.$extension.' file could not be found!');
	
require_once($extFile);	
	
$classname = $extension.'LinkNodes';

$linkNodeList = new $classname();
$nodeList = $linkNodeList->getItems(); 


//now lets echo responese
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n";
echo "<nodes>\n";

foreach ($nodeList as $node) 
{
	$load = $linkNodeList->getLoadLink($node);
	echo '<node text="' .str_replace( array('"','<','>',' & '), array('&quot;','&lt;','&gt;',' and '), $node->name ).'"'. 
	($node->expandible ? ' openicon="_open" icon="_closed" load="'. $load . '"' : ($node->doc_icon  ? ' icon="_doc"'  :' icon="_closed"' )). 
	'  selectable="' . ($node->selectable?'true':'false') .'" url ="'.  $linkNodeList->getUrl($node) .'">' . "\n";
	echo "</node>\n";
	
	
}

echo "</nodes>";

?>