<?php
class Triveni_Customsize_Block_Adminhtml_Customsize_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct() {
      parent::__construct();
      $this->setId('customsize_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('customsize')->__('Custom Size'));
    }

    protected function _beforeToHtml() {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('customsize')->__('Custom Size'),
          'title'     => Mage::helper('customsize')->__('Custom Size'),
          'content'   => $this->getLayout()->createBlock('customsize/adminhtml_customsize_edit_tab_form')->toHtml(),
      ));
      return parent::_beforeToHtml();
    }
}
