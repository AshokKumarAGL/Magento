<?php
class Idev_OneStepCheckout_IndexController extends Mage_Core_Controller_Front_Action {


    public function getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function successAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function indexAction() {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        $this->loadLayout();

        if(Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {

            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template'=>'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template'=>'onestepcheckout/customerbalance/payment/scripts.phtml'));

            $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array('template'=>'onestepcheckout/reward/payment/additional.phtml', 'before' => '-'));
            $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array('template'=>'onestepcheckout/reward/payment/scripts.phtml', 'after' => '-'));

            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts)
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts)
            ;
        }

        $this->renderLayout();
    }

    public function deleteallAction() {
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            $itemId = $item->getItemId();
            $cartHelper->getCart()->removeItem($itemId)->save();
        }
        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() ."checkout/cart/");
    }


    public function contactinfoAction() {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        $firstname = $this->getRequest()->getPost('firstname');
        $lastname = $this->getRequest()->getPost('lastname');
        $emailId = $this->getRequest()->getPost('emailId');
        $mobile = $this->getRequest()->getPost('mobile');

        if($this->getRequest()->getPost() && $firstname!='' && $lastname!='' && $mobile!='' && $emailId!='') {
            Mage::getModel('checkout/session')->setFirstname($firstname);
            Mage::getModel('checkout/session')->setLastname($lastname);
            Mage::getModel('checkout/session')->setEmailId($emailId);
            Mage::getModel('checkout/session')->setMobile($mobile);
            $this->_redirect('onestepcheckout/index/shipping');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function shippingAction() {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$this->_checksessioninfo()) {
            $this->_redirect('onestepcheckout/index/contactinfo');
        }

        if($this->getRequest()->getPost()) {
            $billing_data = $this->getRequest()->getPost('billing');
            $shipping_data = $this->getRequest()->getPost('shipping');
            $shipping_method = $this->getRequest()->getPost('shipping_method', false);
            $helper = Mage::helper('onestepcheckout/checkout');

            if(isset($billing_data['use_for_shipping']) && $billing_data['use_for_shipping'] == '1') {
                Mage::getModel('checkout/session')->setShowaddress('1');
                $shipping_address_source = $billing_data;
            }else {
                Mage::getModel('checkout/session')->setShowaddress('0');
                $shipping_address_source = $shipping_data;
            }
            if($shipping_address_source['firstname']!='' && $shipping_address_source['lastname']!='' && $shipping_address_source['city']!='' && $shipping_address_source['mobile']!='' && $shipping_address_source['postcode']!='') {

                $helper->load_exclude_data($billing_data);
                $helper->load_exclude_data($shipping_data);
                $billing_street = trim(implode("\n", $billing_data['street']));
                $shipping_street = trim(implode("\n", $shipping_address_source['street']));

                Mage::getModel('checkout/session')->setFirstname($shipping_address_source['firstname']); //reset session
                Mage::getModel('checkout/session')->setLastname($shipping_address_source['lastname']); //reset session
                Mage::getModel('checkout/session')->setMobile($shipping_address_source['mobile']); //reset session
                $this->getOnepage()->getQuote()->getShippingAddress()
                        ->setCountryId($shipping_address_source['country_id'])
                        ->setCity($shipping_address_source['city'])
                        ->setPostcode($shipping_address_source['postcode'])
                        ->setRegionId($shipping_address_source['region_id'])
                        ->setRegion($shipping_address_source['region'])
                        ->setFirstname($shipping_address_source['firstname'])
                        ->setLastname($shipping_address_source['lastname'])
                        ->setCompany($shipping_address_source['company'])
                        ->setFax($shipping_address_source['fax'])
                        ->setFax($shipping_address_source['mobile'])
                        ->setMobile($shipping_address_source['telephone'])
                        ->setStreet($shipping_street)
                        ->save()
                        ->setCollectShippingRates(true);

                $this->getOnepage()->getQuote()->getBillingAddress()
                        ->setCountryId($billing_data['country_id'])
                        ->setCity($billing_data['city'])
                        ->setPostcode($billing_data['postcode'])
                        ->setRegionId($billing_data['region_id'])
                        ->setRegion($billing_data['region'])
                        ->setFirstname($billing_data['firstname'])
                        ->setLastname($billing_data['lastname'])
                        //->setEmail($billing_data['email'])
                        ->setCompany($billing_data['company'])
                        ->setFax($billing_data['fax'])
                        ->setTelephone($billing_data['telephone'])
                        ->setMobile($billing_data['mobile'])
                        ->setStreet($billing_street)
                        ->save();

                if(isset($billing_data['email'])) {
                    $this->getOnepage()->getQuote()->getBillingAddress()->setEmail($billing_data['email'])->save();
                }
                $customerAddressId = (!empty($billing_data['address_id'])) ? $billing_data['address_id'] : false ;
                if(!$customerAddressId) {
                    $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
                }
                $result = $this->getOnepage()->saveBilling($billing_data, $customerAddressId);
                $this->_redirect('onestepcheckout/index/order');
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function orderAction() {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }

        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        if (!$this->_checksessioninfo()) {
            $this->_redirect('onestepcheckout/index/contactinfo');
        }

        $quoteData['securitycode'] = $this->getRequest()->getPost('securitycode', false);
        $quoteData['billing'] = $this->getOnepage()->getQuote()->getBillingAddress()->getData();
        $quoteData['shipping'] = $this->getOnepage()->getQuote()->getShippingAddress()->getData();
        $securitycode = $this->getRequest()->getPost('securitycode', false);
        $quoteData['payment'] = array('method'=>'dealerpayment');
        $quoteData['shipping_method'] = $this->_getPaymentMethod('dealerpayment');
//Mage::dispatchEvent('validatePincode', array('event'=>$this->getOnepage()));
//$quoteData['shipping_description'] = $quote->getShippingAddress()->getShippingDescription();
        $this->getRequest()->setPost($quoteData);
        if($securitycode && $securitycode != '') {
            if ($this->_isSecurityCodeRegistered($securitycode)) {
                Mage::getModel('checkout/session')->unsFirstname();
                Mage::getModel('checkout/session')->unsLastname();
                Mage::getModel('checkout/session')->unsEmailId();
                Mage::getModel('checkout/session')->unsMobile();
                Mage::getModel('checkout/session')->unsSameasbilling();
                Mage::getModel('checkout/session')->unsShowaddress();
            }
        }
        $this->loadLayout();
        $this->renderLayout();

    }

    private function _isSecurityCodeRegistered($securitycode) {
        $customer = Mage::getSingleton('customer/session');
        $collection = Mage::getModel('securitycode/customersecuritycode')->getCollection();
        $collection->addFieldToFilter('customer_id',array('eq'=>$customer->getId()));
        $collection->addFieldToFilter('checkout_securitycode',array('eq'=>$securitycode));
        return count($collection)? true:false;
    }

    private function _checksessioninfo() {
        $firstname = Mage::getModel('checkout/session')->getFirstname();
        $lastname = Mage::getModel('checkout/session')->getLastname();
        $emailId = Mage::getModel('checkout/session')->getEmailId();
        $mobile = Mage::getModel('checkout/session')->getMobile();
        if($firstname=='' && $lastname=='' && $mobile=='' && $emailId=='') {
            return false;
        }else {
            return true;
        }
    }

    private function _getPaymentMethod($payment_method) {
        $model = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        $total = $model->getSubtotalInclTax() + $model->getDiscountAmount();
        if ($total < Mage::getStoreConfig('bestylishIGShipping/minimumOrderPriceLimit')) {
            $flatrate = ($payment_method == 'checkmo') ? 'flatrate3_flatrate3':'flatrate_flatrate';
        } else {
            $flatrate = ($payment_method == 'checkmo') ? 'flatrate2_flatrate2':'freeshipping_freeshipping';
        }
        return $flatrate;
    }

    public function getShippingMethod() {
        $model = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        $total = $model->getSubtotalInclTax() + $model->getDiscountAmount();
        if ($total < Mage::getStoreConfig('bestylishIGShipping/minimumOrderPriceLimit')) {
            $flatrate = 'flatrate_flatrate';
        } else {
            $flatrate = 'freeshipping_freeshipping';
        }
        return $flatrate;
    }

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    public function updatePostAction() {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter($data['qty']);
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                $cart->updateItems($cartData)
                        ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
        }
        $this->_goOnestepBack();
    }

    protected function _goOnestepBack() {
        if ($returnUrl = $this->getRequest()->getParam('return_url')) {
            // clear layout messages in case of external url redirect
            if ($this->_isUrlInternal($returnUrl)) {
                $this->_getSession()->getMessages(true);
            }
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
                && !$this->getRequest()->getParam('in_cart')
                && $backUrl = $this->_getRefererUrl()) {

            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
    public function loginAction() {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function detailAction() {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        
        $var = $this->getRequest()->getPost();
        
        
        if((!empty($var)) && $var['shippingAddressHidden']!=''){
        	$shipping_method = array('shipping_method'=>$var['shipping_method']);
        	$customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
	        if ($customerAddressId){
				$address = Mage::getModel('customer/address')->load($customerAddressId);
				$addresBilling = array('firstname' =>$addresBilling['firstname'],
									   'middlename'=>$addresBilling['middlename'],
        						   	   'lastname'=>$addresBilling['lastname'],
        						       'street'=>$addresBilling['street'],
        						       'postcode'=>$addresBilling['postcode'],
        						       'telephone'=>$addresBilling['telephone'],
        						       'country_id'=>$addresBilling['country_id'],
        						       'region_id'=>$addresBilling['region'],
        						       'city'=>$addresBilling['city']);
			}
        	$id = $var['shippingAddressHidden'];
        	$customerShippingAddressId = Mage::getModel('customer/address')->load($id);
        	$addresShipping = array('firstname' =>$customerShippingAddressId['firstname'],
									   'middlename'=>$customerShippingAddressId['middlename'],
        						   	   'lastname'=>$customerShippingAddressId['lastname'],
        						       'street'=>$customerShippingAddressId['street'],
        						       'postcode'=>$customerShippingAddressId['postcode'],
        						       'telephone'=>$customerShippingAddressId['telephone'],
        						       'country_id'=>$customerShippingAddressId['country_id'],
        						       'region_id'=>$customerShippingAddressId['region'],
        						       'city'=>$customerShippingAddressId['city']);
        	
        	
        	Mage::getSingleton('core/session')->unsShippingAddressId();
        	Mage::getSingleton('core/session')->unsShippingAddressId();
        
        	Mage::getSingleton('core/session')->unsDefaultBSId();
            Mage::getSingleton('core/session')->setSessionAddFlagVariable('true');
            Mage::getSingleton('core/session')->setBilling($addresBilling);
            Mage::getSingleton('core/session')->setShipping($addresShipping);
            Mage::getSingleton('core/session')->setShippingMethod($shipping_method);
            
            $this->_redirect('onestepcheckout/index/payments');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function addressAction() {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('checkout/cart');
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('core/session')->unsShippingAddressId();
        Mage::getSingleton('core/session')->unsShippingAddressId();
        if($this->getRequest()->getPost() ) {
            if($this->getRequest()->getPost('shipping_address_id')!='' && $this->getRequest()->getPost('billing_address_id')!='') {
                Mage::getSingleton('core/session')->setDefaultBSId($this->getRequest()->getPost('shipping_address_id'));
                Mage::getSingleton('core/session')->unsSessionAddFlagVariable();
                Mage::getSingleton('core/session')->setShippingAddressId($this->getRequest()->getPost('shipping_address_id'));
                Mage::getSingleton('core/session')->setBilling($this->getRequest()->getPost('billing'));
            } else {
                Mage::getSingleton('core/session')->unsDefaultBSId();
                Mage::getSingleton('core/session')->setSessionAddFlagVariable('true');
                Mage::getSingleton('core/session')->setBilling($this->getRequest()->getPost('billing'));
                Mage::getSingleton('core/session')->setShipping($this->getRequest()->getPost('shipping'));
            }
            $this->_redirect('onestepcheckout/index/payments');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function paymentsAction() {
        //die('this is testing');
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {die('1');
            $this->_redirect('checkout/cart');
            return;
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {die('2');
            $this->_redirect('checkout/cart');
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {die('3');
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {die('4');
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
}
