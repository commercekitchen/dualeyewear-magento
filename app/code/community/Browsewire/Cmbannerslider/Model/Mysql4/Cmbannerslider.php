<?php

class Browsewire_Cmbannerslider_Model_Mysql4_Cmbannerslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cmbannerslider_id refers to the key field in your database table.
        $this->_init('cmbannerslider/cmbannerslider', 'cmbannerslider_id');
    }
}
