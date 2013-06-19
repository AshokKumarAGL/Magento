<?php
class Triveni_Customsize_Model_Mysql4_Customsize_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('customsize/customsize');
    }
}
