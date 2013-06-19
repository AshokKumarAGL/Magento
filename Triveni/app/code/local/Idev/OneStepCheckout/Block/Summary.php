<?php
/**
 *    OneStepCheckout summary block
 *    @author Jone Eide <mail@onestepcheckout.com>
 *    @copyright Jone Eide <mail@onestepcheckout.com>
 *
 */

class Idev_OneStepCheckout_Block_Summary extends Mage_Checkout_Block_Onepage_Abstract    {

    public function __construct()
    {
        $this->getQuote()->collectTotals()->save();
    }

    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }

    public function getGrandTotals()
    {
        return $this->getQuote()->getBaseGrandTotal();
    }

    public function getCoupon()
    {
        return $this->getQuote()->getCouponCode();
    }
}
