<?php
class Triveni_Customsize_Block_Customsize extends Mage_Core_Block_Template
{
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }
    
    public function getCity() {
        if (!$this->hasData('customsize')) {
            $this->setData('customsize', Mage::registry('customsize'));
        }
        return $this->getData('customsize');
    }
}
?>
