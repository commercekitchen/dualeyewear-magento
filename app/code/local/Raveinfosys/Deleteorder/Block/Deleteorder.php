<?php
class Raveinfosys_Deleteorder_Block_Deleteorder extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getDeleteorder()     
     { 
        if (!$this->hasData('deleteorder')) {
            $this->setData('deleteorder', Mage::registry('deleteorder'));
        }
        return $this->getData('deleteorder');
        
    }
}