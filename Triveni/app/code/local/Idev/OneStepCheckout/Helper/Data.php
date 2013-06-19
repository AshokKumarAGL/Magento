<?php
class Idev_OneStepCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function setCustomerComment($observer)
    {
        $enable_comments = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments');
        $enableFeedback = Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback');

        if($enable_comments)	{
            $orderComment = $this->_getRequest()->getPost('onestepcheckout_comments');
            $orderComment = trim($orderComment);

            if ($orderComment != "")
            {
                $observer->getEvent()->getOrder()->setOnestepcheckoutCustomercomment($orderComment);
            }
        }

        if($enableFeedback){

            $feedbackValues = unserialize(Mage::getStoreConfig('onestepcheckout/feedback/feedback_values'));
            $feedbackValue = $this->_getRequest()->getPost('onestepcheckout-feedback');
            $feedbackValueFreetext = $this->_getRequest()->getPost('onestepcheckout-feedback-freetext');

            if(!empty($feedbackValue)){
                if($feedbackValue!='freetext'){
                    $feedbackValue = $feedbackValues[$feedbackValue]['value'];
                } else {
                    $feedbackValue = $feedbackValueFreetext;
                }

                $observer->getEvent()->getOrder()->setOnestepcheckoutCustomerfeedback($feedbackValue);
            }

        }
    }

    public function isRewriteCheckoutLinksEnabled()
    {
        return Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
    }

    /**
     * If we are using enterprise wersion or not
     * @return boolean
     */
    public function isEnterPrise(){
        return (str_replace('.', '', Mage::getVersion()) > 1600) ? true : false;
    }
    
    public function getSavingofTotal($order=null) {
        $newDiscTotal = '';
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $store_id = Mage::helper('core')->getStoreId();
        if($order == null) {
            $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        }else {
            $items = $order->getAllItems();
        }
        foreach($items as $item) {
            $_product = Mage::getModel('catalog/product')->load($item->getProductId());
            $parentIdArray = $_product->loadParentProductIds()->getData('parent_product_ids');
            if(count($parentIdArray) == 0) {
                $mainVal = $read->query("SELECT DISTINCT cpf.entity_id, cpf.price, cpip.final_price FROM catalog_product_flat_".$store_id." AS cpf LEFT JOIN catalog_product_index_price AS cpip ON cpf.entity_id = cpip.entity_id WHERE cpf.entity_id = '".$item->getProductId()."' AND cpip.customer_group_id in (0,1)");
                while($mainRowVal = $mainVal->fetch()) {
                    $quantityVar = ($order == null) ? $item->getQty():$item->getQtyOrdered();
                    if($mainRowVal['price']!='' && $mainRowVal['final_price']!='') {
                        $newDiscTotal = $newDiscTotal +(($mainRowVal['price']-$mainRowVal['final_price'])*$quantityVar);
                    }
                }
            }
        }
        return $newDiscTotal;
    }
    
    public function getNewSubTotal($order=null) {
        $newSubTotal = '';
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $store_id = Mage::helper('core')->getStoreId();
        if($order == null) {
            $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        }else {
            $items = $order->getAllItems();
        }
        
        foreach($items as $item) {
            $_product = Mage::getModel('catalog/product')->load($item->getProductId());
            $parentIdArray = $_product->loadParentProductIds()->getData('parent_product_ids');
            if(count($parentIdArray) == 0) {
                $quantityVar = ($order == null) ? $item->getQty():$item->getQtyOrdered();
                $mainVal = $read->query("SELECT DISTINCT cpf.entity_id, cpf.price FROM catalog_product_flat_".$store_id." AS cpf WHERE cpf.entity_id = '".$item->getProductId()."'");
                while($mainRowVal = $mainVal->fetch()){
                    $newSubTotal = $newSubTotal +(($mainRowVal['price'])*$quantityVar);
                }
            }
        }
        return $newSubTotal;
    }
    
    public function newGrandTotal(){
        return Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal();
    }
    
    public function makeBillingAddInArray() {
        $flagVar =  Mage::getSingleton('core/session')->getSessionAddFlagVariable();
            if($flagVar == 'true'){ 
                $billingAdd = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();
                if($billingAdd->getTelephone()=='-') {
                    $billTelephone = 'Tel Number';
                } else {
                    $billTelephone = $billingAdd->getTelephone();
                }
                  $billingAddressVar = array(
                    'prefix'             => $billingAdd->getPrefix(),
                    'firstname'          => $billingAdd->getFirstname(),
                    'lastname'           => $billingAdd->getLastname(),
                    'street1'            => $billingAdd->getStreet1(),
                    'street2'            => $billingAdd->getStreet2(),
                    'city'               => $billingAdd->getCity(),
                    'country_id'         => $billingAdd->getCountryId(),
                    'region'             => $billingAdd->getRegion(),
                    'region_id'          => $billingAdd->getRegionId(),
                    'postcode'           => $billingAdd->getPostcode(),
                    'telephone'          => $billTelephone,
                    'mobile'             => $billingAdd->getMobile(),
                );
               
            } else { 
                $billingAddressVar = array(
                    'prefix'             => '',
                    'firstname'          => '',
                    'lastname'           => '',
                    'street1'            => '',
                    'street2'            => '',
                    'city'               => '',
                    'country_id'         => '',
                    'region'             => '',
                    'region_id'          => '',
                    'postcode'           => '',
                    'telephone'          => '',
                    'mobile'             => '',
                );
            }
           return $billingAddressVar;
    }
    public function makeShippingAddInArray() {
        $flagVar =  Mage::getSingleton('core/session')->getSessionAddFlagVariable();
            if($flagVar == 'true'){
                $shippingAdd = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
                if($shippingAdd->getTelephone()=='-') {
                    $shippedTelephone = 'Tel Number';
                } else {
                    $shippedTelephone =$shippingAdd->getTelephone();
                }
                $shippingAddressVar = array(
                    'prefix'             => $shippingAdd->getPrefix(),
                    'firstname'          => $shippingAdd->getFirstname(),
                    'lastname'           => $shippingAdd->getLastname(),
                    'street1'            => $shippingAdd->getStreet1(),
                    'street2'            => $shippingAdd->getStreet2(),
                    'city'               => $shippingAdd->getCity(),
                    'country_id'         => $shippingAdd->getCountryId(),
                    'region'             => $shippingAdd->getRegion(),
                    'region_id'          => $shippingAdd->getRegionId(),
                    'postcode'           => $shippingAdd->getPostcode(),
                    'telephone'          => $shippedTelephone,
                    'mobile'             => $shippingAdd->getMobile(),
                );
            } else {
                $shippingAddressVar = array(
                    'prefix'             => '',
                    'firstname'          => '',
                    'lastname'           => '',
                    'street1'            => '',
                    'street2'            => '',
                    'city'               => '',
                    'country_id'         => '',
                    'region'             => '',
                    'region_id'          => '',
                    'postcode'           => '',
                    'telephone'          => '',
                    'mobile'             => '',
                );
            }
             return $shippingAddressVar;
    }
    function makeBillingShippingAddInArray() {
        if(Mage::getSingleton('core/session')->getDefaultBSId() && Mage::getSingleton('core/session')->getDefaultBSId()!='') {
            $finalId = Mage::getSingleton('core/session')->getDefaultBSId();
            $address = Mage::getModel('customer/address')->load($finalId);
            $shippingAddressVar = array(
            'street1'            => $address->getStreet1(),
            'street2'            => $address->getStreet2(),
            'city'               => $address->getCity(),
            'country_id'         => $address->getCountryId(),
            'region'             => $address->getRegion(),
            'region_id'          => $address->getRegionId(),
            'postcode'           => $address->getPostcode(),
            'telephone'           => $address->getTelephone());
        }else {
            $shippedarray = Mage::helper('onestepcheckout')->makeShippingAddInArray();
            $shippingAddressVar = array(
            'street1'            => $shippedarray['street1'],
            'street2'            => $shippedarray['street2'],
            'city'               => $shippedarray['city'],
            'country_id'         => $shippedarray['country_id'],
            'region'             => $shippedarray['region'],
            'region_id'          => $shippedarray['region_id'],
            'postcode'           => $shippedarray['postcode'],
            'telephone'           => $shippedarray['telephone']);
        }
        return $shippingAddressVar;
    }
}
