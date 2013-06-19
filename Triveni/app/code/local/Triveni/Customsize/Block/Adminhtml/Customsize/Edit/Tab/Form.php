<?php
class Triveni_Customsize_Block_Adminhtml_Customsize_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('customsize_form', array('legend'=>Mage::helper('customsize')->__('Customsize Information')));
        
        $fieldset->addField('full_name', 'text', array(
          'label'     => Mage::helper('customsize')->__('Full Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'full_name',
        ));
        
        $fieldset->addField('email_id', 'text', array(
          'label'     => Mage::helper('customsize')->__('Email Id'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email_id',
        ));
       
        $fieldset->addField('measurement_in', 'radios', array(
          'label'     => Mage::helper('customsize')->__('Measurement in'),
          'name'      => 'measurement_in',
          'value'     => 'Inches',
          'values'    => array(array('value'=>'Inches','label'=>'Inches'), array('value'=>'Centemeters','label'=>'Centemeters')),
        ));
        
        $fieldset->addField('measurement_used', 'radios', array(
          'label'     => Mage::helper('customsize')->__('Measurement used'),
          'name'      => 'measurement_used',
          'onclick'   => "",
          'onchange'  => "",
          'value'     => 'Body-You',
          'values'    => array(array('value'=>'Body-You','label'=>'<b>Body-You</b> want to submit body measurement and our tailor will give suitable loosening as per tailoring principles.<br />'),
        					   array('value'=>'Garment-You','label'=>'<b>Garment-You</b> want to submit ready garment measurement and you do not want loosening on submitted numbers.')
        					  ),
        ));
        
        $fieldset->addField('around_bust', 'select', array(
          'label'     => Mage::helper('customsize')->__('Around Bust'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'around_bust',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('around_above_waist', 'select', array(
          'label'     => Mage::helper('customsize')->__('Around Above waist'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'around_above_waist',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('shoulder', 'select', array(
          'label'     => Mage::helper('customsize')->__('Shoulder'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'shoulder',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('sleeve_style', 'select', array(
          'label'     => Mage::helper('customsize')->__('Sleeve Style'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'sleeve_style',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('sleeve_length', 'select', array(
          'label'     => Mage::helper('customsize')->__('Sleeve Length'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'sleeve_length',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('around_arm_hole', 'select', array(
          'label'     => Mage::helper('customsize')->__('Around Arm Hole'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'around_arm_hole',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('around_arm', 'select', array(
          'label'     => Mage::helper('customsize')->__('Around Arm'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'around_arm',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('front_neck_style', 'select', array(
          'label'     => Mage::helper('customsize')->__('Front Neck Style'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'front_neck_style',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('front_neck_depth', 'select', array(
          'label'     => Mage::helper('customsize')->__('Front Neck Depth'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'front_neck_depth',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('back_neck_style', 'select', array(
          'label'     => Mage::helper('customsize')->__('Back Neck Style'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'back_neck_style',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('back_neck_depth', 'select', array(
          'label'     => Mage::helper('customsize')->__('Back Neck Depth'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'back_neck_depth',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('blouse_length', 'select', array(
          'label'     => Mage::helper('customsize')->__('Blouse Length'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'blouse_length',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('closing_side', 'select', array(
          'label'     => Mage::helper('customsize')->__('Closing Side'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'closing_side',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('closing_with', 'select', array(
          'label'     => Mage::helper('customsize')->__('Closing With'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'closing_with',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('lining', 'select', array(
          'label'     => Mage::helper('customsize')->__('Lining'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'lining',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('around_waist', 'select', array(
          'label'     => Mage::helper('customsize')->__('Around Waist'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'around_waist',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('around_hips', 'select', array(
          'label'     => Mage::helper('customsize')->__('Around Hips'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'around_hips',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        $fieldset->addField('petticoat_length', 'select', array(
          'label'     => Mage::helper('customsize')->__('Petticoat Length'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'petticoat_length',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('your_height', 'select', array(
          'label'     => Mage::helper('customsize')->__('Your Height'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'your_height',
          'values'    => Mage::helper('customsize')->getSizeList(),
        ));
        
        $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('customsize')->__('Status'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'status',
          'values'	  => array('active'=>'Active','inactive'=>'Inactive'),
        ));
        
         $fieldset->addField('instructions', 'textarea', array(
          'label'     => Mage::helper('customsize')->__('Instructions'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'instructions',
        ));
        
        if ( Mage::getSingleton('adminhtml/session')->getLabelmagicData() ) {
        $form->setValues(Mage::getSingleton('adminhtml/session')->getLabelmagicData());
        Mage::getSingleton('adminhtml/session')->getLabelmagicData(null);
        } elseif ( Mage::registry('customsize_data') ) {
            $form->setValues(Mage::registry('customsize_data')->getData());
        }
        return parent::_prepareForm();
    }
}