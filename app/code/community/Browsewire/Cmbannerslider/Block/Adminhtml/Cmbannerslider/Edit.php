<?php

class Browsewire_Cmbannerslider_Block_Adminhtml_Cmbannerslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'cmbannerslider';
        $this->_controller = 'adminhtml_cmbannerslider';
        
        $this->_updateButton('save', 'label', Mage::helper('cmbannerslider')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('cmbannerslider')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('cmbannerslider_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'cmbannerslider_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'cmbannerslider_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('cmbannerslider_data') && Mage::registry('cmbannerslider_data')->getId() ) {
            return Mage::helper('cmbannerslider')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('cmbannerslider_data')->getTitle()));
        } else {
            return Mage::helper('cmbannerslider')->__('Add Item');
        }
    }
}
