<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Stores
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Stores_Block_Adminhtml_Index_Edit_Tab_Media extends Mage_Adminhtml_Block_Widget_Form
{

    /**
    * Create Form for Image Uploader
    */
	
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        
        $fieldset    = $form->addFieldset('media', array('legend'=>Mage::helper('core')->__('Media Settings')));
        
        $fieldset->addField('file_new_preview', 'file', array(
          'label'     => Mage::helper('stores')->__('Select File'),
          'required'  => false,
          'name'      => 'file_new_preview',
        ));
         
        $this->setForm($form); 
        $this->setTemplate('stores/tabs/media.phtml');
    }
    
    /**
    * Get Media Image for current Store, if there isn't return false
    *
    * @return array $media or boolean false
    */
	
	public function getMedia(){
        $stores_id = $this->getRequest()->getParam('id',0);
        if ($stores_id){
             $media = Mage::getModel('stores/state')->getMedia($stores_id);
             return $media;
        }
        else return false;
    }
}
