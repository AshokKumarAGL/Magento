<?php
include_once('Mage/Customer/controllers/AccountController.php');

class Triveni_Customer_AccountController extends Mage_Customer_AccountController
{

    protected function __generatePassword($len='7'){
        $al = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        for ($index = 1; $index <= $len; $index++)
        {
            $randomNumber = rand(1,strlen($al));
            $password .= substr($al,$randomNumber-1,1);
        }
        return $password;
    }

    public function createAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function createUserAction() {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input

        if(!$this->getRequest()->isPost()){
            $email = $this->getRequest()->getParam('email');
        }

        if ($this->getRequest()->isPost() || $email!='') {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }
            if($email!=''){
                $prefix = '-';
                $gender = strtolower($this->getRequest()->getParam('gender'));
                if($gender!='') {
                    if($gender=='male' || $gender=='m' || $gender=='mr'){
                        $prefix = "Mr";
                    } else if($gender=='female' || $gender=='f' || $gender=='ms' || $gender=='mrs'){
                        $prefix = "Ms";
                    }
                }

                $fname = $this->getRequest()->getParam('fname');
                if($fname=='') $fname = strstr($email, '@', true);

                $lname = $this->getRequest()->getParam('lname');
                if($lname=='') $lname = $fname;

                $pass = $this->__generatePassword();

                $data = array(
                    'success_url'   =>'',
                    'error_url'     =>'',
                    'prefix'        =>$prefix,
                    'firstname'     =>$fname,
                    'lastname'      =>$lname,
                    'email'         =>$email,
                    'day'           => '',
                    'month'         =>'',
                    'year'          =>'',
                    'dob'           =>'',
                    'password'      =>$pass,
                    'confirmation'  =>$pass
                );
            } else {
                $data = $this->_filterPostData($this->getRequest()->getPost());
            }

            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('create') && isset($data[$code])) {
                    if ($code == 'email') {
                        $data[$code] = trim($data[$code]);
                    }
                    $customer->setData($code, $data[$code]);
                }
            }

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                    ->setData($this->getRequest()->getPost())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                    ->setId(null);
                $customer->addAddress($address);

                $errors = $address->validate();
                if (!is_array($errors)) {
                    $errors = array();
                }
            }

            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {

                    $customer->save();

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    }
                    else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess(Mage::getUrl('/'), array('_secure' => true));
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    }
                    else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                }
                else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            }
            catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }
        $this->_redirectError(Mage::getUrl('/', array('_secure' => true)));
    }

    protected function _loginPostRedirect() {
          $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl()) {

            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn())
            {
                if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
                    if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
                        $referer = Mage::helper('core')->urlDecode($referer);
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                }
                else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }
        else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
            if (!$session->isLoggedIn()) {
                header("Location: ".Mage::getUrl('customer/account/login'));
                exit;
            }
        }

        if (Mage::app()->getStore()->getId()== 7) { // check for shop.bestylish.com
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl());
        } else {
            $this->_redirectUrl($session->getBeforeAuthUrl(true));
        }
    }
    /*****************Make function for Cart Register******************************/
	public function createnewuserAction() {
	        $result = array('success' => false);
	        $session = $this->_getSession();
	        if ($session->isLoggedIn()) {
	            $result['error'] = $this->__('You Are Already Logged in.');
	        }
	        Mage::log('this is create new user action here');
	        $session->setEscapeMessages(true); // prevent XSS injection in user input
	        if($this->getRequest()->isPost()=='1'){
	            $email = $this->getRequest()->getParam('customer_username');
	            $name = $this->getRequest()->getParam('customer_name');
	            $country = $this->getRequest()->getParam('country_id');
	            $phone_extn = $this->getRequest()->getParam('area_code');
	            $phone_no = $this->getRequest()->getParam('customer_phone');
	        }
	        if ($this->getRequest()->isPost() || $email!='') {
	            $errors = array();
	            if (!$customer = Mage::registry('current_customer')) {
	                $customer = Mage::getModel('customer/customer')->setId(null);
	            }
	            if($email!='' && $name!='' && $country !=''){
	            	$parts = explode(" ", $name);
					$lastname = array_pop($parts);
					$firstname = implode(" ", $parts);
						            	
	                $prefix = '-';
	                $fname = $firstname;
	                $lname = $lastname;
	                $pass = $this->getRequest()->getParam('cart_password');
	                $data = array(
	                    'success_url'   =>'',
	                    'error_url'     =>'',
	                    'prefix'        =>$prefix,
	                    'firstname'     =>$fname,
	                    'lastname'      =>$lname,
	                    'email'         =>$email,
	                    'day'           => '',
	                    'month'         =>'',
	                    'year'          =>'',
	                    'dob'           =>'',
	                    'password'      =>$pass,
	                    'confirmation'  =>$pass
	                );
	            }
	            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
	                if ($node->is('create') && isset($data[$code])) {
	                    if ($code == 'email') {
	                        $data[$code] = trim($data[$code]);
	                    }
	                    $customer->setData($code, $data[$code]);
	                }
	            }
	
	            if ($this->getRequest()->getParam('is_subscribed')=='true') {
	                $customer->setIsSubscribed(1);
	            }else {
	                $customer->setIsSubscribed(0);
	            }
	            $customer->getGroupId();
	            if ($this->getRequest()->getPost('create_address')) {
	                $address = Mage::getModel('customer/address')
	                    ->setData($this->getRequest()->getPost())
	                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
	                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
	                    ->setId(null);
	                $customer->addAddress($address);
	                $errors = $address->validate();
	                if (!is_array($errors)) {
	                    $errors = array();
	                }
	            }
	            try {
	                    $validationCustomer = $this->_newValidation($customer);
	                    if (is_array($validationCustomer)) {
				$errors = array_merge($validationCustomer, $errors);
	                        $resultError = implode('<br>',$validationCustomer);
	                    }
	                    $validationResult = count($errors) == 0;
	                    if (true === $validationResult) {
	                        $customer->save();
	                        $session->setCustomerAsLoggedIn($customer);
	                        $result = array('success' => true);
	                                                
	                    } else {
	                        $result['error'] = $resultError;
	                    }
	                }
	                catch (Mage_Core_Exception $e) {
	                    $session->setCustomerFormData($this->getRequest()->getPost());
	                    if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
	                        $message = $this->__('An account already exists on this email address! Please select the option "I have a beStylish account" to reattempt login or to reset your password.');
	                        $result['error'] = $message;
	                    } else {
	                          $result['error'] = $result['error'] = $resultError;;
	                    }
	                } catch (Exception $e) {
	                $result['error'] = $resultError;
	            }
	        }
	         $this->getResponse()->setBody(Zend_Json::encode($result));
	   }

   /*******************MAKE NEW VALIDATE FUNCTION FOR ABOVE FUNCTION********************/

   protected function _newValidation($cust) {

        $errors = array();
        $customerHelper = Mage::helper('customer');
        if (!Zend_Validate::is($cust->getEmail(), 'EmailAddress')) {
            $errors[] = $customerHelper->__('Invalid email address');
        }

        $password = trim($cust->getPassword());
        if (!$cust->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = $customerHelper->__('The password cannot be empty.');
        }
        if ($password && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = $customerHelper->__('The minimum allowed password length is %s characters', 6);
        }
       
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

   /************************************************************************************/
    
    
    /*******************MAKE NEW Action for MEMBER DISCOUNT AS PER LIVE SITE********************/
    public function discountAction()
    {
    
    	if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();    
    }
    
    
}
