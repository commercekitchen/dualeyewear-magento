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

class AW_Afptc_Block_Adminhtml_Rules_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awAfptcGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
   
    protected function _prepareColumns()
    {        
        $selected = array();
        if ($this->getElement()) {
            $selected = array($this->getElement()->getFormdata()->getProductId());
        } else if ($this->getCheckedValues()) {
            $selected = $this->getCheckedValues();
        }
        
        $this->addColumn('chooser', array(
            'type' => 'radio',
            'index' => 'entity_id',
            'filter_index' => 'entity_id',
            'class' => 'radio',
            'renderer' => 'awafptc/adminhtml_rules_edit_renderer_radio',
            'html_name' => 'product_id',
            'filter_condition_callback' => array($this, 'filterChooser'),
            'align' => 'center',    
            'values' => $selected,
            'sortable' => false         
        ));
        
        $this->addColumn('entity_id', array(
                'header'    => $this->__('ID'),
                'index'     => 'entity_id',
                'type'      => 'number'
        ));
     
        $this->addColumn('product_name', array(
                'header'    => $this->__('Name'),
                'index'     => 'name',
        ));

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', array(
                    'header'    => $this->__('Name in Store'),
                    'index'     => 'custom_name'
            ));
        }

        $this->addColumn('sku', array(
                'header'    => $this->__('SKU'),
                'width'     => '80px',
                'index'     => 'sku'
        ));

        $this->addColumn('price', array(
                'header'    => $this->__('Price'),
                'type'      => 'currency',
                'index'     => 'price'
        ));

        $this->addColumn('qty', array(
                'header'    => $this->__('Qty'),
                'width'     => '130px',
                'type'      => 'number',
                'index'     => 'qty'
        ));

        $this->addColumn('product_status', array(
                'header'    => $this->__('Status'),
                'width'     => '90px',
                'index'     => 'status',
                'type'      => 'options',
                'source'    => 'catalog/product_status',
                'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> $this->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
    }
    
    public function setCollection($collection)
    {     
        $collection->addAttributeToSelect('links_purchased_separately');
       
        $collection->joinAttribute(
            'links_purchased_separately',
            'catalog_product/links_purchased_separately',
            'entity_id',
            null,
            'left',
            0
        );
       
        $collection->getSelect()
             ->where('e.type_id IN("simple","downloadable","virtual")')
             ->where("`e`.`has_options` = 0
                 OR (`e`.`type_id` = 'downloadable' AND `e`.`required_options` = 0
                 AND `{$this->_getLinksTableName($collection)}`.`value` = 0)");

        $_visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
        );
        $collection->addAttributeToFilter('visibility', $_visibility);
        $this->_collection = $collection;
    }
    
    protected function _getLinksTableName($collection)
    {
        $columns = $collection->getSelect()->getPart(Zend_Db_Select::COLUMNS);

        foreach ($columns as $column) {
            if (array_pop($column) == 'links_purchased_separately') {
                return array_shift($column);
            }
        }

        return 'at_links_purchased_separately';
    }
    
    protected function filterChooser($collection, $column)
    {
        $val = $column->getFilter()->getValue();

        if ($val === '') {
            return '';
        }
        $request = Mage::app()->getRequest();        
        $checked = $request->getParam('checkedValues');
        if (!$checked) {
            $id = $request->getParam('id');
            if (!$id && (int) $val) {
               return $collection->getSelect()->where("e.entity_id is null");
            }
            $rule = Mage::getModel('awafptc/rule')->load($id);
            if (!$rule->getProductId() && (int) $val) {
               return $collection->getSelect()->where("e.entity_id is null");
            }
            if (!$checked = $rule->getProductId()) {
                return '';
            }
        }

        if (!(int) $val) {
            $collection->getSelect()->where("e.entity_id != {$checked}");
        } else {
            $collection->getSelect()->where("e.entity_id = {$checked}");
        }
        return '';
    }
   
    public function getRowUrl($row)
    {
        return null;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    protected function _prepareMassaction()
    {      
        return $this;
    }
}