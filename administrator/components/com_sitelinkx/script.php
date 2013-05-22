<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Sitelinkx
 * @copyright Copyright (C) www.extro-media.de
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class com_SitelinkxInstallerScript {



function install($parent) {

jimport( 'joomla.installer.installer' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

$db =& JFactory::getDBO();
$query = "SELECT * FROM `#__sitelinkx_config`";
$db->setQuery($query);
$rows = $db->loadObjectList();
$ihrev = $rows[0]->version;

//--- restore
$dba =& JFactory::getDBO();
$querya = "SELECT * FROM `bak_sitelinkx`";
$dba->setQuery($querya);
$rowsa = $dba->LoadObjectList();
$anzahl = count($rowsa);

for ($x=0, $anzahl; $x <= $anzahl; $x++) {

$dba =& JFactory::getDBO();
$querya = "INSERT INTO `#__sitelinkx` 
  SET 
  `#__sitelinkx`.`id` = (SELECT `bak_sitelinkx`.`id` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`wort` = (SELECT `bak_sitelinkx`.`wort` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`ersatz` = (SELECT `bak_sitelinkx`.`ersatz` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`schlagwort` = (SELECT `bak_sitelinkx`.`schlagwort` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`fenster` = (SELECT `bak_sitelinkx`.`fenster` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`published` = (SELECT `bak_sitelinkx`.`published` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`begpub` = (SELECT `bak_sitelinkx`.`begpub` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`endpub` = (SELECT `bak_sitelinkx`.`endpub` FROM `bak_sitelinkx` LIMIT ".$x.",1);";

@$dba->setQuery($querya);
@$rowsa = $dba->LoadObjectList();
}
//--- restore ende

//--- plugin kopieren und aktivieren
$dbb =& JFactory::getDBO();
$queryb = "SELECT * FROM `#__extensions` WHERE `element` = 'sitelinkx'";
$dbb->setQuery($queryb);
$rowsb = $dbb->LoadObjectList();
$installiert = count($rowsb);

if ($installiert == 0) { $queryc = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ('', 'Content - Sitelinkx', 'plugin', 'sitelinkx', 'content', 0, 1, 1, 0, '{\"legacy\":true,\"name\":\"Content - Sitelinkx\",\"type\":\"plugin\",\"creationDate\":\"02\\/2011\",\"author\":\"www.eXtro-media.de\",\"copyright\":\"(c) 2009-2011 all rights reserved\",\"authorEmail\":\"sitelinkx@eXtro-media.de\",\"authorUrl\":\"http:\\/\\/www.eXtro-media.de\",\"version\":\"1.52\",\"description\":\"Sitelinkx plugin V1.52\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0)"; }
if ($installiert > 0)  { $queryc = "UPDATE `#__extensions` SET `enabled` = '1' WHERE `element` = 'sitelinkx'"; }

$quelle = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sitelinkx'.DS.'plugin'.DS;
$ziel = JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'sitelinkx'.DS;
$kopieren = JFolder::create($ziel);
$kopieren = $kopieren && JFile::copy($quelle.'sitelinkx.php', $ziel.'sitelinkx.php');
$kopieren = $kopieren && JFile::copy($quelle.'sitelinkx.xml', $ziel.'sitelinkx.xml');
$dbb->setQuery($queryc);
$kopieren = $kopieren && $dbb->query();
//--- plugin kopieren und aktivieren ende

?>

<p><h4>About</h4><br />
  <strong>Sitelinkx is a simple way to manage your Contentweblinks.</strong><br />
  The component and plugin run with Joomla 1.6/1.7!</p>  
<p>Sitelinkx was tested for compatibility  with Virtuemart, Joomfish and Community Builder  Enhanced.<br />
  If you find any incompatibilities, please let us know and write a message.<br />
  <br />
  We would be glad to hear from you how you liked Sitelinkx, <br />
  so send us an eMail to <a href="mailto:sitelinkx@eXtro-media.de">sitelinkx@extro-media.de</a> , any feedback is welcome.<br />
  <br />
  We would like to thank everybody who sent us feedback on Sitelinkx.<br />
  Special thanks go to Marc Mittag, thank you very much for the word boundaries improvement.
  <br />
  <br />  
  <strong>Version: </strong> <?php echo $ihrev; ?><br />
  <br />
  <strong>Copyright:</strong> © 2009 - 2011 <a href="http://www.extro-media.de">extro-media.de</a><br />
  <br />
  <strong>Homepage:</strong> <a href="http://www.extro-media.de">http://www.extro-media.de</a><br />
  <br />
  <strong>
  Sitelinkx is licensed under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a></strong><br />
</p>
  <br />
  <br />
		
<?php	
}

function update($parent) {

jimport( 'joomla.installer.installer' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

$db =& JFactory::getDBO();
$query = "SELECT * FROM `#__sitelinkx_config`";
$db->setQuery($query);
$rows = $db->loadObjectList();
$ihrev = $rows[0]->version;

//--- restore
$dba =& JFactory::getDBO();
$querya = "SELECT * FROM `bak_sitelinkx`";
$dba->setQuery($querya);
$rowsa = $dba->LoadObjectList();
$anzahl = count($rowsa);

for ($x=0, $anzahl; $x <= $anzahl; $x++) {

$dba =& JFactory::getDBO();
$querya = "INSERT INTO `#__sitelinkx` 
  SET 
  `#__sitelinkx`.`id` = (SELECT `bak_sitelinkx`.`id` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`wort` = (SELECT `bak_sitelinkx`.`wort` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`ersatz` = (SELECT `bak_sitelinkx`.`ersatz` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`schlagwort` = (SELECT `bak_sitelinkx`.`schlagwort` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`fenster` = (SELECT `bak_sitelinkx`.`fenster` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`published` = (SELECT `bak_sitelinkx`.`published` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`begpub` = (SELECT `bak_sitelinkx`.`begpub` FROM `bak_sitelinkx` LIMIT ".$x.",1),
  `#__sitelinkx`.`endpub` = (SELECT `bak_sitelinkx`.`endpub` FROM `bak_sitelinkx` LIMIT ".$x.",1);";

@$dba->setQuery($querya);
@$rowsa = $dba->LoadObjectList();
}
//--- restore ende

//--- plugin kopieren und aktivieren
$dbb =& JFactory::getDBO();
$queryb = "SELECT * FROM `#__extensions` WHERE `element` = 'sitelinkx'";
$dbb->setQuery($queryb);
$rowsb = $dbb->LoadObjectList();
$installiert = count($rowsb);

if ($installiert == 0) { $queryc = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ('', 'Content - Sitelinkx', 'plugin', 'sitelinkx', 'content', 0, 1, 1, 0, '{\"legacy\":true,\"name\":\"Content - Sitelinkx\",\"type\":\"plugin\",\"creationDate\":\"02\\/2011\",\"author\":\"www.eXtro-media.de\",\"copyright\":\"(c) 2009-2011 all rights reserved\",\"authorEmail\":\"sitelinkx@eXtro-media.de\",\"authorUrl\":\"http:\\/\\/www.eXtro-media.de\",\"version\":\"1.52\",\"description\":\"Sitelinkx plugin V1.52\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0)"; }
if ($installiert > 0)  { $queryc = "UPDATE `#__extensions` SET `enabled` = '1' WHERE `element` = 'sitelinkx'"; }

$quelle = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sitelinkx'.DS.'plugin'.DS;
$ziel = JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'sitelinkx'.DS;
$kopieren = JFolder::create($ziel);
$kopieren = $kopieren && JFile::copy($quelle.'sitelinkx.php', $ziel.'sitelinkx.php');
$kopieren = $kopieren && JFile::copy($quelle.'sitelinkx.xml', $ziel.'sitelinkx.xml');
$dbb->setQuery($queryc);
$kopieren = $kopieren && $dbb->query();
//--- plugin kopieren und aktivieren ende

?>

<p><h4>About</h4><br />
  <strong>Sitelinkx is a simple way to manage your Contentweblinks.</strong><br />
  The component and plugin run with Joomla 1.6/1.7!</p>  
<p>Sitelinkx was tested for compatibility  with Virtuemart, Joomfish and Community Builder  Enhanced.<br />
  If you find any incompatibilities, please let us know and write a message.<br />
  <br />
  We would be glad to hear from you how you liked Sitelinkx, <br />
  so send us an eMail to <a href="mailto:sitelinkx@eXtro-media.de">sitelinkx@extro-media.de</a> , any feedback is welcome.<br />
  <br />
  We would like to thank everybody who sent us feedback on Sitelinkx.<br />
  Special thanks go to Marc Mittag, thank you very much for the word boundaries improvement.
  <br />
  <br />  
  <strong>Version: </strong> <?php echo $ihrev; ?><br />
  <br />
  <strong>Copyright:</strong> © 2009 - 2011 <a href="http://www.extro-media.de">extro-media.de</a><br />
  <br />
  <strong>Homepage:</strong> <a href="http://www.extro-media.de">http://www.extro-media.de</a><br />
  <br />
  <strong>
  Sitelinkx is licensed under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a></strong><br />
</p>
  <br />
  <br />
		
<?php	
}







function uninstall($parent) {

jimport( 'joomla.installer.installer' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

$dbb =& JFactory::getDBO();
$queryb = "SELECT * FROM `#__extensions` WHERE `element` = 'sitelinkx'";
$dbb->setQuery($queryb);
$rowsb = $dbb->LoadObjectList();
$installiert = count($rowsb);

$queryc = "DELETE FROM `#__extensions` WHERE `element` = 'sitelinkx'";

$ziel = JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'sitelinkx'.DS;
$loeschen = JFile::delete($ziel.'sitelinkx.php');
$loeschen = $loeschen && JFile::delete($ziel.'sitelinkx.xml');
$dbb->setQuery($queryc);
$loeschen = $loeschen && $dbb->query();




?>

Thank you for testing Sitelinkx
		
<?php	
}


}
?>