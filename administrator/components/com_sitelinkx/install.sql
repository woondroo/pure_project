CREATE TABLE IF NOT EXISTS `#__sitelinkx` (
  `id` int(11) NOT NULL auto_increment,
  `wort` varchar(2000) NOT NULL,
  `ersatz` varchar(2000) NOT NULL,
  `schlagwort` varchar(2000) NOT NULL,
  `fenster` varchar(10) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `begpub` datetime NOT NULL,
  `endpub` datetime NOT NULL,
  `anzahl` INT(11) NOT NULL,
  `suchm` INT(11) NOT NULL,      
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `bak_sitelinkx`;

RENAME TABLE `#__sitelinkx` TO `bak_sitelinkx`;

CREATE TABLE IF NOT EXISTS `#__sitelinkx` (
  `id` int(11) NOT NULL auto_increment,
  `wort` varchar(2000) NOT NULL,
  `ersatz` varchar(2000) NOT NULL,
  `schlagwort` varchar(2000) NOT NULL,
  `fenster` varchar(10) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `begpub` datetime NOT NULL,
  `endpub` datetime NOT NULL,
  `anzahl` INT(11) NOT NULL,
  `suchm` INT(11) NOT NULL,      
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__sitelinkx_config`;

CREATE TABLE IF NOT EXISTS `#__sitelinkx_config` (
  `id` int(11) NOT NULL auto_increment,
  `version` float NOT NULL,
  `anzahl` int(11) NOT NULL,
  `suchm` int(11) NOT NULL,
  `erreichb` tinyint(1) NOT NULL,
  `fenster` varchar(10) NOT NULL,
  `hinweis` tinyint(1) NOT NULL,    
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__sitelinkx_config` (`id`, `version`, `anzahl`, `suchm`, `erreichb`, `fenster`, `hinweis`) VALUES ('1', '1.54', '0', '0', '0', '_blank', '1'); 