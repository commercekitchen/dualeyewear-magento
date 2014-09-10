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

class AW_Afptc_Block_Adminhtml_Rules_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $rule = Mage::registry('awafptc_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('general_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->__('General Information')));
 
        $fieldset->addField('name', 'text', array(
            'label' => $this->__('Rule Name'),
            'title' => $this->__('Rule Name'),
            'required' => true,
            'name' => 'name'
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => $this->__('Description'),
            'title' => $this->__('Description'),
            'name' => 'description'
        ));

        $fieldset->addField('status', 'select', array(
            'label' => $this->__('Status'),
            'title' => $this->__('Status'),
            'name' => 'status',
            'options' => array(
                '1' => $this->__('Enabled'),
                '0' => $this->__('Disabled'),
            ),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $rule->setStoreIds(0);
            $fieldset->addField('store_ids', 'hidden', array(
                'name' => 'store_ids[]'
            ));
        } else {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name' => 'store_ids[]',
                'label' => $this->__('Store View'),
                'title' => $this->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups,
                array(
                     'value' => 0,
                     'label' => Mage::helper('salesrule')->__('NOT LOGGED IN')
                )
            );
        }

        $fieldset->addField('customer_groups', 'multiselect', array(
            'name'     => 'customer_groups[]',
            'label'    => $this->__('Customer Groups'),
            'title'    => $this->__('Customer Groups'),
            'required' => true,
            'values'   => $customerGroups
        ));

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('start_date', 'date', array(
            'name'         => 'start_date',
            'label'        => $this->__('From Date'),
            'title'        => $this->__('From Date'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => $outputFormat,
            'required'     => true,
            'time'         => true
        ));

        $fieldset->addField('end_date', 'date', array(
            'name'         => 'end_date',
            'label'        => $this->__('To Date'),
            'title'        => $this->__('To Date'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => $outputFormat,
            'time'         => true,
            'required'     => true
        ));
     
        $fieldset->addField('show_popup', 'select', array(
            'label' => $this->__('Show Pop Up on adding Item(s) to Cart'),
            'title' => $this->__('Show Pop Up on adding Item(s) to Cart'),
            'name' => 'show_popup',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No')
            ),
        ));

        $fieldset->addField('priority', 'text', array(
            'label' => $this->__('Priority'),
            'title' => $this->__('Priority'),            
            'note'  => $this->__('Rules with greater priority are processed first'),
            'name'  => 'priority'
        ));

        $form->setValues($rule->getData());

        if (null !== $rule->getData('start_date') && $form->getElement('start_date')) {
            $form->getElement('start_date')->setValue(
                Mage::app()->getLocale()->date($rule->getData('start_date'), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }

        if (null !== $rule->getData('end_date') && $form->getElement('end_date')) {
            $form->getElement('end_date')->setValue(
                Mage::app()->getLocale()->date($rule->getData('end_date'), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}