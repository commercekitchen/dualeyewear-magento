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
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_Adminhtml_Block_Downloads_Files_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('downloads_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->_getHelper()->__('General Information'));
    }

    protected function _beforeToHtml()
    {
        $helper = $this->_getHelper();

        $assignProducts = Mage::app()->getRequest()->getActionName() == 'assignProducts';

        if (!$assignProducts) {
            $this->addTab('general_tab', array(
                'label' => $helper->__('File'),
                'title' => $helper->__('File'),
                'content' => $this->getLayout()->createBlock('mageworx/downloads_files_edit_tab_general')->toHtml(),
                'active' => true,
            ));
        }

        $this->addTab('product_tab', array(
            'label' => $helper->__('Products'),
            'title' => $helper->__('Products'),
            'content' => $this->getLayout()->createBlock('mageworx/downloads_files_edit_tab_product')->toHtml(),
            'active' => $assignProducts ? true : false
        ));

        return parent::_beforeToHtml();
    }

    private function _getHelper()
    {
        return Mage::helper('downloads');
    }

}