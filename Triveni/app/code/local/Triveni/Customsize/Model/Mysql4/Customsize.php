<?php
class Triveni_Customsize_Model_Mysql4_Customsize extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('customsize/customsize', 'id');
    }
}
