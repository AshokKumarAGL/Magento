<?php
class Triveni_Customsize_Model_Customsize extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('customsize/customsize');
    }
}
