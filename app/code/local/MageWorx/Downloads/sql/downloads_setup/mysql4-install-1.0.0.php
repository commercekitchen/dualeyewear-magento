<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Downloads extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

/* @var $installer MageWorx_Downloads_Model_Mysql4_Setup */
$installer = $this;
$installer->installEntities();

$installer->startSetup();

$storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
if (Mage::app()->isSingleStoreMode()) {
    $storeId = Mage::app()->getStore(true)->getId();
}

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('downloads/categories')};
CREATE TABLE IF NOT EXISTS {$this->getTable('downloads/categories')} (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` smallint(6) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO {$this->getTable('downloads/categories')} (`category_id`,`store_id`,`title`,`description`,`is_active`) VALUES
  (" . MageWorx_Downloads_Helper_Data::DEFAULT_CATEGORY_ID . "," . $storeId . ",'Default','Default category'," . MageWorx_Downloads_Helper_Data::STATUS_ENABLED . ");

-- DROP TABLE IF EXISTS {$installer->getTable('downloads/files')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('downloads/files')} (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_description` text,
  `type` varchar(10) NOT NULL,
  `size` int(10) unsigned NOT NULL default '0',
  `allow_guests` tinyint(1) unsigned NOT NULL default '1',
  `customer_groups` text,
  `downloads` int(10) unsigned NOT NULL,
  `downloads_limit` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
   PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('downloads/relation')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('downloads/relation')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `UNQ_MAGEWORX_DOWNLOADS_RELATION` (`file_id`,`product_id`),
   CONSTRAINT `FK_MAGEWORX_DOWNLOADS_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
