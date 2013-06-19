<?php

class Triveni_Cronjob_Model_Observer {

    public function CronJob(){
       $collections=Mage::getModel('catalog/product')->getCollection();
     foreach($collections as $_product)
		{
			$_product->setView(0);
			$_product->setSale(0);
			$_product->setPopularToday(0);
			$saleOverall=$_product->getSaleOverall();
			$viewOverall=$_product->getViewOverall();
			$_product->setPopularOverall($saleOverall+$viewOverall);
			$_product->save();
		}
        return true;
    }

}

?>
