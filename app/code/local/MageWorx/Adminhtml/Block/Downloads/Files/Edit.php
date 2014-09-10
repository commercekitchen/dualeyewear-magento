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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * File Downloads & Product Attachments extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @author     MageWorx Dev Team
 */
class MageWorx_Adminhtml_Block_Downloads_Files_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'mageworx';
        $this->_controller = 'downloads_files';
        $helper = Mage::helper('downloads');

        parent::__construct();

        $this->_updateButton('save', '', array(
            'label' => $helper->__('Save'),
            'onclick' => 'saveDownloadsForm()',
            'class' => 'save',
            'sort_order' => 30
        ), 1);

        $this->_updateButton('delete', '', array(
            'label' => $helper->__('Delete'),
            'onclick' => "deleteConfirm('{$helper->__('Are you sure you want to do this?')}', '{$this->getUrl('*/*/delete', array('id' => (int) $this->getRequest()->getParam('id')))}')",
            'class' => 'delete',
            'sort_order' => 10
        ));

        if (Mage::app()->getRequest()->getActionName() != 'multiupload' && Mage::app()->getRequest()->getActionName() != 'assignProducts') {
            $this->_addButton('saveandcontinue', array(
                'label' => $helper->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
                'sort_order' => 20
            ), -100);
        }

        $this->_formScripts[] = "
        	function saveDownloadsForm() {
        		applySelectedProducts('save')
            }
            function saveAndContinueEdit() {
                applySelectedProducts('saveandcontinue')
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('downloads_data') && Mage::registry('downloads_data')->getId()) {
            return Mage::helper('downloads')->__("Edit File '%s'", $this->htmlEscape(Mage::registry('downloads_data')->getName()));
        } else {
            return Mage::helper('downloads')->__('Upload File');
        }
    }

}