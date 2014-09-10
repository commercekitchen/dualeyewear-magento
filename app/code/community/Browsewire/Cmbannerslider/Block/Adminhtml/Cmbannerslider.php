<?php

class Browsewire_Cmbannerslider_Block_Adminhtml_Cmbannerslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cmbannerslider';
    $this->_blockGroup = 'cmbannerslider';
    $this->_headerText = Mage::helper('cmbannerslider')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('cmbannerslider')->__('Add Item');
    parent::__construct();
  }
}
