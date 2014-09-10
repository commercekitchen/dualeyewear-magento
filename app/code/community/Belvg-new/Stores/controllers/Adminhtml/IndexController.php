<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Stores
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */


class Belvg_Stores_Adminhtml_IndexController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
        }
        return $this;
    }   
 
    public function indexAction()
    {        
        $this->_initAction()->renderLayout();
    }
        
    
    public function deleteAction()
    {
        $storesId = $this->getRequest()->getParam('id');
        $stateModel = Mage::getModel('stores/state');
        $stateModel->deleteStore($storesId);
        $this->_redirect('*/*/');
    }
    
    public function editAction()
    {    
        $this->_initAction()->renderLayout();        
    }
    public function newAction()
    {
        $this->_initAction()->renderLayout();        
    }
    
    public function saveAction()
    {
        $storeData    = $this->getRequest()->getPost('storeform');
        $supportedProducts    = $this->getRequest()->getParam('stores_assigned_products');
        $id        = '';
        $id        = $this->getRequest()->getParam('id');
        Mage::getModel('stores/state')->saveStore($id,$storeData,$supportedProducts);
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('stores/adminhtml_index_edit_tab_products')->toHtml();
        $this->getResponse()->setBody(
           $block
        );
    }    
}
