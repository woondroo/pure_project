<?php
/**
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @plugin Sitelinkx
 * @copyright Copyright (C) www.extro-media.de
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentSitelinkx extends JPlugin {

	function onContentPrepare( $context, &$article, &$params, $page=0 ) {
		if ( JString::strpos( $article->text, '{sitelinkxoff}' ) !== false ) {
			$article->text = str_replace('{sitelinkxoff}','',$article->text);
			return true;
		}
		$wurdeersetzt = 0;
		$wieoft = 0;
		$daba =& JFactory::getDBO();
		
		$anfr = "SELECT * FROM `#__sitelinkx_config`";
		$daba->setQuery($anfr);
		$ergeb = $daba->loadObjectList();
		$hinweis = $ergeb[0]->hinweis;
		$rel = '';
		if($hinweis == 1) {
			$rel = 'rel="nofollow"';
			}
		$anzahl = $ergeb[0]->anzahl;
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM `#__sitelinkx` ORDER BY wort";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$count = count( $rows );
		$suche = '`<[^>]*>`';
		$derzz =& JFactory::getDate()->toFormat();
		
		for($i = 0 ; $i<$count ; $i++) {
			if ($rows[$i]->endpub == "0000-00-00 00:00:00") $rows[$i]->endpub ="9999-12-31 23:59:59";
			switch($derzz) {
				case ($derzz < $rows[$i]->begpub);
					break;
				case ($derzz > $rows[$i]->endpub);
					break;
				default:
					$ausgabe = preg_split($suche, $article->text, -1, PREG_SPLIT_OFFSET_CAPTURE);
					preg_match_all($suche, $article->text, $anker);
					$de = $rows[$i]->anzahl;
					$wort = $rows[$i]->wort;
					$wordboundary = '';
					if ($rows[$i]->suchm == '0') $wordboundary = '([><\s.?!:,;\'=~_"/(){}\[\]\\\\+)'; //changed by Marc: local and unicode-independent word boundary
					
					/* start inserted Marc Mittag for wordboundary */
					$search_arr = array();
					$search_arr[] = '`'.$wordboundary.$wort.$wordboundary.'`'; 
					$search_arr[] = '`'.$wordboundary.$wort.'$`'; 
					$search_arr[] = '`^'.$wort.$wordboundary.'`'; 
					$search_arr[] = '`^'.$wort.'$`'; 
					$ersatz = '<a class="sitelinkx" href="'.$rows[$i]->ersatz.'" title="'.$rows[$i]->schlagwort.'" target="'.$rows[$i]->fenster.'" '.$rel.' >'.$wort.'</a>';
					$replace_arr = array();
					$replace_arr[] = '\\1'.$ersatz.'\\2';
					$replace_arr[] = '\\1'.$ersatz;
					$replace_arr[] = $ersatz.'\\1';
					$replace_arr[] = $ersatz;
					/* end inserted Marc Mittag for wordboundary */
					$insgesamt = 0;
					for ($j=0; $j < count($ausgabe); $j++) {
						if($insgesamt < $anzahl) {
						  $ausgabe[$j][0] = preg_replace( $search_arr, $replace_arr, $ausgabe[$j][0], $anzahl, $wieoft );
						  $insgesamt += $wieoft;
						}
						if ($wieoft > 0) {
							$wurdeersetzt = 1;
						}
					}
					$zusammen = '';
					for ($k=0; $k < count($ausgabe); $k++) {
						$anker_hinzu = "";
						if(isset($anker[0][$k])) {
							$anker_hinzu = $anker[0][$k];
						}
						$zusammen = $zusammen . $ausgabe[$k][0] . $anker_hinzu; 
					}
					
					$article->text = $zusammen;
					
					$wort='';
					$linkwort='';
					$ersatz='';
					$ausgabe='';
					$zusammen='';
			}
		}
	}
}
