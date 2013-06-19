<?php

class Clearcode_Addshoppers_Adminhtml_AdminformController extends Mage_Adminhtml_Controller_Action
{
    /**
     * View form action
     */
    const REG_LOGIN_EXISTS = -1;
    const REG_ACCOUNT_NOT_CREATED = 0;
    const REG_ACCOUNT_CREATED = 1;
    const REG_PASSWORD_TOO_SHORT = 2;
    const REG_PASSWORD_CONSECUTIVE_CHARS = 8;
    const REG_PASSWORD_COMMON = 9;
    const REG_PARAM_MISSING = 10;
    const REG_DOMAIN_BANNED = 17;
    const REG_CATEGORY_INVALID = 19;
    const LOGIN_ACCOUNT_CREATED = 1;
    const LOGIN_MISSING_PARAMETER = 10;
    const LOGIN_WRONG_CREDENTIALS = 11;
    const LOGIN_SITE_EXISTS = 15;

    public $loginMessages = array(
        self::LOGIN_ACCOUNT_CREATED => 'Account authenticated successfuly',
        self::LOGIN_MISSING_PARAMETER => 'Please fill in all the fields',
        self::LOGIN_WRONG_CREDENTIALS => 'Wrong credentials',
        self::LOGIN_SITE_EXISTS => 'Site is already registered',
    );
    public $registrationMessages = array(
        self::REG_LOGIN_EXISTS => 'Login already exists',
        self::REG_ACCOUNT_NOT_CREATED => 'Account was not created due to unknown error',
        self::REG_ACCOUNT_CREATED => 'Account was successfuly created!',
        self::REG_PASSWORD_TOO_SHORT => 'Password is too short',
        self::REG_PASSWORD_CONSECUTIVE_CHARS => 'Password must consist of different characters',
        self::REG_PASSWORD_COMMON => 'Password is too weak',
        self::REG_PARAM_MISSING => 'Request was invalid',
        self::REG_DOMAIN_BANNED => 'Your domain is banned',
    );
    protected $api_url = 'http://api.addshoppers.com/1.0';
    protected $defaultShopId = '500975935b3a42793000002b';
    protected $defaultButtons = '<div class="share-buttons share-buttons-tab" data-buttons="twitter,facebook,pinterest" data-style="medium" data-counter="true" data-hover="true" data-promo-callout="true" data-float="left"></div>';
    //// '<div style="float:right;"><div class="share-buttons share-buttons-panel" data-style="medium" data-counter="true" data-oauth="true" data-hover="true" data-buttons="twitter,facebook,pinterest"></div></div><div class="share-buttons-multi"><div class="share-buttons share-buttons-fb-like" data-style="standard"></div><div class="share-buttons share-buttons-og" data-action="want" data-counter="false"></div><div class="share-buttons share-buttons-og" data-action="own" data-counter="false"></div></div>';
    protected $shopUrl;
    protected $shopName;
    protected $platform = "magento";

