<?php

class Raveinfosys_Deleteorder_Model_Mysql4_Deleteorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the deleteorder_id refers to the key field in your database table.
        $this->_init('deleteorder/deleteorder', 'deleteorder_id');
    }
}