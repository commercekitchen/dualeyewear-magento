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
class MageWorx_Adminhtml_Block_Downloads_Files_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper = $this->_getHelper();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general_form_legend', array('legend' => $helper->__('Files')));

        $categories = Mage::getSingleton('downloads/categories')->getCategoriesList();

        $fieldset->addField('category_id', 'select', array(
            'label' => $helper->__('Category'),
            'name' => 'general[category_id]',
            'values' => $categories,
            'required' => true
        ));

        if (Mage::app()->getRequest()->getActionName() != 'multiupload') {
            $fieldset->addField('name', 'text', array(
                'label' => $helper->__('Name'),
                'name' => 'general[name]',
                'index' => 'name',
                'required' => true
            ));
        }

        $fieldset->addField('file_description', 'textarea', array(
            'label' => $helper->__('Description'),
            'name' => 'general[file_description]',
            'index' => 'file_description',
        ));

        $fieldset->addField('downloads_limit', 'text', array(
            'label' => $helper->__('Downloads Limit'),
            'name' => 'general[downloads_limit]',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', array(
                'label' => $helper->__('Stores'),
                'name' => 'general[store_ids]',
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'general[store_ids]',
                'value' => Mage::app()->getStore(true)->getId(),
            ));
        }

        $customerGroups = $this->_getCustomerGroups();
        if ($customerGroups) {
            $fieldset->addField('customer_groups', 'multiselect', array(
                'label' => $helper->__('Customer Groups'),
                'name' => 'general[customer_groups][]',
                'values' => $customerGroups,
            ));
        }

        $fileId = Mage::app()->getRequest()->getParam('id');

        if (Mage::app()->getRequest()->getActionName() == 'multiupload') {
            $multiUpload = array(
                'label' => $helper->__('Multi Upload'),
                'name' => 'general[multiupload]',
                'index' => 'multiupload',
                'values' => $fileId ? $fileId : uniqid()
            );

            $fieldset->addField('multiupload', 'multiupload', $multiUpload);
        } else {
            $fileConf = array('label' => $helper->__('File'), 'name' => 'file');
            if ($fileId) {
                $fileConf['after_element_html'] = '<p class="nm"><small><a href="' . $this->getUrl('*/*/download', array('id' => $fileId)) . '">' . $helper->__('Download') . '</a></small></p>';
            } else {
                //$fileConf['required'] = true;
            }
            $fieldset->addField('file', 'file', $fileConf);

            $fieldset->addField('url', 'text', array(
                'label' => $helper->__('URL'),
                'name' => 'general[url]',
                'index' => 'url',
                'after_element_html' => '<p class="note"><span>' . $this->__('When uploading video URL embedded video code is required') . '</span></p>'
            ));

            $fieldset->addField('embed_code', 'textarea', array(
                'label' => $helper->__('Embedded Video Code'),
                'name' => 'general[embed_code]',
                'required' => false
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'label' => $helper->__('Status'),
            'name' => 'general[is_active]',
            'index' => 'is_active',
            'values' => $helper->getStatusArray()
        ));

        $session = Mage::getSingleton('adminhtml/session');
        if ($data = $session->getData('downloads_data')) {
            $form->setValues($data['general']);
        } elseif (Mage::registry('downloads_data')) {
            $form->setValues(Mage::registry('downloads_data')->getData());
        }
        $this->setForm($form);

        return $this;
    }

    protected function _getCustomerGroups()
    {
        $result = array();
        $customerGroups = Mage::getSingleton('customer/group')->getCollection()->getItems();
        if ($customerGroups) {
            foreach ($customerGroups as $item) {
                $result[] = array(
                    'value' => $item->getData('customer_group_id'),
                    'label' => $item->getData('customer_group_code')
                );
            }
        }
        return $result;
    }

    protected function _getHelper()
    {
        return Mage::helper('downloads');
    }

}