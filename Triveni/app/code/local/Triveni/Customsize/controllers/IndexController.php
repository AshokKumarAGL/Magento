<?php
class Triveni_Customsize_IndexController extends Mage_Core_Controller_Front_Action
{
    
   public function customsizesaveAction() { 
       if ($data = $this->getRequest()->getPost()) {
        	
       		$model = Mage::getModel('customsize/customsize');
        	$model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            try{
            	$model->save();
            	echo 'SUCESS';
            }catch(Exception $e){
            	echo 'Faliure';
            }
            
       }
   }  
   
   public function loadstoresizeAction(){
		echo '<table width="100%" cellspacing="0" cellpadding="0" class="orderTable">
					<tbody>
						<tr>
							<th width="5" align="center">&nbsp;</th>
							<th align="center">Size Name</th>
						</tr>
						<tr>
							<td align="center" colspan="2">No Size Information available.</td>
						</tr>
					</tbody>
				</table>';
   }
   
}