    protected function sendRequestWithBasicAuth($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, 'addshop:addshop123');
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }

    function getButtonsCode($shopid, $apikey)
    {
        return array(
            'buttons' => array(
                'button1' => $this->defaultButtons,
                'button2' => $this->defaultButtons,
                'open-graph' => $this->defaultButtons,
            ),
        );
        
        $data = "shopid=" . urlencode($shopid)
            . "&key=" . urlencode($apikey);

        $curl = curl_init($this->api_url . '/account/social-analytics/tracking-codes');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return json_decode($result, true);
    }

    public function _beforeSave($object)
    {
        return $this;
    }

    public function indexAction()
    {
        $this->_redirect('/system_config/edit/section/addshoppers/');
    }

    public function gridAction()
    {
        $this->_registryObject();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('addshoppers/adminhtml_form_edit_tab_login')
                ->toHtml()
        );
    }

    public function registerAction()
    {
        $this->shopUrl = urlencode(Mage::app()->getStore()->getBaseUrl());
        $this->shopName = urlencode(Mage::app()->getStore()->getName());

        $params = $this->getRequest()->getParams();

        if(!isset($params['register']['register']))
        {
            $data = "email=" . urlencode($params['register']['login'])
                . "&password=" . urlencode($params['register']['password'])
                . "&url=" . $this->shopUrl
                . "&category=" . urlencode($params['register']['category'])
                . "&platform=" . $this->platform
                . "&site_name=" . $this->shopName;

            if (isset($params['register']['phone']))
                $data .= "&phone=" . urlencode($params['register']['phone']);

            $return = $this->sendRequestWithBasicAuth($this->api_url . '/registration', $data);
        }
        // $this->getResponse()->setBody(var_dump($return));
        if(isset($params['register']['register']))
        {
            $return['result'] = 1;
        }
        if(($return['result'] == -1))
        {
            $message = Clearcode_Addshoppers_Block_Adminform::LOGIN_EXISTS;
        }
        elseif($return['result'] == 0)
        {
            $message = Clearcode_Addshoppers_Block_Adminform::NOT_CREATED;
        }
        elseif($return['result'] == 2)
        {
            $message = Clearcode_Addshoppers_Block_Adminform::PASSWORD_TOO_SHORT;
        }
        elseif($return['result'] == 8)
        {
            $message = Clearcode_Addshoppers_Block_Adminform::PASSWORD_WRONG;
        }
        elseif(($return['result'] == 1) || (isset($params['register']['register'])))
        {
            if(isset($params['configuration']['use_schema']))
            {
                $params['configuration']['use_schema'] = 1;
            }
            if(isset($params['configuration']['social']))
            {
                $params['configuration']['social'] = 1;
            }
            if(isset($params['configuration']['opengraph']))
            {
                $params['configuration']['opengraph'] = 1;
            }

            $this->saveConfigForStore('clearcode_addshoppers/settings/enabled', $params['store'],  1);
            $this->saveConfigForStore('clearcode_addshoppers/settings/use_schema', $params['store'],  $params['configuration']['social']);

            if(isset($params['register']['register']))
            {
                $data = "login=" . urlencode($params['register']['login'])
                    . "&password=" . urlencode($params['register']['password'])
                    . "&url=" . urlencode(Mage::app()->getStore()->getHomeUrl())
                    . "&category=" . urlencode($params['register']['category'])
                    . "&platform=" . $this->platform
                    . "&site_name=" . $this->shopName;
            if (isset($params['register']['phone']))
                $data .= "&phone=" . urlencode($params['register']['phone']);

                $return = $this->sendRequestWithBasicAuth($this->api_url . '/login', $data);
            }
            $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  $return['api_key']);
            $buttons_codes = $this->getButtonsCode($return['shopid'], $return['api_key']);
            $buttons_code_html = '';
            if($params['configuration']['social'])
            {
                $buttons_code_html = '<div style="float:right;">' . $buttons_codes['buttons']['button2'] . '</div>';
            }
            if($params['configuration']['social'])
            {
                $buttons_code_html = $buttons_code_html . $buttons_codes['buttons']['open-graph'];
            }
            if($buttons_code_html == '')
            {
                $buttons_code_html = $buttons_codes['buttons']['button1'];
            }

            $this->saveConfigForStore('clearcode_addshoppers/settings/button_code', $params['store'],  $buttons_code_html);

            $this->saveConfigForStore('clearcode_addshoppers/settings/active', $params['store'],  1);
            $this->saveConfigForStore('clearcode_addshoppers/settings/url', $params['store'],  urlencode(Mage::helper('core/url')->getHomeUrl()));
            $this->saveConfigForStore('clearcode_addshoppers/settings/platform', $params['store'],  $params['register']['platform']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/social', $params['store'],  1);
            $this->saveConfigForStore('clearcode_addshoppers/settings/opengraph', $params['store'],  1);
            $this->saveConfigForStore('clearcode_addshoppers/settings/email', $params['store'],  $params['register']['email']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/password', $params['store'],  $params['register']['password']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/category', $params['store'],  $params['register']['category']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/shopid', $params['store'],  $return['shopid']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/url', $params['store'],  $this->shopUrl);

            $message = Clearcode_Addshoppers_Block_Adminform::CREATED;
        }
        else
        {
            $message = 'Other except!';
        }

        Mage::getSingleton('core/session')->setData('message', array('value' => $message));
        
        Mage::app()->getCache()->clean();
        
        $this->redirectBack($params['store']);
    }

    public function socialAction()
    {
        $params = $this->getRequest()->getParams();
        $social = (isset($params['config']['social'])) ? 1 : 0;
        $this->saveConfigForStore('clearcode_addshoppers/settings/social', $params['store'],  $social);
        $open = (isset($params['config']['opengraph'])) ? 1 : 0;
        $this->saveConfigForStore('clearcode_addshoppers/settings/opengraph', $params['store'],  $open);
        $schema = (isset($params['config']['use_schema'])) ? 1 : 0;
        $this->saveConfigForStore('clearcode_addshoppers/settings/use_schema', $params['store'],  $schema);
        
        Mage::app()->getCache()->clean();
        
        $this->redirectBack($params['store']);
    }

    public function settingsAction()
    {
        $params = $this->getRequest()->getParams();
        $this->saveConfigForStore('clearcode_addshoppers/settings/email', $params['store'],  $params['config']['login']);
        $this->saveConfigForStore('clearcode_addshoppers/settings/password', $params['store'],  $params['config']['password']);
        $this->saveConfigForStore('clearcode_addshoppers/settings/shopid', $params['store'],  $params['config']['shop_id']);
        $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  $params['config']['api_key']);
        
        Mage::app()->getCache()->clean();
        
        $this->redirectBack($params['store']);
    }

    public function loginAction()
    {
        $this->shopUrl = urlencode(Mage::app()->getStore()->getBaseUrl());
        $this->shopName = urlencode(Mage::app()->getStore()->getName());

        $params = $this->getRequest()->getParams();

        if($this->getConfigForStore('clearcode_addshoppers/settings/active', $params['store']) != 1)
        {
            $this->saveConfigForStore('clearcode_addshoppers/settings/social', $params['store'],  1);
            $params['configuration']['social'] = 1;
            $this->saveConfigForStore('clearcode_addshoppers/settings/opengraph', $params['store'],  1);
            $params['configuration']['opengraph'] = 1;
            $this->saveConfigForStore('clearcode_addshoppers/settings/use_schema', $params['store'],  1);
            $params['configuration']['use_schema'] = 1;
        }

        $data = "login=" . urlencode($params['login']['login'])
            . "&password=" . urlencode($params['login']['password'])
            . "&url=" . $this->shopUrl
            . "&category=Other"
            . "&phone=" . urlencode($this->getConfigForStore('clearcode_addshoppers/settings/phone', $params['store']))
            . "&platform=" . $this->platform
            . "&site_name=" . $this->shopName;

        $return = $this->sendRequestWithBasicAuth($this->api_url . '/login', $data);

        if($return['result'] == 17)
        {
            $message = Clearcode_Addshoppers_Block_Adminform::DOMAIN_BANNED;
        }
        elseif($return['result'] == 11)
        {
            $message = Clearcode_Addshoppers_Block_Adminform::WRONG_CREDENTIAL;
        }
        elseif(($return['result'] == 1) || ($return['result'] == 15) || ($return['result'] == 10))
        {

            if($return['result'] == 10)
            {
                if((isset($params['login']['login'])) && (isset($params['login']['password'])))
                {
                    $message = "LOGIN_MISSING_PARAMETER";
                    $data = "login=" . urlencode($params['login']['login'])
                        . "&password=" . urlencode($params['login']['password'])
                        . "&url=" . $this->shopUrl
                        . "&category=" . urlencode($this->getConfigForStore('clearcode_addshoppers/settings/category', $params['store']))
                        . "&phone=" . urlencode($this->getConfigForStore('clearcode_addshoppers/settings/phone', $params['store']))
                        . "&platform=" . $this->platform
                        . "&site_name=" . $this->shopName;

                    $return = $this->sendRequestWithBasicAuth($this->api_url . '/login', $data);
                    // $this->getResponse()->setBody(var_dump($return));
                    if(!isset($return['shopid']))
                    {
                        $return['shopid'] = $this->getConfigForStore('clearcode_addshoppers/settings/shopid', $params['store']);
                    }
                    if(!isset($return['api_key']))
                    {
                        $return['api_key'] = $this->getConfigForStore('clearcode_addshoppers/settings/account_id', $params['store']);
                    }
                    $buttons_codes = $this->getButtonsCode($return['shopid'], $return['api_key']);
                    $buttons_code_html = '';
                    if($params['configuration']['social'])
                    {
                        $buttons_code_html = '<div style="float:right;">' . $buttons_codes['buttons']['button2'] . '</div>';
                    }
                    if($params['configuration']['opengraph'])
                    {
                        $buttons_code_html = $buttons_code_html . $buttons_codes['buttons']['open-graph'];
                    }
                    if($buttons_code_html == '')
                    {
                        $buttons_code_html = $buttons_codes['buttons']['button1'];
                    }
                    if(!isset($buttons_codes['buttons']['button2']))
                    {
                        $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  '9NF-HtmcFlwYOCOCpRJRwv6smHChiubg');
                        $buttons_code_html = '<div class="share-buttons share-buttons-panel" data-style="medium" data-counter="true" data-oauth="true" data-hover="true" data-buttons="twitter,facebook,pinterest"></div><div style="float:right;"><div class="share-buttons-multi"><div class="share-buttons share-buttons-fb-like" data-style="standard"></div><div class="share-buttons share-buttons-og" data-action="want" data-counter="false"></div><div class="share-buttons share-buttons-og" data-action="own" data-counter="false"></div></div></div>';
                    }
                    $this->saveConfigForStore('clearcode_addshoppers/settings/button_code', $params['store'],  $buttons_code_html);

                    $this->saveConfigForStore('clearcode_addshoppers/settings/active', $params['store'],  1);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/social', $params['store'],  $params['configuration']['social']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/opengraph', $params['store'],  $params['configuration']['opengraph']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/email', $params['store'],  $params['login']['login']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/password', $params['store'],  $params['login']['password']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/category', $params['store'],  $params['login']['category']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/shopid', $params['store'],  $return['shopid']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  $return['api_key']);
                    $this->saveConfigForStore('clearcode_addshoppers/settings/url', $params['store'],  $this->shopUrl);

                    $this->redirectBack($params['store']);
                }
            }

            if(isset($params['configuration']['use_schema']))
            {
                $params['configuration']['use_schema'] = 1;
            }
            if(isset($params['configuration']['social']))
            {
                $params['configuration']['social'] = 1;
            }
            if(isset($params['configuration']['opengraph']))
            {
                $params['configuration']['opengraph'] = 1;
            }

            $this->saveConfigForStore('clearcode_addshoppers/settings/enabled', $params['store'],  1);
            $this->saveConfigForStore('clearcode_addshoppers/settings/use_schema', $params['store'],  $params['configuration']['use_schema']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  $return['api_key']);
