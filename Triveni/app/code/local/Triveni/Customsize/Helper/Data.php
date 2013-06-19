<?php
class Triveni_Customsize_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSizeList(){		
		$heightLenght = 70;
   		$increaseHeight = .5;
   		$sizeList = array();
		for($height = 25; $height < $heightLenght; $height = $height + 1/2){
			$sizeList[$height] = $height;
		}
		return $sizeList;
	}
	
	public function getHeight(){	
		$height = '';
		for($i=122;$i<212;$i++){
			$height .= '<option value="'.$i.'">'.$i.'</option>';
		}
		//echo $height;
	}
	
	
}
