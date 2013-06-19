<?php
class Idev_OneStepCheckout_Model_Observer extends Mage_Core_Model_Abstract {
    public function initialize_checkout($observer) {
        $helper = Mage::helper('onestepcheckout/checkout');
    }

    public function setDetails($event) {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() ."checkout/cart/");
        }
        $controller = $event->getEvent()->getControllerAction();
        $postData   = $controller->getRequest()->getPost();
        $paymentMethod = $postData['payment']['method'];
         if(Mage::getSingleton('core/session')->getShippingAddressId()) {
//            $controller->getRequest()->setPost('billing_address_id',Mage::getSingleton('core/session')->getBillingAddressId());
//            $controller->getRequest()->setPost('shipping_address_id',Mage::getSingleton('core/session')->getShippingAddressId());
//            $controller->getRequest()->setPost('billing',Mage::getSingleton('core/session')->getBilling());
//            $controller->getRequest()->setPost('shipping',Mage::getSingleton('core/session')->getShipping());
                $controller->getRequest()->setPost('billing_address_id',Mage::getSingleton('core/session')->getShippingAddressId());
                $controller->getRequest()->setPost('shipping_address_id',Mage::getSingleton('core/session')->getShippingAddressId());
                $controller->getRequest()->setPost('billing',Mage::getSingleton('core/session')->getBilling());

        } else {
            $controller->getRequest()->setPost('billing',Mage::getSingleton('core/session')->getBilling());
            $controller->getRequest()->setPost('shipping',Mage::getSingleton('core/session')->getShipping());
        }
       
    }
    public function setextradel(){
        if($date == $paidDate) {
            if($city == $paidCity) {
                if($delivery=='express') {
                    $setMethod = $flatrate1;
                } else if ($delivery=='midlnite') {
                        $setMethod = $flatrate2;
                }
            } else {

            }
        } else {

        }
    }
    public function setPaymentmethod($event) {
        $citrusBanks = array("ISS002","ISS006","ISS010","ISS003","ISS001","ISS011","ISS008","ISS007","ISS004","ISS014","ISS015","ISS016","ISS017");
        if(Mage::getModel('checkout/session')->getNetBankingCards()) {
            if(in_array(Mage::getModel('checkout/session')->getNetBankingCards(), $citrusBanks) ) {
                Mage::getModel('checkout/session')->setNetBankingOptions(Mage::getModel('checkout/session')->getNetBankingCards());
                Mage::getModel('checkout/session')->setBankOptions('citrusNetBanking');
                Mage::helper('onestepcheckout/checkout')->savePayment(array('method'=>'citrus_standard'));
            }
        }
    }
    
   public function getPaymentMethod($event) {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() ."checkout/cart/");
        }
        $model = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        $total = $model->getSubtotalInclTax() + $model->getDiscountAmount();
        $payment_method = Mage::getSingleton('checkout/type_onepage')->getQuote()->getPayment()->getMethod();
        if ($total < Mage::getStoreConfig('bestylishIGShipping/minimumOrderPriceLimit')) {
            $shipping_method = ($payment_method == 'checkmo') ? 'flatrate3_flatrate3':'flatrate_flatrate';
        } else {
            $shipping_method = 'freeshipping_freeshipping';
        }
       
        $shipping_method = 'flatrate_flatrate';
        $controller = $event->getEvent()->getControllerAction();
        $controller->getRequest()->setPost('shipping_method', $shipping_method);
        Mage::getSingleton('checkout/type_onepage')->saveShippingMethod($shipping_method);
        Mage::helper('onestepcheckout/checkout')->saveShippingMethod($shipping_method);
    }

    public function setCustomerInfo() {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() ."checkout/cart/");
        }
        $customerSessId = Mage::getSingleton('customer/session')->getId();
        $customer = Mage::getModel('customer/customer')->load($customerSessId);        
        if(!(count(Mage::getSingleton('customer/session')->getCustomer()->getAddresses())>0)) {
            $shipping = Mage::getSingleton('core/session')->getShipping();
            $customer->setFirstname($shipping['firstname']);
            $customer->setLastname($shipping['lastname']);
            $customer->setPrefix($shipping['prefix']);
            $customer->save();
        }
    }

    public function showCodMsg($payment_method) {
        $model = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        $total = $model->getSubtotalInclTax() + $model->getDiscountAmount();
        if ($total < Mage::getStoreConfig('bestylishIGShipping/minimumOrderPriceLimit'))
            return true;
        else
            return false;
    }

    public function saveShipToBillInfo() {
        if(!Mage::getSingleton('core/session')->getShippingAddressId()) {
            $customer       = Mage::getSingleton('customer/session');
            $shipAddress    = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
            $shipping       = Mage::getSingleton('core/session')->getShipping();
            $custom_address = array (
                                'prefix'    => $shipAddress['prefix'],
                                'firstname' => $shipAddress['firstname'],
                                'lastname'  => $shipAddress['lastname'],
                                'street'    => $shipAddress['street'],
                                'city'      => $shipAddress['city'],
                                'region_id' => $shipAddress['region_id'],
                                'region'    => $shipAddress['region'],
                                'postcode'  => $shipAddress['postcode'],
                                'country_id'=> 'IN',
                                'telephone' => $shipAddress['telephone'],
                                'mobile'    => $shipAddress['mobile'],
                                );
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customAddress = Mage::getModel('customer/address');
            if(count(Mage::getSingleton('customer/session')->getCustomer()->getAddresses())>0) {
                $defaultShip = 0;
            } else {
                $defaultShip = 1;
            }
            $customAddress->setData($custom_address)
                        ->setCustomerId($customer->getId())
                        ->setIsDefaultShipping($defaultShip);
            try {
                $customAddress->save();
            }
            catch (Exception $ex) {
            }
        }
    }
    //Add by Gajendra
    public function saveOrderDetailsAfter($evt){
        $order = $evt->getOrder();
            $ncollection = Mage::getModel('deliverydetails/orderdetails')->getCollection();
            $ncollection->addFieldToFilter('increment_id',array('eq'=>$order->getIncrementId()));
            if ($ncollection->count()) {
                foreach ($ncollection as $fItem) {
                    $fItem->delete();
                }
            }
            $collection = Mage::getModel('deliverydetails/quotedetails')->getCollection();
            $collection->addFieldToFilter('quote_id',array('eq'=>$order->getQuoteId()));
            if ($collection->count()) {
                $data = $collection->getFirstItem()->getData();
                $ordermodel = Mage::getModel('deliverydetails/orderdetails');
                $ordermodel->setIncrementId($order->getIncrementId());
                $ordermodel->setQuoteId($data['quote_id']);
                $ordermodel->setDeliveryDate($data['delivery_date']);
                $ordermodel->setMessageHeading($data['message_heading']);
                $ordermodel->setMessageOncard($data['message_oncard']);
                $ordermodel->setExtraCharge($data['extra_charge']);
                $ordermodel->setSenderName($data['sender_name']);
                $ordermodel->setSpecialInstruction($data['special_instruction']);
                $ordermodel->setDeliveryTime($data['delivery_time']);
                $ordermodel->setOtherInfo($data['other_info']);
                $ordermodel->setData('created_time',$data['created_time']);
                $ordermodel->save();
            }
   }
    
}
