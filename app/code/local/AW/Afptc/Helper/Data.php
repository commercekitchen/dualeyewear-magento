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

class AW_Afptc_Helper_Data extends Mage_Core_Helper_Abstract
{     
    const AW_AFPTC_RULE_DECLINE       = 'aw-afptc-rule-decline';
    const AW_AFPTC_POPUP_DECLINE      = 'aw-afptc-popup-decline';

    const GENERAL_ENABLED             = 'awafptc/general/enable';
    const GENERAL_ALLOW_READD_TO_CART = 'awafptc/general/allow_readd_to_cart';
    const POPUP_DO_NOT_SHOW_ALLOWED   = 'awafptc/popup/do_not_show_allowed';
    const POPUP_COOKIE_LIFETIME       = 'awafptc/popup/cookie_lifetime';

    public function isAllowReAddToCart($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_ALLOW_READD_TO_CART, $store);
    }

    public function getCustomerGroup()
    {        
        return $this->_session()->isLoggedIn() ? $this->_session()->getCustomer()->getGroupId() : 0;
    }
    
    public function getCustomerId()
    {
        return $this->_session()->getCustomer()->getId();
    }

    public function getDeclineRuleCookieName($ruleId)
    {
        return self::AW_AFPTC_RULE_DECLINE . '-' . $ruleId;
    }

    public function getDeclinePopupCookieName()
    {
        return self::AW_AFPTC_POPUP_DECLINE;
    }

    public function setDeclineRuleCookie($ruleId)
    {
        $cookie = Mage::getSingleton('core/cookie');
        $cookie->set($this->getDeclineRuleCookieName($ruleId), '1', time() + $this->getCookieLifetime(), '/');
        return $this;
    }

    public function getDeclineRuleCookie($ruleId)
    {
        $cookie = Mage::getSingleton('core/cookie');
        return $cookie->get($this->getDeclineRuleCookieName($ruleId));
    }

    public function extensionDisabled($store = null)
    {        
        return !$this->isModuleOutputEnabled()
            || !Mage::getStoreConfig(self::GENERAL_ENABLED, $store)
        ;
    }

    public function isDoNotShowOptionAllowed($store = null)
    {
        return Mage::getStoreConfig(self::POPUP_DO_NOT_SHOW_ALLOWED, $store);
    }

    public function getCookieLifetime($store = null)
    {
        return (int)Mage::getStoreConfig(self::POPUP_COOKIE_LIFETIME, $store);
    }

    protected function _session()
    {
        return Mage::getSingleton('customer/session');
    }

    public function removeDeclineCookies()
    {
        $cookie = Mage::getSingleton('core/cookie');
        $store = Mage::app()->getStore();
        $rulesCollection = Mage::getResourceModel('awafptc/rule')->getActiveRulesCollection($store);
        foreach ($rulesCollection as $rule) {
            $cookie->set($this->getDeclineRuleCookieName($rule->getId()), '0', time() + $this->getCookieLifetime(),'/');
        }
        return $this;
    }
}