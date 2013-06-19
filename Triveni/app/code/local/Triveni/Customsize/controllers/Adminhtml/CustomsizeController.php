<?php
class Triveni_Customsize_Adminhtml_CustomsizeController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('customsize/customsize')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Customsize Manager'), Mage::helper('adminhtml')->__('Customsize Manager'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('customsize/customsize')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                    $model->setData($data);
            }

            Mage::register('customsize_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('customsize/customsize');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Customsize Manager'), Mage::helper('adminhtml')->__('Customsize Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('customsize/adminhtml_customsize_edit'))
                    ->_addLeft($this->getLayout()->createBlock('customsize/adminhtml_customsize_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customsize')->__('Customsize does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
        	
        	$model = Mage::getModel('customsize/customsize');
            $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
            if($this->getRequest()->getParam('id')!=''){
                $data['modified_at'] = now();
                $data['modified_by'] = $username;
            } else {
                $data['created_at'] = now();
            }
            
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
                
            try {
            	$model->save();
            
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customsize')->__('Customsize is successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customsize')->__('Unable to find customsize to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                    $model = Mage::getModel('customsize/customsize');
                    $model->setId($this->getRequest()->getParam('id'))
                            ->delete();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Customsize is successfully deleted'));
                    $this->_redirect('*/*/');
            } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
}
