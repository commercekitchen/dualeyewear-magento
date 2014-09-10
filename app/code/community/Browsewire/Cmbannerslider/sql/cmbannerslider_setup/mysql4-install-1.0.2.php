<?php

$createdtime = now();
$updatedtime = now();
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cmbannerslider')};
CREATE TABLE {$this->getTable('cmbannerslider')} (
  `cmbannerslider_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NULL,
  `status` smallint(6) NOT NULL default '0',
  `weblink` varchar(255) NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`cmbannerslider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('cmbannerslider')} VALUES (1,'Browsewire','browsewire_logo_img.png','Browsewire',1,'http://www.browsewire.net/', '$createdtime','$updatedtime' );

    ");

$installer->endSetup(); 
