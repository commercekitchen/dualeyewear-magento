<?php

class Browsewire_Cmbannerslider_Model_Cmbannerslider extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmbannerslider/cmbannerslider');
    }
}
