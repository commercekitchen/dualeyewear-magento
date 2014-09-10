<?php

class Browsewire_Cmbannerslider_Block_Adminhtml_Cmbannerslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('cmbannerslider_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cmbannerslider')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('cmbannerslider')->__('Item Information'),
          'title'     => Mage::helper('cmbannerslider')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('cmbannerslider/adminhtml_cmbannerslider_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
