<?php
class Browsewire_Cmbannerslider_Model_Mysql4_Cmbannerslider_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmbannerslider/cmbannerslider');
    }
}
