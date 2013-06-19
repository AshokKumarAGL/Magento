<?php
class Triveni_Customsize_Block_Adminhtml_Customsize extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_controller = 'adminhtml_customsize';
        $this->_blockGroup = 'customsize';
        $this->_headerText = Mage::helper('customsize')->__('Custom Size Manager');
        $this->_addButtonLabel = Mage::helper('customsize')->__('Add Custom Size');
        parent::__construct();
    }
}
