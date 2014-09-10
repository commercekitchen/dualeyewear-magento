<?php

/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_Adminhtml_Block_Downloads_Files_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_tree = array();

    public function __construct()
    {
        parent::__construct();

        $this->setId('downloadsProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

        if ($this->getFileId() && $this->_getSelectedProducts()) {
            $this->setDefaultFilter(array('massaction' => 1));
        }
    }

    public function getFileId()
    {
        return (int)Mage::app()->getFrontController()->getRequest()->getParam('id');
    }

    protected function _getHelper()
    {
        return Mage::helper('downloads');
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getResourceModel('downloads/catalog_product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');


        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $collection->getSelect()
            ->joinLeft(array('category' => $collection->getTable('catalog/category_product')),
            'e.entity_id = category.product_id',
            array('cat_ids' => 'GROUP_CONCAT(category.category_id)'))
            ->group('e.entity_id');

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = $this->_getHelper();

        $this->addColumn('name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('type', array(
            'header' => $helper->__('Type'),
            'width' => 100,
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header' => $helper->__('Attrib. Set Name'),
            'width' => 100,
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header' => $helper->__('SKU'),
            'width' => 100,
            'index' => 'sku',
        ));

        $this->addColumn('price', array(
            'header' => $helper->__('Price'),
            'index' => 'price',
            'type' => 'currency',
            'currency_code'
            => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header' => $helper->__('Qty'),
            'width' => 100,
            'index' => 'qty',
            'type' => 'number',
            'validate_class'
            => 'validate-number',
        ));

        $this->addColumn('category', array(
            'header' => $helper->__('Category'),
            'index' => 'cats',
            'type' => 'options',
            'validate_class' => 'validate-number',
            'options' => Mage::getSingleton('downloads/resource_categories')->getOptionArray(),
            'renderer' => 'MageWorx_Adminhtml_Block_Downloads_Files_Grid_Renderer_Prodcat',
            'filter_condition_callback' => array($this, 'category_filter')
        ));

        $this->addColumn('visibility', array(
            'header' => $helper->__('Visibility'),
            'width' => 70,
            'index' => 'visibility',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'width' => 70,
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $massBlock = $this->getMassactionBlock();
        $massBlock->setTemplate('downloads/widget-grid-massaction.phtml');

        $massBlock->setFormFieldName('in_products');
        $massBlock->addItem(null, array());

        $productIds = $this->_getSelectedProducts();
        if ($productIds) {
            $massBlock->getRequest()->setPost($massBlock->getFormFieldNameInternal(), $productIds);
        }

        return $this;
    }

    private function _getSelectedProducts()
    {
        $productIds = '';
        $session = Mage::getSingleton('adminhtml/session');
        if ($data = $session->getData('downloads_data')) {
            if (isset($data['post_products'])) {
                $productIds = $data['post_products'];
            }
            $session->setData('downloads_data', null);
        } elseif (Mage::registry('downloads_data')) {
            $productIds = Mage::registry('downloads_data')->getData('in_products');
        }
        if (is_array($productIds) && $productIds) {
            $productIds = implode(',', $productIds);
        }

        return $productIds;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    public function category_filter($collection, $column)
    {
        $cond = $column->getFilter()->getCondition();
        if (empty($cond['eq'])) {
            return true;
        }

        $where = 'category.category_id = ' . $cond['eq'];
        $collection->getSelect()->where($where);
    }
}
