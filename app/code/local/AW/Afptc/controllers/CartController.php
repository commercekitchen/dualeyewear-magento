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

require_once 'Mage' . DS . 'Checkout' . DS . 'controllers' . DS . 'CartController.php';

class AW_Afptc_CartController extends Mage_Checkout_CartController
{     
    public function addProductAction()
    {
        $products = $this->getRequest()->getParam('products', null);
        if (null === $products) {
            $this->_goBack();
            return false;
        }

        $cartModel = Mage::getSingleton('checkout/cart');
        foreach ($products as $ruleId => $itemIds) {
            $ruleModel = Mage::getModel('awafptc/rule')->load($ruleId);
            if (null === $ruleModel->getId()) {
                continue;
            }

            foreach ($itemIds as $itemId) {
                try {
                    $ruleModel->apply($cartModel, $itemId);
                } catch (Exception $e) {
                    $errorMessage = $e->getMessage();
                    break;
                }
            }
        }

        if (isset($errorMessage)) {
            $this->_getSession()->addError($this->__($errorMessage));
            $this->_goBack();
            return false;
        }

        $cartModel->getQuote()->unsTotalsCollectedFlag()->collectTotals()->save();
        $message = $this->__('Free products were added to your shopping cart.');
        $this->_getSession()->addSuccess($message);
        $this->_goBack();
    }

    public function getPopupHtmlAction()
    {
        $this->loadLayout('awafptc_popup');
        $popupBlock = $this->getLayout()->getBlock('awafptc.popup');
        $response = array(
            'content' => $popupBlock->toHtml()
        );
        $response = Mage::helper('core')->jsonEncode($response);
        $this->getResponse()->setBody($response);
    }
}