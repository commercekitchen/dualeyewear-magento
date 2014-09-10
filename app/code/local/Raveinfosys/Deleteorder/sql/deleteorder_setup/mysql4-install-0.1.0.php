<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('deleteorder')};    
	CREATE TABLE {$this->getTable('deleteorder')} (
  `deleteorder_id` int(11) NOT NULL auto_increment,   
  `internal_company_id` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`deleteorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 