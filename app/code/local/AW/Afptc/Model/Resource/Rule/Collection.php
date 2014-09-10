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

class AW_Afptc_Model_Resource_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('awafptc/rule');
    }

    public function addStatusFilter()
    {
        $this->getSelect()->where('main_table.status = ?', 1);
        return $this;
    }

    public function addPriorityOrder()
    {
        $this->getSelect()->order('main_table.priority DESC');
        return $this;
    }
    
    public function addTimeLimitFilter()
    {
        $this->getSelect()
            ->where("if(main_table.end_date is null, true, main_table.end_date > UTC_TIMESTAMP()) AND
                if(main_table.start_date is null, true, main_table.start_date < UTC_TIMESTAMP())");

        return $this;
    }
    
    public function addStoreFilter($store)
    {
        $this->getSelect()->where('find_in_set(0, store_ids) OR find_in_set(?, store_ids)', $store);
        return $this;
    }
    
    public function addGroupFilter($group)
    {
        $this->getSelect()->where('find_in_set(?, customer_groups)', $group);
        return $this;
    }
}