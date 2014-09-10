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

class Belvg_Stores_Block_Adminhtml_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('statusGrid');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getModel('stores/state')->getCollection();                
          $this->setCollection($collection);
                
        return parent::_prepareCollection();
    }


    public function setFilterValues($data)
    {
        return $this->_setFilterValues($data);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity', array(
            'header'    => $this->__('Store #'),
            'align'     =>'left',
            'width'     => '100px',
            'truncate'      => 50,
            'index'     => 'id'
        ));
        $this->addColumn('title', array(
            'header'    => $this->__('Store Name'),
            'align'     =>'left',            
            'truncate'      => 100,
            'index'     => 'title'
        ));
        $this->addColumn('country', array(
            'header'    => $this->__('Country'),
            'align'     =>'left',            
            'truncate'      => 100,
            'index'     => 'country'
        ));
        $this->addColumn('state', array(
            'header'    => $this->__('State'),
            'align'     =>'left',            
            'truncate'      => 100,
            'index'     => 'state'
        ));
        $this->addColumn('city', array(
            'header'    => $this->__('City'),
            'align'     =>'left',            
            'truncate'      => 100,
            'index'     => 'city'
        ));
        $this->addColumn('address', array(
            'header'    => $this->__('Address'),
            'align'     =>'left',            
            'truncate'      => 100,
            'index'     => 'address'
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  $this->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => $this->__('Edit'),
                        'url'       => array('base'=> '*/*/Edit'),
                        'field'     => 'id'
                    )                
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        return parent::_prepareColumns();
    }

    
    /*
    protected function _toHtml()
    {
        return Mage::app()->getLayout()->createBlock('adminhtml/store_switcher')->toHtml().parent::_toHtml();
    }    
	*/
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}