//              var_dump($this->getButtonsCode($return['shopid'],$return['api_key']));

            $buttons_codes = $this->getButtonsCode($return['shopid'], $return['api_key']);
            $buttons_code_html = '';
            if($params['configuration']['social'])
            {
                $buttons_code_html = '<div style="float:right;">' . $buttons_codes['buttons']['button2'] . '</div>';
            }
            if($params['configuration']['opengraph'])
            {
                $buttons_code_html .= $buttons_codes['buttons']['open-graph'];
            }
            if($buttons_code_html == '')
            {
                $buttons_code_html = $buttons_codes['buttons']['button1'];
            }
            $this->saveConfigForStore('clearcode_addshoppers/settings/button_code', $params['store'],  $buttons_code_html);

            $this->saveConfigForStore('clearcode_addshoppers/settings/active', $params['store'],  1);
            $this->saveConfigForStore('clearcode_addshoppers/settings/social', $params['store'],  $params['configuration']['social']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/opengraph', $params['store'],  $params['configuration']['opengraph']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/email', $params['store'],  $params['login']['login']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/password', $params['store'],  $params['login']['password']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/category', $params['store'],  'Other');
            $this->saveConfigForStore('clearcode_addshoppers/settings/shopid', $params['store'],  $return['shopid']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  $return['api_key']);
            $this->saveConfigForStore('clearcode_addshoppers/settings/url', $params['store'],  $this->shopUrl);

            $message = 'AUTHENTICATED';
        }
        else
        {
            $message = 'Unknown exception.';
        }

        Mage::getSingleton('core/session')->setData('message', array('value' => $message));
        
        Mage::app()->getCache()->clean();

        $this->redirectBack($params['store']);
    }

    public function resetAction()
    {
        $params = $this->getRequest()->getParams();
        
        $this->saveConfigForStore('clearcode_addshoppers/settings/button_code', $params['store'],  $this->defaultButtons);
        $this->saveConfigForStore('clearcode_addshoppers/settings/active', $params['store'],  0);
        $this->saveConfigForStore('clearcode_addshoppers/settings/social', $params['store'],  1);
        $this->saveConfigForStore('clearcode_addshoppers/settings/opengraph', $params['store'],  1);
        $this->saveConfigForStore('clearcode_addshoppers/settings/email', $params['store'],  '');
        $this->saveConfigForStore('clearcode_addshoppers/settings/password', $params['store'],  '');
        $this->saveConfigForStore('clearcode_addshoppers/settings/category', $params['store'],  '');
        $this->saveConfigForStore('clearcode_addshoppers/settings/shopid', $params['store'],  $this->defaultShopId);
        $this->saveConfigForStore('clearcode_addshoppers/settings/enabled', $params['store'],  1);
        $this->saveConfigForStore('clearcode_addshoppers/settings/use_schema', $params['store'],  1);
        $this->saveConfigForStore('clearcode_addshoppers/settings/account_id', $params['store'],  '');
        $this->saveConfigForStore('clearcode_addshoppers/settings/url', $params['store'],  '');
        $this->saveConfigForStore('clearcode_addshoppers/settings/platform', $params['store'],  'magento');
        
        Mage::app()->getCache()->clean();

        $this->redirectBack($params['store']);
    }

    public function enableAction()
    {
        $params = $this->getRequest()->getParams();
        $this->saveConfigForStore('clearcode_addshoppers/settings/enabled', $params['store'], $params['status']['enabled']);
        
        Mage::app()->getCache()->clean();
        
        $this->redirectBack($params['store']);
    }

    protected function _registryObject()
    {
        Mage::register('clearcode_addshoppers', Mage::getModel('clearcode_addshoppers/form'));
    }
    
    protected function getConfigForStore($path, $storeCode)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $storeId = Mage::app()->getStore($storeCode)->getId();
        
        $query = "SELECT value FROM " . $resource->getTableName('core_config_data') . " WHERE scope_id = '" . $storeId . "' AND path = '" . $path . "'";
        
        $results = $readConnection->fetchCol($query);
        
        if (count($results) == 0)
        {
            $query = "SELECT value FROM " . $resource->getTableName('core_config_data') . " WHERE scope_id = '0' AND path = '" . $path . "'";
            $results = $readConnection->fetchCol($query);
        }
        
        return isset($results[0]) ? $results[0] : "";
    }
    
    protected function saveConfigForStore($path, $storeCode, $value)
    {
        $configModel = Mage::getResourceModel('core/config');
        $storeId = ($storeCode == 'default') ? 0 : Mage::app()->getStore($storeCode)->getId();
        $scope = ($storeCode != 'default' && $storeId != 0) ? 'stores' : 'default';
        $configModel->saveConfig($path, $value, $scope, $storeId);
    }
    
    protected function redirectBack($storeCode)
    {
        $this->_redirect('/system_config/edit/section/addshoppers/store/'. $storeCode);
    }
}
