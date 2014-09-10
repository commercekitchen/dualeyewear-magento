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

$installer = $this;

$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('awafptc/state')};
    DROP TABLE IF EXISTS {$this->getTable('awafptc/used')};
    ALTER TABLE {$this->getTable('awafptc/rule')} ADD `stop_rules_processing` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `priority`;
    ALTER TABLE {$this->getTable('awafptc/rule')} ADD `simple_action` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `stop_rules_processing`;
    ALTER TABLE {$this->getTable('awafptc/rule')} ADD `discount_step` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `simple_action`;

");
$installer->endSetup();