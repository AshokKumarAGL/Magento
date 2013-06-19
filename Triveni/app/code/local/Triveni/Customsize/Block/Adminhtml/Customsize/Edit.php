<?php
class Triveni_Customsize_Block_Adminhtml_Customsize_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'customsize';
        $this->_controller = 'adminhtml_customsize';

        $this->_updateButton('save', 'label', Mage::helper('customsize')->__('Save Custom Size'));
        $this->_updateButton('delete', 'label', Mage::helper('customsize')->__('Delete Custom Size'));
    }

    public function getHeaderText() {
        if( Mage::registry('customsize_data') && Mage::registry('customsize_data')->getId() ) {
            return Mage::helper('customsize')->__("Edit City");
        } else {
            return Mage::helper('customsize')->__('Add Custom Size');
        }
    }
}
