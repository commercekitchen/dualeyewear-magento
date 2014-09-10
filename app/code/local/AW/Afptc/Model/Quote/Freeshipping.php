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

class AW_Afptc_Model_Quote_Freeshipping extends Mage_Sales_Model_Quote_Address_Total_Abstract
{   
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        foreach ($this->_getAddressItems($address) as $item) {
            if ($option = $item->getProduct()->getCustomOption('aw_afptc_rule')) {
                $item->setFreeShipping((bool) Mage::getModel('awafptc/rule')
                        ->load($option->getValue())->getFreeShipping()
                );
            }
        }
        return $this;
    }
}