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

class AW_Afptc_Model_Resource_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('awafptc/rule', 'rule_id');
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (is_array($object->getStoreIds()))
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        
        if (is_array($object->getCustomerGroups()))
            $object->setCustomerGroups(implode(',', $object->getCustomerGroups()));
    }

    public function getActiveRulesCollection(Mage_Core_Model_Store $store)
    {
        $rulesCollection = Mage::getModel('awafptc/rule')->getCollection();
        $rulesCollection
            ->addStatusFilter()
            ->addTimeLimitFilter()
            ->addStoreFilter((int) $store->getId())
            ->addGroupFilter((int) Mage::helper('awafptc')->getCustomerGroup())
            ->addPriorityOrder()
        ;
        return $rulesCollection;
    }
}