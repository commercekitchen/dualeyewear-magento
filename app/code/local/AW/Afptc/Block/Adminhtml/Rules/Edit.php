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

class AW_Afptc_Block_Adminhtml_Rules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'awafptc';
        $this->_controller = 'adminhtml_rules';
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(url) {
                   editForm.submit(
                        url.replace(/{{tab_id}}/ig,awafptc_info_tabsJsTabs.activeTab.id)
                   );
            }
            document.observe('dom:loaded', function() {
                awAfptcGridJsObject.reloadParams = {};
                awAfptcGridJsObject.reloadParams.checkedValues = '" . Mage::registry('awafptc_rule')->getProductId() . "'

                Event.observe('action_simple_action', 'change', function(event) {
                    var element = Event.element(event);
                    var value = element[element.selectedIndex].value;
                    changeActionProductVisibility(value);
                });

                var changeActionProductVisibility = function(value) {
                    if ($('aw-afptc-grid-products')) {
                        if (value == " . AW_Afptc_Model_Rule::BUY_X_GET_Y_ACTION . ") {
                            $('aw-afptc-grid-products').hide();
                        } else {
                            $('aw-afptc-grid-products').show();
                        }
                    }
                }

                if ($('action_simple_action')) {
                    changeActionProductVisibility($('action_simple_action').value);
                }
            });
        ";
        parent::__construct();
    }

    public function getHeaderText()
    {
        $auction = Mage::registry('awafptc_rule');
        if ($auction->getId()) {
            if ($auction->getName()) {
                return $this->__("Edit Rule '%s'", $this->htmlEscape($auction->getName()));
            }
            return $this->__("Edit Rule #'%s'", $this->htmlEscape($auction->getId()));
        } else {
            return $this->__('Create New Rule');
        }
    }

    protected function _prepareLayout()
    {
        $this->_addButton('save_and_continue', array(
            'label' => $this->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
            'class' => 'save'
                ), 10);

        parent::_prepareLayout();
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
                    '_current' => true,
                    'back' => 'edit',
                    'tab' => '{{tab_id}}'
        ));
    }
}