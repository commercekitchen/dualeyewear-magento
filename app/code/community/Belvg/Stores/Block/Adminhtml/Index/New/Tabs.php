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

class Belvg_Stores_Block_Adminhtml_Index_New_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {        
        parent::__construct();
        $this->setId('design_tabs');
        $this->setDestElementId('design_edit_form');
        $this->setTitle(Mage::helper('core')->__('Add Store'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('core')->__('General'),
            'content'   => $this->getLayout()->createBlock('stores/adminhtml_index_new_tab_general')->toHtml(),
        ));
        
        //Add Serializer for Product Grid and Creating Product Tab
		
		$products_grid = $this->getLayout()->createBlock('stores/adminhtml_index_new_tab_products', 'stores_edit_tab_products');
        $grid_serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $grid_serializer->initSerializerBlock('stores_edit_tab_products', 'getRelatedProducts', 'stores_assigned_products', 'stores_assigned_products');


        $this->addTab('form_products', array(
            'label'     => Mage::helper('stores')->__('Associated Products'),
            'content' => $products_grid->toHtml() . $grid_serializer->toHtml(),
        ));
        
        $this->addTab('media', array(
            'label'     => Mage::helper('core')->__('Media'),
            'content'   => $this->getLayout()->createBlock('stores/adminhtml_index_new_tab_media')->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}
