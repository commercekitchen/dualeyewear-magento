<?php
class Raveinfosys_Deleteorder_Block_Adminhtml_Deleteorder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_deleteorder';
    $this->_blockGroup = 'deleteorder';
    $this->_headerText = Mage::helper('deleteorder')->__('Delete Order');
   // $this->_addButtonLabel = Mage::helper('deleteorder')->__('Add Item');
    parent::__construct();
  }
}