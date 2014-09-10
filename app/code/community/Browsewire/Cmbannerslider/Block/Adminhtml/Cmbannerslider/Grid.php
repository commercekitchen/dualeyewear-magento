<?php

class Browsewire_Cmbannerslider_Block_Adminhtml_Cmbannerslider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('cmbannersliderGrid');
      $this->setDefaultSort('cmbannerslider_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('cmbannerslider/cmbannerslider')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  { 
      $this->addColumn('cmbannerslider_id', array(
          'header'    => Mage::helper('cmbannerslider')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'cmbannerslider_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('cmbannerslider')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
	$this->addColumn('filename', array(

            'header'=>Mage::helper('cmbannerslider')->__('Image'),
            'index'=>'filename',
            'renderer'  => 'Browsewire_Cmbannerslider_Block_Adminhtml_Grid_Renderer_Image',
            'align' => 'left',
        )); 


      $this->addColumn('status', array(
          'header'    => Mage::helper('cmbannerslider')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('cmbannerslider')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(

                    array(
                        'caption'   => Mage::helper('cmbannerslider')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cmbannerslider_id');
        $this->getMassactionBlock()->setFormFieldName('cmbannerslider');
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('cmbannerslider')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('cmbannerslider')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('cmbannerslider/status')->getOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('cmbannerslider')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('cmbannerslider')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)

  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
