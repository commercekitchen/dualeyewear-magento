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

class AW_Afptc_Block_Adminhtml_Rules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awafptc_info_tabs');
        $this->setDestElementId('edit_form');       
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label' => $this->__('Rule Info'),
            'title' => $this->__('Rule Info'),
            'content' => $this->getLayout()->createBlock('awafptc/adminhtml_rules_edit_tab_general')->toHtml()
        ));
        
         $this->addTab('conditions_section', array(
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
            'content' => $this->getLayout()->createBlock('awafptc/adminhtml_rules_edit_tab_conditions')->toHtml()
        ));
         
          $this->addTab('action_section', array(
            'label' => $this->__('Action'),
            'title' => $this->__('Action'),
            'content' => $this->getLayout()->createBlock('awafptc/adminhtml_rules_edit_tab_action')->toHtml()
        ));
          
        $this->_updateActiveTab();
      
        return parent::_beforeToHtml();
    }
    
    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}