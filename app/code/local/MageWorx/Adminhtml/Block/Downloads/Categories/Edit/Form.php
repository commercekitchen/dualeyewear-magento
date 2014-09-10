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
class MageWorx_Adminhtml_Block_Downloads_Categories_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $helper = Mage::helper('downloads');
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('general_form_legend', array('legend' => $helper->__('General Information')));

        $fieldset->addField('store_id', 'hidden', array(
            'name' => 'store_id'
        ));

        $fieldset->addField('title', 'text', array(
            'label' => $helper->__('Name'),
            'name' => 'title',
            'required' => true
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => $helper->__('Short Description'),
            'required' => false,
            'name' => 'description'
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => $helper->__('Status'),
            'name' => 'is_active',
            'values' => $helper->getStatusArray()
        ));

        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getData('downloads_data')) {
            $form->setValues($session->getData('downloads_data'));
            $session->setData('downloads_data');
        } elseif (Mage::registry('downloads_data')) {
            $form->setValues(Mage::registry('downloads_data')->getData());
        }
        return parent::_prepareForm();
    }

}