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

class AW_Afptc_Block_Adminhtml_Rules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('AwAfptcGrid');
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('awafptc/rule')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        $this->addAdditionalFields();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => $this->__('ID'),
            'align' => 'right',           
            'index' => 'rule_id'           
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Rule Name'),
            'index' => 'name'            
        ));
        
        $this->addColumn('status', array(
            'header' => $this->__('Status'),
            'index' => 'status',
            'type' => 'options',           
            'options' => array(        
               1 => $this->__('Enabled'),
               0 => $this->__('Disabled')
            ),
        ));

        $this->addColumn('start_date', array(
            'header' => $this->__('Start Date and Time'),
            'index' => 'start_date',
            'width' => '170px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' ---- '
        ));

        $this->addColumn('end_date', array(
            'header' => $this->__('End Date and Time'),
            'index' => 'end_date',
            'width' => '170px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' ---- '
        ));
 
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header' => $this->__('Store View'),
                'index' => 'store_ids',
                'type' => 'store',
                'width' => '350px',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter_condition_callback' => array($this, 'filterStore'),
            ));
        }
        
         $this->addColumn('priority', array(
            'header' => $this->__('Priority'),
            'index' => 'priority',
            'type' => 'number'       
        ));
 
        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '150px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => $this->__('Delete'),
                    'url' => array(
                        'base' => '*/*/delete'
                    ),
                    'field' => 'id',
                    'confirm' => $this->__('Are you sure?')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true
        ));
        return parent::_prepareColumns();
    }

    protected function filterStore($collection, $column)
    {
        if (!$val = $column->getFilter()->getValue()) {
            return;
        }

        $collection->getSelect()
                ->where("FIND_IN_SET('$val', {$column->getIndex()}) OR FIND_IN_SET('0', {$column->getIndex()})");
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function addAdditionalFields()
    {
        foreach ($this->getCollection() as $item) {
            $item->setData('store_ids', explode(',', $item->getData('store_ids')));
        }
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rule_id');
        $this->getMassactionBlock()->setFormFieldName('rules');

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> $this->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'status',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => $this->__('Status'),
                    'values' => array(
                        $this->__('Disabled'),
                        $this->__('Enabled')
                    ),
                )
            )
        ));

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => $this->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Are you sure?')
        ));
    }
}