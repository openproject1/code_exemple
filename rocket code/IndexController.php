<?php

/**
 *
 * @package auth
 * @author cvancea
 */
class Auth_IndexController extends Zend_Controller_Action
{
    const MSG_ERROR_EMAIL_FAIL   = "System problem when trying to send mail with the reset password instructions !";
    const MSG_EMAIL_SEND_SUCCESS = "An e-mail with password reset instructions was sent to your e-mail address.";

    // @var MerchantExt_Service_MerchantAuth
    protected $_service;

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     *
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_service = new MerchantExt_Service_MerchantAuth();

        // change the layout for all the action in this controller ( admin layout )
        $this->_helper->layout->setLayout('admin');
    }

    /**
     * Forward to login controller.
     *
     */
    public function indexAction()
    {
        $this->_forward("login");
    }

    /**
     * Login
     *
     */
    public function loginAction()
    {
        // when login go to the default index page
        if(Zend_Auth::getInstance()->hasIdentity()){
            $this->view->is_logged_in = true;
            $this->_helper->redirector('index', 'index', 'default');
        };

        //create the form
        $oForm = new Auth_Form_Login();

        // get the request
        $oRequest = $this->getRequest();

        if ($oRequest->isPost()) {
            $aFormData = $oRequest->getPost();
            if ($oForm->isValid($aFormData)) {
                // do the login
                if ($this->_loginMerchantInBob($aFormData)) {
                    // after successfully login go to default index page
                    $this->_helper->redirector('index', 'index', 'default');
                }
            } else {
                $oForm->populate($aFormData);
            }
        }

        $this->view->form = $oForm;
    }

	/**
	 * Logout
	 *
	 */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $auth->clearIdentity();
        }

        // after logout go to the default index page
        $this->_helper->redirector('index', 'index', 'default');
    }

	/**
	 * Set password
	 *
	 */
    public function setPasswordAction()
    {
        $sSetPasswordKey = $this->_request->getParam('merchant', false);

        // get the request
        $oRequest = $this->getRequest();

        if (!$oRequest->isPost()){
            $this->_notPostSetResetPasswordVerification($sSetPasswordKey);
        }

        // create the form
        $oForm = new Auth_Form_SetPassword();

        if ($oRequest->isPost()) {
            $aFormData = $oRequest->getPost();
            if ($oForm->isValid($aFormData)) {
                $this->_isPostCheckSavePassword($aFormData, $sSetPasswordKey);
            } else {
                $oForm->populate($aFormData);
            }
        }

        $this->view->form = $oForm;
    }

	/**
	 * Forgot Password
	 *
	 */
    public function forgotPasswordAction()
    {
        // create the form
        $oForm = new Auth_Form_ForgotPassword();

        // get the request
        $oRequest = $this->getRequest();

        if ($oRequest->isPost()) {
            $aFormData = $oRequest->getPost();
            if ($oForm->isValid($aFormData)) {

                // send the email address to bob and get a new set_pasword_key from bob to use in the email
                $mSetPasswordKey = $this->_service->getMerchantSetPasswordKeyByEmail($aFormData);

                // only when email address exist and set_password_key present
                if ($mSetPasswordKey !== FALSE ) {

                    // send the email address to bob and get the username/login to write the  username/login in the email
                    $sLogin = (string) $this->_service->getMerchantLoginByEmail($aFormData);

                    // ! send email
                    $this->_sendMailResetPasswordLink($mSetPasswordKey, $aFormData, $sLogin);

                    // after the set_pasword_key is generated and send the email and redirect to default index
                    $this->_helper->redirector('index', 'index', 'default');
                }

            } else {
                $oForm->populate($aFormData);
            }
        }

        $this->view->form = $oForm;
    }

	/**
	 * Reset password
	 *
	 */
    public function resetPasswordAction()
    {
        $sSetPasswordKey = $this->_request->getParam('merchant', FALSE);

        // get the request
        $oRequest = $this->getRequest();

        if (!$oRequest->isPost()){
            $this->_notPostSetResetPasswordVerification($sSetPasswordKey);
        }

        // create the form
        $oForm = new Auth_Form_ResetPassword();

        if ($oRequest->isPost()) {
            $aFormData = $oRequest->getPost();
            if ($oForm->isValid($aFormData)) {
                $this->_isPostCheckSavePassword($aFormData, $sSetPasswordKey);
            }
            else{
                $oForm->populate($aFormData);
            }
        }

        $this->view->form = $oForm;
    }


    /**
     * TODO move this function into something like Login_Client_Marketplace
     *
     * @param array $aData
     * @internal param array $data from form
     * @return bool
     */
    protected function _loginMerchantInBob(array $aData)
    {
        // Save a reference to the Singleton instance of Zend_Auth
        $auth = Zend_Auth::getInstance();

        // Attempt authentication, saving the result
        $result = $auth->authenticate(new Conny_Auth_Adapter($aData['username'], $aData['password']));

        // Interpret the auth result
        if ($result->isValid()){
            return true;
        }

        return false;
    }

    /**
     * Handel the set reset when the request in not a post
     * @param string $sSetPasswordKey
     */
    protected function _notPostSetResetPasswordVerification($sSetPasswordKey){
        /*
         * verify if this set_password_key exist
         */
        $oSetPasswordKeyVerification = $this->_service->verifySetPasswordKey($sSetPasswordKey);

        if ($oSetPasswordKeyVerification === false){
            // go out when verification fails
            $this->_helper->redirector('index', 'index', 'default');
        }
    }

    /**
     * Handel the check and save the password to bob when request is post
     * @param array  $aFormData
     * @param string $sSetPasswordKey
     */
    protected function _isPostCheckSavePassword($aFormData, $sSetPasswordKey){
        // check the passwords
        if($this->_service->checkNewPassword($aFormData)){
            // save the password to bob
            if ($this->_service->savePasswordToBob($aFormData, $sSetPasswordKey)) {
                // new password saved redirect to default index
                $this->_helper->redirector('index', 'index', 'default');
            }
        }
    }

    /**
     * Go to the email service and send the email from bob
     *
     * @param mixed $sSetPasswordKey
     * @param array $aFormData
     * @param string $sLogin
     */
    protected function _sendMailResetPasswordLink($mSetPasswordKey, $aFormData, $sLogin){

        $sSetPasswordKey = (string) $mSetPasswordKey;
        $bMailResponse = $this->_service->sendMailResetPasswordLink($sSetPasswordKey, $aFormData, $sLogin);

        if($bMailResponse === TRUE){
            Conny_Messaging_Factory::getFlashMessenger()->addSuccessMessage(self::MSG_EMAIL_SEND_SUCCESS);
        }
        else{
            Conny_Messaging_Factory::getFlashMessenger()->addErrorMessage(self::MSG_ERROR_EMAIL_FAIL);
        }
    }
}