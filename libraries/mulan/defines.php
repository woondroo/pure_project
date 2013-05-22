<?php
$base = JURI::root(); // http://localhost/joomla1.7
define('DS2', '/');
define('URI_BASE', (strpos($base, 'administrator') === false) ? $base : dirname($base).DS2);
define('DIR_ML', URI_BASE .'media'.DS2.'mulan'.DS2);
define('DIR_CSS', DIR_ML.'css'.DS2);
define('DIR_JS', DIR_ML.'js'.DS2);
define('DIR_IMG', DIR_ML.'images'.DS2);
?>