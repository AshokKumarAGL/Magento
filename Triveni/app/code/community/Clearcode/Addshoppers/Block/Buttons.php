<?php

/**
 * Buttons block to display the buttons
 *
 * @package CLS_AddShoppers
 * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
 * @author Nicholas Vahalik <nick@classyllama.com>
 */
class Clearcode_Addshoppers_Block_Buttons extends Clearcode_Addshoppers_Block_Abstract
{
    /**
     * If enabled, return the button code.
     *
     * @return string HTML Code
     * @copyright Copyright (c) 2012 Classy Llama Studios, LLC
     * @author Nicholas Vahalik <nick@classyllama.com>
     */
    public function _toHtml() {
        if ($this->getStoreConfig('clearcode_addshoppers/settings/enabled')) {
            return $this->_getButtonsCode();
        }
    }
    
    private function _getButtonsCode()
    {
        if ($this->getStoreConfig('clearcode_addshoppers/settings/social'))
        {
            return $this->getStoreConfig('clearcode_addshoppers/settings/button_code');
        }
    }
    
    protected function getStoreConfig($path)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $storeId = Mage::app()->getStore()->getId();
        
        $query = "SELECT value FROM " . $resource->getTableName('core_config_data') . " WHERE scope_id = '" . $storeId . "' AND path = '" . $path . "'";
        
        $results = $readConnection->fetchCol($query);
        
        if (count($results) == 0)
        {
            $query = "SELECT value FROM " . $resource->getTableName('core_config_data') . " WHERE scope_id = '0' AND path = '" . $path . "'";
            $results = $readConnection->fetchCol($query);
        }
        
        return $results[0];
    }
}
