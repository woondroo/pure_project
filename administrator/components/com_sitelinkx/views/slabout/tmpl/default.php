<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('behavior.tooltip'); ?>

<h3><?php echo JText::_('SL_HELP');?></h3>
<?php
$db =& JFactory::getDBO();
$query = "SELECT * FROM `#__sitelinkx_config`";
$db->setQuery($query);
$rows = $db->loadObjectList();
$ihrev = $rows[0]->version;
$derzv = $ihrev;
$testen = 'http://www.extro-media.de/images/slcv.txt';
?>
<p><h4>About</h4><br />
  <strong>Sitelinkx is a simple way to manage your Contentweblinks.</strong><br />
  The component and plugin run with Joomla 1.6!</p>
<p>Sitelinkx was tested for compatibility  with Virtuemart, Joomfish and Community Builder Enhanced.<br />
  If you find any incompatibilities, please let us know and write a message.<br />
  <br />
  We would be glad to hear from you how you liked Sitelinkx, <br />
  so send us an eMail to <a href="mailto:sitelinkx@eXtro-media.de">sitelinkx@extro-media.de</a> , any feedback is welcome.<br />
  <br />
  We would like to thank everybody who sent us feedback on Sitelinkx.<br />
  Special thanks go to Marc Mittag, thank you very much for the word boundaries improvement.
  <br />
  <br />  
  <strong>If you find Sitelinkx useful, please consider a donation.</strong>
  <br />
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="79JGZPNUSH6JN" /> <input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/DE/i/btn/btn_donateCC_LG.gif" type="image" /> <img src="https://www.paypal.com/de_DE/i/scr/pixel.gif" border="0" width="1" height="1" /> </form>
  <br />
  <br />  
  <strong>Your Version :</strong> <?php echo $ihrev;?><br />
  <strong>Newest Version :</strong>
<?php
$datei = @fopen($testen,'r');
$derzv = @fread($datei,16);
@fclose($datei);
if ($derzv == '') $derzv = $ihrev;
echo $derzv;
?>
  <br />
  <br />    
<?php if ($ihrev<$derzv) echo "<p style='color:red;'><strong>A newer version is available!</strong></p>"; ?>
  <br />  
  <strong>Copyright:</strong> © 2009 - 2011 <a href="http://www.extro-media.de">extro-media.de</a><br />
  <br />
  <strong>Homepage:</strong> <a href="http://www.extro-media.de">http://www.extro-media.de</a><br />
  <br />
  <strong>
  Sitelinkx is licensed under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv3</a></strong><br />
</p>
<p>If you want to translate Sitelinkx into your language or have a better <br />
translation for an existing localisation, please let us know.</p>

