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
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Stores_Block_Adminhtml_Index_New_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        
        $counrtries = Mage::Helper('stores')->getCountryCollection();
        
        $form        = new Varien_Data_Form();
        $stores_id    = $this->getRequest()->getParam('id');
        $fieldset    = $form->addFieldset('general', array('legend'=>Mage::helper('core')->__('General Settings')));
        $wysiwygConfig    = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => 'page_tabs')
        );
       
        $fieldset->addField('title', 'text', array(
            'label'    => Mage::helper('core')->__('Store Name'),
            'title'    => Mage::helper('core')->__('Store Name'),
            'name'     => 'title',
            'width'    => '50px',                
            'required' => true,
        ));
        
        $fieldset->addField('zip_code', 'text', array(
                'label'    => Mage::helper('core')->__('Zip Code'),
                'title'    => Mage::helper('core')->__('Zip Code'),
                'name'     => 'zip_code',
                'style'     => 'width:50px;',
                'required' => false,
            ));  
            
            $fieldset->addField('country', 'select', array(
                'label'    => Mage::helper('core')->__('Country'),
                'title'    => Mage::helper('core')->__('Country'),
                'name'     => 'country',
                'options'  => $counrtries,
                'style'     => 'width:250px;',
                'required' => true,
            ));    
            
            $fieldset->addField('state', 'text', array(
                'label'    => Mage::helper('core')->__('State (Full Name)'),
                'title'    => Mage::helper('core')->__('State (Full Name)'),
                'name'     => 'state',
                'style'     => 'width:250px;',
                'required' => false,
            ));

            $fieldset->addField('city', 'text', array(
                'label'    => Mage::helper('core')->__('City'),
                'title'    => Mage::helper('core')->__('City'),
                'name'     => 'city',
                'style'     => 'width:250px;',
                'required' => false,
            ));            
            
            $fieldset->addField('address', 'text', array(
                'label'    => Mage::helper('core')->__('Address'),
                'title'    => Mage::helper('core')->__('Address'),
                'name'     => 'address',
                'style'     => 'width:250px;',
                'required' => true,
            ));    
            
            $fieldset->addField('is_all_products', 'checkbox', array(
                'label'    => Mage::helper('core')->__('Use All Products from WebSite Store'),
                'title'    => Mage::helper('core')->__('Use All Products from WebSite Store'),
                'name'     => 'is_all_products',
                'checked'    => 'true',                
                'required' => false,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
            
            
            $fieldset->addField('phone', 'text', array(
                'label'    => Mage::helper('core')->__('Phone'),
                'title'    => Mage::helper('core')->__('Phone'),
                'name'     => 'phone',                
                'required' => false,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
            $fieldset->addField('fax', 'text', array(
                'label'    => Mage::helper('core')->__('Fax'),
                'title'    => Mage::helper('core')->__('Fax'),
                'name'     => 'fax',                
                'required' => false,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
            
            $fieldset->addField('picture_desc', 'textarea', array(
                'label'    => Mage::helper('core')->__('Description for Store\'s Photo'),
                'title'    => Mage::helper('core')->__('Description for Store\'s Photo'),
                'name'     => 'picture_desc',                
                'required' => false,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
            
			$fieldset->addField('relatlng', 'checkbox', array(
				'label'    => Mage::helper('core')->__('Update Lat/Lng after Save'),
				'title'    => Mage::helper('core')->__('Update Lat/Lng after Save'),
				'name'     => 'relatlng',                
				'required' => false,
				'checked'  => 'checked',
				'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
			
            $fieldset->addField('lng', 'text', array(
                'label'    => Mage::helper('core')->__('Latitude'),
                'title'    => Mage::helper('core')->__('Latitude'),
                'name'     => 'lng',                
                'required' => false,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
            
            $fieldset->addField('lat', 'text', array(
                'label'    => Mage::helper('core')->__('Longitude'),
                'title'    => Mage::helper('core')->__('Longitude'),
                'name'     => 'lat',                
                'required' => false,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
            
        $formData = array();        

        $form->addValues($formData);
        $form->setFieldNameSuffix('storeform');
        $this->setForm($form);
    }

}
