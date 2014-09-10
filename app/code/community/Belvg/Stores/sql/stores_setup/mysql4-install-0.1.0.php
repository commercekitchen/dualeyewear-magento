<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS ".$installer->getTable('belvg_stores')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100),
  `desc` text,
  `file_preview` varchar(255) ,
  `store` int(11),
  `country` varchar(100),
  `zip_code` varchar(100),
  `address` varchar(100),
  `city` varchar(100),
  `state` varchar(100),
  `fax` varchar(100),
  `phone` varchar(100),
  `lat` varchar(50),
  `lng` varchar(50),
  `is_all_products` int(11) ,
  `picture_desc` text ,
  FULLTEXT KEY `ft3` (`address`,`zip_code`,`country`,`city`,`state`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;
");   