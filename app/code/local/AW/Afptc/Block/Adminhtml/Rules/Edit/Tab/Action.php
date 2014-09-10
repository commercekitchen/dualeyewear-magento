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

class AW_Afptc_Block_Adminhtml_Rules_Edit_Tab_Action extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $rule = Mage::registry('awafptc_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('action_');

        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend'=> $this->__('Action')
        ));
        
        if(!$rule->getId()) {
            $rule->setDiscount(100);
        }

        $_ruleAction = $fieldset->addField('simple_action', 'select', array(
            'label'      => $this->__('Apply'),
            'name'       => 'simple_action',
            'options'    => array(
                AW_Afptc_Model_Rule::BY_PERCENT_ACTION  => $this->__('Percent of product price discount'),
                AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION => $this->__('Buy X get Y free (discount amount is Y)'),
            ),
        ));

        $_discountStep = $fieldset->addField('discount_step', 'text', array(
            'name'  => 'discount_step',
            'label' => $this->__('Discount Qty Step (Buy X)'),
            'class' => 'validate-not-negative-number'
        ));

        $_discountAmount = $fieldset->addField('discount', 'text', array(
            'label'    => $this->__('Discount Amount Applied to Product, %'),
            'title'    => $this->__('Discount Amount Applied to Product, %'),
            'required' => true,
            'name'     => 'discount',
            'class'    => 'validate-greater-than-zero validate-percents'
        ));
        
        $fieldset->addField('free_shipping', 'select', array(
            'label'   => $this->__('Free Shipping'),
            'title'   => $this->__('Free Shipping'),
            'name'    => 'free_shipping',
            'options' => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => $this->__('Stop Further Rules Processing'),
            'title'     => $this->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));
        $productsRenderBlock = $this->getLayout()->createBlock('awafptc/adminhtml_rules_edit_renderer_products');
        $form
            ->addFieldset('awafptc_grid_fieldset',
                array(
                   'fieldset_container_id' => 'aw-afptc-grid-products',
                   'class'                 => 'aw-afptc-grid-products',
                   'legend'                => $this->__('Action Product')
                )
            )
            ->addField('awafptc_grid_product', 'select',
                array(
                    'name'     => 'awafptc_grid_product',
                    'formdata' => $rule,
                )
            )
            ->setRenderer($productsRenderBlock)
        ;

        $form->setValues($rule->getData());
        $this->setForm($form);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($_ruleAction->getHtmlId(), $_ruleAction->getName())
                ->addFieldMap($_discountStep->getHtmlId(), $_discountStep->getName())
                ->addFieldMap($_discountAmount->getHtmlId(), $_discountAmount->getName())
                ->addFieldDependence(
                    $_discountAmount->getName(),
                    $_ruleAction->getName(),
                    AW_Afptc_Model_Rule::BY_PERCENT_ACTION
                )
                ->addFieldDependence(
                    $_discountStep->getName(),
                    $_ruleAction->getName(),
                    AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION
                )
        );
        return parent::_prepareForm();
    }
}