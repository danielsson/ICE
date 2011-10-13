<?php
$queries = array(
"CREATE TABLE IF NOT EXISTS `ice_content` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `fieldname` varchar(64) NOT NULL,
 `content` text,
 `pagename` varchar(64) NOT NULL,
 `fieldtype` enum('field','area') NOT NULL DEFAULT 'field',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;",

"CREATE TABLE IF NOT EXISTS `ice_users` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `username` varchar(125) NOT NULL DEFAULT '--',
 `password` varchar(125) NOT NULL DEFAULT '--',
 `userlevel` enum('0','1','2','3') NOT NULL DEFAULT '0',
 `keyCardHash` varchar(32) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;",

"CREATE TABLE IF NOT EXISTS `ice_files` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(64) NOT NULL DEFAULT 'Unnamed',
 `path` varchar(256) DEFAULT NULL,
 `url` varchar(256) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;",

"CREATE TABLE IF NOT EXISTS `ice_pages` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(256) DEFAULT 'NoName',
 `tid` int(11) DEFAULT NULL,
 `url` varchar(256) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;",

"INSERT INTO ice_users (username,password,userlevel) VALUES ('admin','21232f297a57a5a743894a0e4a801fc3','3');");

?>