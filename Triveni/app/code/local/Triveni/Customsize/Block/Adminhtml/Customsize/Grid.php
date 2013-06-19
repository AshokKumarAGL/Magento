<?php
class Triveni_Customsize_Block_Adminhtml_Customsize_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('customsizeGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('customsize/customsize')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
          'header'    => Mage::helper('customsize')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
        ));

        $this->addColumn('full_name', array(
          'header'    => Mage::helper('customsize')->__('Full Name'),
          'align'     =>'left',
          'index'     => 'full_name',
        ));
		
		$this->addColumn('email_id', array(
          'header'    => Mage::helper('customsize')->__('Email Id'),
          'align'     =>'left',
          'index'     => 'email_id',
        ));
		
        $this->addColumn('status', array(
          'header'    => Mage::helper('customsize')->__('Status'),
          'align'     =>'left',
          'index'     => 'status',
          'type' 	  => 'options',
          'options'   => array('active'=>'Active', 'inactive'=>'Inactive'),
        ));
		
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customsize')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customsize')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
