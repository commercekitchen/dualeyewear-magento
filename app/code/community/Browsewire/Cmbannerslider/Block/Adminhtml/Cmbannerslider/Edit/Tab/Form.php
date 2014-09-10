<?php
/**
 * @author Ocodewire (ocodewire.com)
 * @copyright  Copyright (c)  ocodewire
 * @version : 1.0.2
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Browsewire_Cmbannerslider_Block_Adminhtml_Cmbannerslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cmbannerslider_form', array('legend'=>Mage::helper('cmbannerslider')->__('Item information')));
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('cmbannerslider')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('cmbannerslider')->__('Image File'),
          'required'  => false,
          'name'      => 'filename',
	  ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('cmbannerslider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('cmbannerslider')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('cmbannerslider')->__('Disabled'),
              ),
          ),
      ));

	$fieldset->addField('weblink', 'text', array(
          'label'     => Mage::helper('cmbannerslider')->__('Web Url'),
          'required'  => false,
          'name'      => 'weblink',
      ));

      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('cmbannerslider')->__('Content'),
          'title'     => Mage::helper('cmbannerslider')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));

      if ( Mage::getSingleton('adminhtml/session')->getCmbannerSliderData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCmbannerSliderData());
          Mage::getSingleton('adminhtml/session')->setCmbannerSliderData(null);
      } elseif ( Mage::registry('cmbannerslider_data') ) {
          $form->setValues(Mage::registry('cmbannerslider_data')->getData());
      }
      return parent::_prepareForm();
  }
}
