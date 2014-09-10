<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Afptc
 * @version    1.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

try {
    $this->startSetup();
    $this->run("
        CREATE TABLE IF NOT EXISTS {$this->getTable('awafptc/rule')} (
          `rule_id` int(10) unsigned not null auto_increment,
          `name` varchar(255) default null,
          `description` text default null,
          `status` tinyint(2) unsigned not null,
          `store_ids` text not null,   
          `customer_groups` text not null,
          `discount` decimal(12,2) not null,
          `priority` int(10) unsigned not null,
          `show_popup` tinyint(2) unsigned not null,
          `show_once` tinyint(2) unsigned default null,
          `free_shipping` tinyint(2) unsigned not null,
          `product_id` int(10) default null,
          `conditions_serialized` text default null,  
          `start_date`  datetime default null,
          `end_date`  datetime default null, 
          PRIMARY KEY (`rule_id`),
          KEY `AW_AFPTC_STATUS` (`status`),   
          KEY `AW_AFPTC_SHOW_ONCE` (`show_once`), 
          KEY `AW_AFPTC_PRODUCT` (`product_id`), 
          KEY `AW_AFPTC_FREE_SHIPPING` (`free_shipping`), 
          KEY `AW_AFPTC_SHOW_POPUP` (`show_popup`), 
          KEY `AW_AFPTC_PRIORITY` (`priority`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        CREATE TABLE IF NOT EXISTS {$this->getTable('awafptc/state')} (
          `item_id` int(10) unsigned not null auto_increment,
          `quote_id` int(10) unsigned not null,          
          `customer_id` int(10) unsigned not null,
          `state` text not null,
          PRIMARY KEY (`item_id`),                       
          KEY `AW_AFPTC_STATE_QID` (`quote_id`),
          KEY `AW_AFPTC_STATE_CID` (`customer_id`)         
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        CREATE TABLE IF NOT EXISTS {$this->getTable('awafptc/used')} (
          `item_id` int(10) unsigned not null auto_increment,
          `rule_id` int(10) unsigned not null,          
          `quote_id` int(10) unsigned not null,
          `is_removed` tinyint(2) unsigned default null,
          PRIMARY KEY (`item_id`),               
          KEY `AW_AFPTC_USED_QID` (`quote_id`),
          KEY `FK_AW_AFPTC_USED_RID` (`rule_id`),
          CONSTRAINT `FK_AW_AFPTC_USED_RID` FOREIGN KEY (`rule_id`)
          REFERENCES {$this->getTable('awafptc/rule')} (`rule_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    $this
        ->getConnection()
        ->addKey($this->getTable('awafptc/used'), 'AW_AFPTC_USED_COMPOSITE', array('quote_id','rule_id'), 'unique')
    ;
    $this->endSetup();
} catch (Exception $e) {
    echo $e->getMessage();
    Mage::logException($e);
}