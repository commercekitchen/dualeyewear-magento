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

class AW_Afptc_Block_Adminhtml_Rules_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $rule = Mage::registry('awafptc_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl(
                    'awafptc_admin/adminhtml_rules/newConditionHtml/form/rule_conditions_fieldset'
                )
            )
        ;

        $fieldset = $form
            ->addFieldset(
                'conditions_fieldset',
                array(
                    'legend' => $this->__(
                        'Apply the rule only if the following conditions are met (leave blank for all products)'
                    )
                )
            )
            ->setRenderer($renderer)
        ;

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
        ))->setRule($rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
   
        $this->setForm($form);
        return parent::_prepareForm();
    }
}