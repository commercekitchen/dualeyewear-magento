<?php
/**
 * @author Ocodewire (ocodewire.com)
 * @copyright  Copyright (c)  ocodewire
 * @version : 1.0.2
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Browsewire_Cmbannerslider_Block_Cmbannerslider extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCmbannerslider()     
     { 
        if (!$this->hasData('cmbannerslider')) {
            $this->setData('cmbannerslider', Mage::registry('cmbannerslider'));
        }
        return $this->getData('cmbannerslider');       
    }
}
