<?php
include_once('Mage/Customer/controllers/AddressController.php');

class Triveni_Customer_AddressController extends Mage_Customer_AddressController
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Customer addresses list
     */
    public function indexAction()
    {
        if (count($this->_getSession()->getCustomer()->getAddresses())) {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            $block = $this->getLayout()->getBlock('address_book');
            if ($block) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        } else {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/new'));
        }
    }

    public function editAction()
    {
        $this->_forward('form');
    }

    public function newAction()
    {
        $this->_forward('form');
    }

    /**
     * Address book form
     */
    public function formAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('customer/address');
        }
        $this->renderLayout();
    }

    public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $customer = $this->_getSession()->getCustomer();
            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->setId($existsAddress->getId());
                }
            }

            $errors = array();

            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntity($address);
            $addressData    = $addressForm->extractData($this->getRequest());
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                $errors = $addressErrors;
            }	
            
            try {
                $addressForm->compactData($addressData);
                $address->setCustomerId($customer->getId())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));

                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }

                if (count($errors) === 0) {
                    $address->save();
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    foreach ($errors as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }

        return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => $address->getId())));
    }
    
    //--------------------------------Code added by Ashok-------------------------------------
	public function savenewaddressAction()
    {
    	$customer = $this->_getSession()->getCustomer();
    	// Load parameters
    	$shipping_firstname = $this->getRequest()->getParam('shipping_firstname');
    	$shipping_ext = $this->getRequest()->getParam('shipping_ext');
    	$shipping_telephone = $this->getRequest()->getParam('shipping_telephone');
    	$shipping_street = $this->getRequest()->getParam('shipping_street');
    	$shipping_city = $this->getRequest()->getParam('shipping_city');
    	$shipping_postcode = $this->getRequest()->getParam('shipping_postcode');
    	$shipping_country_id = $this->getRequest()->getParam('shipping_country_id');
    	$shipping_region = $this->getRequest()->getParam('shipping_region');
    	
    	try {
	    	// call address model.
	    	$firstname = strstr(' ',$shipping_firstname, true);
	    	$lastname = strstr(' ',$shipping_firstname);
	    	
	    	$addArr = array(
	    					'firstname' => $firstname,
						    'lastname' => $lastname,
						    'company' => '',
						    'street' => Array
						        (
						            '0' => $shipping_street
						        ),
						
						    'city' => $shipping_city,
						    'country_id' => $shipping_country_id,
						    'region' => NoidaUP,
						    'region_id' => $shipping_region,
						    'postcode' => $shipping_postcode,
						    'telephone' => $shipping_telephone,
						    'fax' => '',
						    'vat_id' => '',
	    					);
	    	
	    	$address  = Mage::getModel('customer/address')
	    				->setCustomerId($customer->getId())
	    				->setData($addArr)
	    				->setIsDefaultBilling(false)
	                    ->setIsDefaultShipping(false)
	                    ->setId(null);
	        $address->save();
	        $this->_getSession()->addSuccess($this->__('The address has been saved.'));
			$this->_redirectSuccess(Mage::getUrl('*/*/detail', array('_secure'=>true)));
            return;
                    
    	}catch(Exception $e){
    		$this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
    	}
    	
    	
    }
    //---------------------------------------Exit---------------------------------------------
    
    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_getSession()->addError($this->__('The address does not belong to this customer.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->_getSession()->addSuccess($this->__('The address has been deleted.'));
            } catch (Exception $e){
                $this->_getSession()->addException($e, $this->__('An error occurred while deleting the address.'));
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
    }
}
