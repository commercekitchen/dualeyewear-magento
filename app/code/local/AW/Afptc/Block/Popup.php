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

class AW_Afptc_Block_Popup extends Mage_Catalog_Block_Product_Abstract
{
    protected $_products = null;

    public function canShow()
    {
        return (!$this->helper('awafptc')->extensionDisabled()
            && count($this->getProducts()) != 0
            && !$this->_cookieDisallowed())
        ;
    }

    protected function _getPreparedProduct($productId, $ruleModel)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        if (null !== $product->getId() && $product->isSaleable()) {
            $this->_prepareProductPriceForRule($ruleModel, $product);
            return $product;
        }
        return null;
    }

    protected function _prepareProductsByRule($ruleModel)
    {
        if ($ruleModel->getSimpleAction() == AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION) {
            foreach ($ruleModel->getBuyXProductIds() as $itemId => $productId) {
                $_product = $this->_getPreparedProduct($productId, $ruleModel);
                if (null === $_product) {
                    continue;
                }
                $_product->addData(array(
                    'afptc_item_id' => $itemId,
                    'afptc_rule_id' => $ruleModel->getId()
                ));
                array_push($this->_products, $_product);
            }
        }

        if ($ruleModel->getSimpleAction() == AW_Afptc_Model_Rule::BY_PERCENT_ACTION) {
            $_product = $this->_getPreparedProduct($ruleModel->getProductId(), $ruleModel);
            if (null !== $_product) {
                $_product->addData(array(
                    'afptc_item_id' => null,
                    'afptc_rule_id' => $ruleModel->getId()
                ));
                array_push($this->_products, $_product);
            }
        }
        return $this;
    }

    public function getProducts()
    {
        $cartModel = Mage::getSingleton('checkout/cart');
        if (null === $this->_products) {
            $store = Mage::app()->getStore();
            $rulesCollection = Mage::getResourceModel('awafptc/rule')->getActiveRulesCollection($store);
            $this->_products = array();
            foreach ($rulesCollection as $ruleModel) {
                $ruleModel->load($ruleModel->getId());
                if (!$ruleModel->validate($cartModel)) {
                    continue;
                }

                if (isset($_stopFlag)) {
                    break;
                }

                if ($ruleModel->getStopRulesProcessing()) {
                    $_stopFlag = true;
                }

                if (!$ruleModel->getShowPopup()) {
                    continue;
                }

                if (Mage::helper('awafptc')->getDeclineRuleCookie($ruleModel->getId())) {
                    continue;
                }

                if ($ruleModel->getSimpleAction() == AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION
                    && count($ruleModel->getBuyXProductIds()) == 0
                ) {
                    continue;
                }

                if (true === $ruleModel->getAlreadyApplied()) {
                    continue;
                }

                $this->_prepareProductsByRule($ruleModel);
            }
        }
        return $this->_products;
    }

    public function getPostUrl()
    {
        return $this->getUrl('awafptc/cart/addProduct');
    }

    public function getDeclinePopupCookieName()
    {
        return $this->helper('awafptc')->getDeclinePopupCookieName();
    }

    public function getDoNotShowAllowed()
    {
        return $this->helper('awafptc')->isDoNotShowOptionAllowed();
    }

    protected function _prepareProductPriceForRule(AW_Afptc_Model_Rule $ruleModel, &$productModel)
    {
        $finalPrice = $productModel->getFinalPrice();
        $productModel->setFinalPrice(max(0, $finalPrice - ($finalPrice * $ruleModel->getDiscount() / 100)));
        return $this;
    }

    protected function _cookieDisallowed()
    {
        return Mage::getSingleton('core/cookie')->get($this->getDeclinePopupCookieName());
    }
}