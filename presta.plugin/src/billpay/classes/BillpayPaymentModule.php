<?php

/**
 * Billpay
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Billpay module to newer versions in the future.
 *
 * @category  Payment
 * @package   Billpay Prestashop module
 * @author    Billpay GmbH ( support@billpay.de )
 * @author    Catalin Vancea ( catalin.vancea@billpay.de )
 * @copyright Copyright 2012 Billpay GmbH
 * @license   Commercial
 * @link      https://www.billpay.de/
 */

include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayUtils.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayDbUtils.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpaySessionUtils.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayGatherDataUtils.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayGatherBackendDataUtils.php');

BillpaySessionUtils::getInstance()->startSession();

abstract class BillpayPaymentModule extends PaymentModule
{
    protected $_html = '';
    protected $_postErrors = array();

    const BILLPAY_API_TEST_URL = 'https://test-api.billpay.de/xml/offline';
    const BILLPAY_API_LIVE_URL = 'https://api.billpay.de/xml';

    public static $billpayTransactionModes = array('test' => 'test', 'live' => 'live');
    public static $billpayCustomerGroups   = array('b2c'  => 'b2c' , 'b2b' => 'b2b', 'both' => 'both');

    /**
     *
     * @var Billpay Module Configuration
     */
    protected $_billpayModuleConfig = array();

    /**
     *
     * @var int
     */
    protected $_billpayFormattedOrderTotal;

    /**
     *
     * @var array
     */
    protected $_billpaySubmitErrors = array();

    /**
     *
     * @var array
     */
    protected $_billpayPreauthorizeRequestResult = array();

    /**
     *
     * @var string
     */
    protected $_billpayPreauthorizeRequestBptid;

    /**
     *
     * @var int
     */
    private $_hookCancelProductCount = 0;


    /**
     * @var array
     */
    private $_hookCancelProductProductsUpdatedQuantity = array();

    /**
     *
     * @var array
     */
    private $_hookCancelProductProductsRefunded = array();


    /**
     * Class constructor. Initiates parent constructor.
     *
     * @return null
     */
    public function __construct()
    {
        $this->tab  = 'payments_gateways';

        $this->author  = 'Billpay GmbH';
        $this->version = '0.3.1';

        $this->need_instance = 1;

       parent::__construct();
    }


    /**
     * Presta install module function
     *
     * @return boolean
     */
    public function install()
    {
        if (!parent::install() ||
                !$this->registerHook('invoice') ||
                !$this->registerHook('payment') ||
                !$this->registerHook('paymentReturn') ||
                !$this->registerHook('updateOrderStatus') ||
                !$this->registerHook('adminOrder') ||
                !$this->registerHook('orderDetailDisplayed') ||
                !$this->registerHook('PDFInvoice') ||
                !$this->registerHook('cancelProduct'))
        {
            return false;
        }

        $this->_setStartBillpayConfigurations();
        $this->_createBillpayApprovedOrderStatus();
        $this->_createBillpayActivatedOrderStatus();
        $this->_createBillpayCanceldOrderStatus();

        if(!BillpayDbUtils::createBillpayTable())
            return false;

        return true;
    }

    /**
     * On install if config does not exist in presta db table create it, if exist use value from presta config db table
     *
     * @return void
     */
    private function _setStartBillpayConfigurations()
    {
        if (Configuration::get('BILLPAY_MERCHANT_ID') === false)
            Configuration::updateValue('BILLPAY_MERCHANT_ID' ,     '');
        if (Configuration::get('BILLPAY_PORTAL_ID') === false)
            Configuration::updateValue('BILLPAY_PORTAL_ID'   ,     '');
        if (Configuration::get('BILLPAY_API_PASSWORD') === false)
            Configuration::updateValue('BILLPAY_API_PASSWORD',     '');
        if (Configuration::get('BILLPAY_API_TEST_URL') === false)
            Configuration::updateValue('BILLPAY_API_TEST_URL',     self::BILLPAY_API_TEST_URL);
        if (Configuration::get('BILLPAY_API_LIVE_URL') === false)
            Configuration::updateValue('BILLPAY_API_LIVE_URL',     self::BILLPAY_API_LIVE_URL);
        if (Configuration::get('BILLPAY_TRANSACTION_MODE') === false)
            Configuration::updateValue('BILLPAY_TRANSACTION_MODE', self::$billpayTransactionModes['test']);
        if (Configuration::get('BILLPAY_CUSTOMER_GROUP') === false)
            Configuration::updateValue('BILLPAY_CUSTOMER_GROUP',   self::$billpayCustomerGroups['b2c']);
        if (Configuration::get('BILLPAY_FEE_INVOICE_B2C') === false)
            Configuration::updateValue('BILLPAY_FEE_INVOICE_B2C',  '');
        if (Configuration::get('BILLPAY_FEE_INVOICE_B2B') === false)
            Configuration::updateValue('BILLPAY_FEE_INVOICE_B2B',  '');
        if (Configuration::get('BILLPAY_FEE_DD') === false)
            Configuration::updateValue('BILLPAY_FEE_DD',           '');
    }

    /**
     *
     * @return boolean
     */
    private function _createBillpayApprovedOrderStatus()
    {
        if (Configuration::get('BILLPAY_APPROVED_ORDER_STATUS'))
            return true;

        $billpayApprovedOrderStatus = new OrderState();

        foreach (Language::getLanguages() as $language)
        {
            if (strtolower($language['iso_code']) == 'de')
                $billpayApprovedOrderStatus->name[$language['id_lang']] = 'Billpay angelegt';
            else
                $billpayApprovedOrderStatus->name[$language['id_lang']] = 'Billpay approved';
        }

        $billpayApprovedOrderStatus->color   = '#C5DBEC';
        $billpayApprovedOrderStatus->hidden  = false;
        $billpayApprovedOrderStatus->logable = true;

        $billpayApprovedOrderStatus->send_email = true;
        $billpayApprovedOrderStatus->invoice    = false;
        $billpayApprovedOrderStatus->delivery   = false;

        if ($billpayApprovedOrderStatus->add())
        {
            copy(_PS_MODULE_DIR_ . 'billpay/views/templates/backend/img/approved.gif',
            _PS_IMG_DIR_ . 'os/' . $billpayApprovedOrderStatus->id . '.gif'
                    );
        }

        Configuration::updateValue('BILLPAY_APPROVED_ORDER_STATUS', (int)$billpayApprovedOrderStatus->id);
    }


    /**
     *
     * @return boolean
     */
    private function _createBillpayActivatedOrderStatus()
    {
        if (Configuration::get('BILLPAY_ACTIVATED_ORDER_STATUS'))
            return true;

        $billpayActivatedOrderStatus = new OrderState();

        foreach (Language::getLanguages() as $language)
        {
            if (strtolower($language['iso_code']) == 'de')
                $billpayActivatedOrderStatus->name[$language['id_lang']] = 'Billpay aktiviert';
            else
                $billpayActivatedOrderStatus->name[$language['id_lang']] = 'Billpay activated';
        }

        $billpayActivatedOrderStatus->color   = '#B3CDE2';
        $billpayActivatedOrderStatus->hidden  = false;
        $billpayActivatedOrderStatus->logable = true;

        $billpayActivatedOrderStatus->send_email = false;
        $billpayActivatedOrderStatus->invoice    = true;
        $billpayActivatedOrderStatus->delivery   = false;

        if ($billpayActivatedOrderStatus->add())
        {
            copy(_PS_MODULE_DIR_ . 'billpay/views/templates/backend/img/activated.gif',
            _PS_IMG_DIR_ . 'os/' . $billpayActivatedOrderStatus->id . '.gif'
                    );
        }

        Configuration::updateValue('BILLPAY_ACTIVATED_ORDER_STATUS', (int)$billpayActivatedOrderStatus->id);
    }

    /**
     *
     * @return boolean
     */
    private function _createBillpayCanceldOrderStatus()
    {
        if (Configuration::get('BILLPAY_CANCELD_ORDER_STATUS'))
            return true;

        $billpayCanceldOrderStatus = new OrderState();

        foreach (Language::getLanguages() as $language)
        {
            if (strtolower($language['iso_code']) == 'de')
                $billpayCanceldOrderStatus->name[$language['id_lang']] = 'Billpay storniert';
            else
                $billpayCanceldOrderStatus->name[$language['id_lang']] = 'Billpay canceled';
        }

        $billpayCanceldOrderStatus->color   = '#88929A';
        $billpayCanceldOrderStatus->hidden  = false;
        $billpayCanceldOrderStatus->logable = true;

        $billpayCanceldOrderStatus->send_email = false;
        $billpayCanceldOrderStatus->invoice    = false;
        $billpayCanceldOrderStatus->delivery   = false;

        if ($billpayCanceldOrderStatus->add())
        {
            copy(_PS_MODULE_DIR_ . 'billpay/views/templates/backend/img/canceled.gif',
            _PS_IMG_DIR_ . 'os/' . $billpayCanceldOrderStatus->id . '.gif'
                    );
        }

        Configuration::updateValue('BILLPAY_CANCELD_ORDER_STATUS', (int)$billpayCanceldOrderStatus->id);
    }


    /**
     * Presta uninstall module function
     *
     * @return boolean
     */
    public function uninstall()
    {
        if (!parent::uninstall())
            return false;

        BillpayUtils::writeToLogFile($this->displayName. ' module uninstaled');

        return true;
    }


    /**
     * Presta getContent default function
     *
     * @return string rendered template output
     */
    public function getContent()
    {
        $settingsUpdateSuccessful = false;

        if (isset($_POST['submitBillpaySettings']))
        {
            $this->_setAndCheckConfigurationPostErrors();

            if (!sizeof($this->_postErrors))
            {
                $this->_updateBillpayConfigurationValues();
                $settingsUpdateSuccessful = true;
            }
        }

        global $smarty;
        $smarty->assign($this->_getBackendContentConfigurationTplValues($settingsUpdateSuccessful));

        // always use billpay module backend tpl for all three methods of payment ( Invoice, Direct Debit, Transaction Credit )
        return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/backend/billpay_configuration.tpl');
    }

    /**
     *
     * @return void
     */
    private function _setAndCheckConfigurationPostErrors()
    {
        if (empty($_POST['BILLPAY_MERCHANT_ID']))
            $this->_postErrors[] = $this->l('Please add your Billpay merchand ID', 'billpaypaymentmodule');
        if (empty($_POST['BILLPAY_PORTAL_ID']))
            $this->_postErrors[] = $this->l('Please add your Billpay portal ID', 'billpaypaymentmodule');
        if (empty($_POST['BILLPAY_API_PASSWORD']))
            $this->_postErrors[] = $this->l('Please add your Security Key', 'billpaypaymentmodule');
        if (empty($_POST['BILLPAY_API_TEST_URL']))
            $this->_postErrors[] = $this->l('Please add the API URL base test mode', 'billpaypaymentmodule');
        if (empty($_POST['BILLPAY_API_LIVE_URL']))
            $this->_postErrors[] = $this->l('Please add the API URL base live mode', 'billpaypaymentmodule');
    }

    /**
     *
     * @return void
     */
    private function _updateBillpayConfigurationValues()
    {
        Configuration::updateValue('BILLPAY_MERCHANT_ID',      $_POST['BILLPAY_MERCHANT_ID']);
        Configuration::updateValue('BILLPAY_PORTAL_ID',        $_POST['BILLPAY_PORTAL_ID']);
        Configuration::updateValue('BILLPAY_API_PASSWORD',     $_POST['BILLPAY_API_PASSWORD']);
        Configuration::updateValue('BILLPAY_API_TEST_URL',     $_POST['BILLPAY_API_TEST_URL']);
        Configuration::updateValue('BILLPAY_API_LIVE_URL',     $_POST['BILLPAY_API_LIVE_URL']);
        Configuration::updateValue('BILLPAY_TRANSACTION_MODE', $_POST['BILLPAY_TRANSACTION_MODE']);
        Configuration::updateValue('BILLPAY_CUSTOMER_GROUP',   $_POST['BILLPAY_CUSTOMER_GROUP']);
        Configuration::updateValue('BILLPAY_FEE_INVOICE_B2C',  $_POST['BILLPAY_FEE_INVOICE_B2C']);
        Configuration::updateValue('BILLPAY_FEE_INVOICE_B2B',  $_POST['BILLPAY_FEE_INVOICE_B2B']);
        Configuration::updateValue('BILLPAY_FEE_DD',           $_POST['BILLPAY_FEE_DD']);
    }

    /**
     *
     * @param boolean
     * @return array
     */
    private function _getBackendContentConfigurationTplValues($settingsUpdateSuccessful = false)
    {
        $BillpayConfigurationTplValues = array(
                'version'                  => $this->version,
                'name'                     => $this->name,
                'displayName'              => $this->displayName,
                'billpayTransactionModes'  => self::$billpayTransactionModes,
                'billpayCustomerGroups'    => self::$billpayCustomerGroups,
                'billpayConfiguration'     => array(
                        'BILLPAY_MERCHANT_ID'      => isset($_POST['BILLPAY_MERCHANT_ID'])      ? $_POST['BILLPAY_MERCHANT_ID']      : Configuration::get('BILLPAY_MERCHANT_ID'),
                        'BILLPAY_PORTAL_ID'        => isset($_POST['BILLPAY_PORTAL_ID'])        ? $_POST['BILLPAY_PORTAL_ID']        : Configuration::get('BILLPAY_PORTAL_ID'),
                        'BILLPAY_API_PASSWORD'     => isset($_POST['BILLPAY_API_PASSWORD'])     ? $_POST['BILLPAY_API_PASSWORD']     : Configuration::get('BILLPAY_API_PASSWORD'),
                        'BILLPAY_API_TEST_URL'     => isset($_POST['BILLPAY_API_TEST_URL'])     ? $_POST['BILLPAY_API_TEST_URL']     : Configuration::get('BILLPAY_API_TEST_URL'),
                        'BILLPAY_API_LIVE_URL'     => isset($_POST['BILLPAY_API_LIVE_URL'])     ? $_POST['BILLPAY_API_LIVE_URL']     : Configuration::get('BILLPAY_API_LIVE_URL'),
                        'BILLPAY_TRANSACTION_MODE' => isset($_POST['BILLPAY_TRANSACTION_MODE']) ? $_POST['BILLPAY_TRANSACTION_MODE'] : Configuration::get('BILLPAY_TRANSACTION_MODE'),
                        'BILLPAY_CUSTOMER_GROUP'   => isset($_POST['BILLPAY_CUSTOMER_GROUP'])   ? $_POST['BILLPAY_CUSTOMER_GROUP']   : Configuration::get('BILLPAY_CUSTOMER_GROUP'),
                        'BILLPAY_FEE_INVOICE_B2C'  => isset($_POST['BILLPAY_FEE_INVOICE_B2C'])  ? $_POST['BILLPAY_FEE_INVOICE_B2C']  : Configuration::get('BILLPAY_FEE_INVOICE_B2C'),
                        'BILLPAY_FEE_INVOICE_B2B'  => isset($_POST['BILLPAY_FEE_INVOICE_B2B'])  ? $_POST['BILLPAY_FEE_INVOICE_B2B']  : Configuration::get('BILLPAY_FEE_INVOICE_B2B'),
                        'BILLPAY_FEE_DD'           => isset($_POST['BILLPAY_FEE_DD'])           ? $_POST['BILLPAY_FEE_DD']           : Configuration::get('BILLPAY_FEE_DD'),
                ),
                'billpayPostErrors' => $this->_postErrors,
                'settingsUpdate'    => $settingsUpdateSuccessful,
        );

        return $BillpayConfigurationTplValues;
    }


    /**
     *
     * @return void
     */
    protected function _warningsCheck()
    {
        if (!Configuration::get('BILLPAY_MERCHANT_ID'))
        {
            $this->warning = $this->l('Please add your Billpay merchand ID', 'billpaypaymentmodule');
            return;
        }

        if (!Configuration::get('BILLPAY_PORTAL_ID'))
        {
            $this->warning = $this->l('Please add your Billpay portal ID', 'billpaypaymentmodule');
            return;
        }

        if (!Configuration::get('BILLPAY_API_PASSWORD'))
        {
            $this->warning = $this->l('Please add your Security Key', 'billpaypaymentmodule');
            return;
        }
    }


    /**
     *
     * @param array $params
     * @return void
     */
    public function hookPayment($params)
    {
        if (!$this->active)
            return false;

        if (!Configuration::get('BILLPAY_MERCHANT_ID') || !Configuration::get('BILLPAY_PORTAL_ID') || !Configuration::get('BILLPAY_API_PASSWORD'))
            return false;

        if (BillpaySessionUtils::hasSessionVar('billpayPreauthorizeRequestStatusDenied') === true)
            return false;

        if (BillpaySessionUtils::hasSessionVar('billpayModuleConfig') === false)
        {
            include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayModuleConfigRequest.php');
            $billpayConfigRequest = new BillpayModuleConfigRequest();
            $billpayConfigRequest->setBillpayModuleConfigurationRequest(self::_getCountryISOCode($params['cart']), BillpayGatherDataUtils::_getCurrencyBaseOnId($params['cart']->id_currency), self::_getLanguageISOCode($params['cookie']));

            if($billpayConfigRequest->sendBillpayModuleConfigurationRequest() !== false)
                BillpaySessionUtils::setSessionVar('billpayModuleConfig', $billpayConfigRequest->getBillpayModuleConfiguration());
            else
                return false;
        }

        if(BillpaySessionUtils::hasSessionVar('billpayModuleConfig') === true)
        {
            // store params for individual payments logic
            $this->_billpayModuleConfig        = BillpaySessionUtils::getSessionVar('billpayModuleConfig');
            $this->_billpayFormattedOrderTotal = BillpayUtils::getBillpayFormattedPrice($params['cart']->getOrderTotal(true));

            if (!$this->_billpayModuleConfig['active'])
            {
                BillpayUtils::writeToLogFile('--- Payments methods are inactive for this portal. Hiding ' . $this->displayName);
                return false;
            }
        }

        return true;
    }


    /**
     * @param object $cart
     * @return string|NULL
     */
    public static function _getCountryISOCode($cart)
    {
        $addressObject = new Address((int) ($cart->id_address_invoice));
        $currentCountryObject = new Country((int) $addressObject->id_country);

        return BillpayUtils::getCountryISOAlpha3CodeByISOAlpha2Code($currentCountryObject->iso_code);
    }

    /**
     *
     * @param object $cookie
     * @return string
     */
    protected static function _getLanguageISOCode($cookie)
    {
        $currentLanguageObject = new Language((int) $cookie->id_lang);
        return strtolower($currentLanguageObject->iso_code);
    }


    /**
     *
     * @param BillpayPreauthorizeRequest $billpayPreauthorizeRequest
     * @return boolean
     */
    public function sendBillpayPreauthorizeRequest($billpayPreauthorizeRequest)
    {
        $postSalutation = (isset($_POST['billpay_id_gender'])) ? $_POST['billpay_id_gender'] : '';
        $postBirthday   = (isset($_POST['billpay_birthday_year']) && isset($_POST['billpay_birthday_month']) && isset($_POST['billpay_birthday_day'])) ? $_POST['billpay_birthday_year'].$_POST['billpay_birthday_month'].$_POST['billpay_birthday_day'] : '';
        $customerGroup  = (isset($_POST['cutomer_group'])) ? $_POST['cutomer_group'] : 'p';

        // setup
        $billpayPreauthorizeRequest->setBillpayPreauthorizeRequestStartDetails((boolean) ($_POST['billpay_terms_checkbox']))
                                   ->setBillpayPreauthorizeRequestCustomerDetails($customerGroup, $postSalutation, $postBirthday)
                                   ->setBillpayPreauthorizeRequestShippingDetails()
                                   ->setBillpayPreauthorizeRequestTotal()
                                   ->setBillpayPreauthorizeRequestFraudDetection(BillpaySessionUtils::getMd5SessionIdAsProfilingTag())
                                   ->setBillpayPreauthorizeRequestAddArticles();

        // setup; extra company details values for invoice b2b
        if (isset($_POST['billpay_invoice']) && $customerGroup == 'b')
            $billpayPreauthorizeRequest->setBillpayPreauthorizeRequestCompanyDetails(trim($_POST['billpay_company_name']), $_POST['billpay_legal_status'], trim($_POST['billpay_registration_number']), trim($_POST['billpay_holder_name']), trim($_POST['billpay_tax_id']));

        // setup; extra bank acount details values for direct debit and transaction credit
        if(isset($_POST['direct_debit']) || isset($_POST['transaction_credit']))
            $billpayPreauthorizeRequest->setBillpayPreauthorizeRequestBankAccount(trim($_POST['billpay_account_holder']), $_POST['billpay_account_number'], trim($_POST['billpay_bank_code']));

        // setup; extra rate request details for transaction credit
        if(isset($_POST['transaction_credit']))
            $billpayPreauthorizeRequest->setBillpayPreauthorizeRequestRate((int) trim($_POST['tcr']), (float) trim($_POST['tct']));

        // send
        if($billpayPreauthorizeRequest->sendBillpayPreauthorizeRequest() === false) // Exception $e handel
            return false;

        // status response
        if($billpayPreauthorizeRequest->getBillpayPreauthorizeRequestStatus() === false)
        {
            // DENIED, hide all billpay payments
            BillpaySessionUtils::setSessionVar('billpayPreauthorizeRequestStatusDenied', true);
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
        }

        // error check
        if($billpayPreauthorizeRequest->checkBillpayPreauthorizeRequestHasError() === false)
        {
            BillpaySessionUtils::setSessionVar('billpayRequestError', $billpayPreauthorizeRequest->getBillpayPreauthorizeRequestCustomerErrorMessage());
            return false;
        }

        // store results
        $this->_billpayPreauthorizeRequestResult = $billpayPreauthorizeRequest->getBillpayPreauthorizeRequestResults();

        // store bptid
        $this->_billpayPreauthorizeRequestBptid = $billpayPreauthorizeRequest->getBillpayPreauthorizeRequestBptid();

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function saveBillpayPreauthorizeRequestResultsToDb()
    {
        if(BillpayDbUtils::insertBankDataResultsToBillpayTable((string) $this->currentOrder, $this->_billpayPreauthorizeRequestResult) === false)
        {
            BillpayUtils::writeToLogFile('--- Billpay preauthorize request results NOT SUCCESSFULLY saved to `' . _DB_PREFIX_ . 'billpay` table', 1);
            return false;
        }

        BillpayUtils::writeToLogFile('--- Billpay preauthorize request results saved successfully to `' . _DB_PREFIX_ . 'billpay` table');

        return true;
    }

    /**
     *
     * @param object $cart
     * @return void
     */
    public function validateOrder($cart, $extraVars = array())
    {
        $total = $cart->getOrderTotal(true);
        $customer = new Customer($cart->id_customer);

        parent::validateOrder(
                (int)$cart->id,
                Configuration::get('BILLPAY_APPROVED_ORDER_STATUS'),
                $total,
                $this->displayName,
                $this->l('Payed with:', 'billpaypaymentmodule'). ' ' . $this->displayName, // replace this with a real msg if necesary
                $extraVars,
                null,
                false,
                $customer->secure_key
        );
    }

    /**
     *
     * @return boolean
     */
    public function updateBillpayOrderReferenceRequest()
    {
        include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayUpdateOrderRequest.php');

        $billpayUpdateOrderRequest = new BillpayUpdateOrderRequest();
        $billpayUpdateOrderRequest->setBillpayUpdateOrderRequest($this->_billpayPreauthorizeRequestBptid, (string) $this->currentOrder);

        if ($billpayUpdateOrderRequest->sendBillpayUpdateOrderRequest() === false)
            return false;

        return true;
    }

    /**
     *
     * @param object $cart
     * @return void
     */
    public function redirectToOrderConfirmation($cart)
    {
        $customer = new Customer($cart->id_customer);
        Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key=' . $customer->secure_key
                . '&id_cart='.(int)($cart->id)
                . '&id_module='.(int)($this->id)
                . '&id_order='.(int)($this->currentOrder)
        );
    }

    /**
     *
     * @param array $params
     * @return void
     */
    public function hookUpdateOrderStatus($params)
    {
        $order = new Order((int) $params['id_order']);

        if (!Validate::isLoadedObject($order) || $order->module != $this->name)
            return;

        if($params['newOrderStatus']->id == (int)Configuration::get('BILLPAY_ACTIVATED_ORDER_STATUS'))
            $this->_executeBillpayActivateOrderStatus($order);

        if($params['newOrderStatus']->id == (int)Configuration::get('BILLPAY_CANCELD_ORDER_STATUS'))
            $this->_executeBillpayCanceldOrderStatus($order);
    }

    /**
     *
     * @param array $params
     * @param Order $order
     * @return void
     */
    protected function _executeBillpayActivateOrderStatus($order)
    {
        // activation already performed
        $billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable(($order->id));
        if ((boolean) $billpayBankData["activation_performed"] === true)
        {
            BillpayUtils::writeToLogFile('--- Activation already done don\'t send another activation xml request. order: '. $order->id);
            return;
        }

        // request logic
        $productsWithRefundedQuantity = $this->_getProductsWithRefundedAndNotRefundedQuantity($order);

        if ($this->_checkProductsWithRefundedQuantityExist($order))
        {
            $productsWithRefundedQuantity = $this->_addDiscountToProductsPriceIfPrezent($productsWithRefundedQuantity, $order);
            $invoiceCreatedRequestResult  = $this->_executeBillpayInvoiceCreatedRequest($order, $order->getProducts($productsWithRefundedQuantity)); // use order get products to recalculate the prodcts with the new quantity
        }
        else
        {
            $invoiceCreatedRequestResult = $this->_executeBillpayInvoiceCreatedRequest($order); // no refounded products send without products info
        }
            
        // db logic
        if ($this->name == 'billpaytransactioncredit')
        	$updateBankDataResultsToBillpayTable = BillpayDbUtils::updateBankDataResultsToBillpayTableForTransactionCredit($order->id, $invoiceCreatedRequestResult);
        else
            $updateBankDataResultsToBillpayTable = BillpayDbUtils::updateBankDataResultsToBillpayTable($order->id, $invoiceCreatedRequestResult);
        
        if($updateBankDataResultsToBillpayTable === false)
        {
            BillpayUtils::writeToLogFile('--- Billpay invoice created results NOT SUCCESSFULLY saved to `' . _DB_PREFIX_ . 'billpay` table. Reference:'. $order->id, 1);
            die(Tools::displayError('<fieldset><strong><span style=\'color: red;\'>' .
                                        $this->l('Problems activating this order with Billpay.', 'billpaypaymentmodule')).
                                    '</span></strong></fieldset>');
        }

        BillpayUtils::writeToLogFile('--- Billpay invoice created results saved successfully to `' . _DB_PREFIX_ . 'billpay` table. Reference:'. $order->id);
    }

    /**
     *
     * @param Order $order
     * @return void
     */
    protected function _executeBillpayCanceldOrderStatus($order)
    {
        $productsWithRefundedQuantity = $this->_getProductsWithRefundedAndNotRefundedQuantity($order);

        if ($this->_checkProductsWithRefundedQuantityExist($order))
        {
            $productsWithRefundedQuantity = $this->_addDiscountToProductsPriceIfPrezent($productsWithRefundedQuantity, $order);
            $this->_executeBillpayCancelRequest($order, $order->getProducts($productsWithRefundedQuantity)); // use order get products to recalculate the prodcts with the new quantity
        }
        else
        {
            $this->_executeBillpayCancelRequest($order); // no refounded products send without products info
        }
    }

    /**
     *
     * @param array $params
     * @return void
     */
    public function hookCancelProduct($params)
    {
        if (!isset($_POST['cancelProduct']))
            return;

        $order       = $params['order'];
        $orderDetail = new OrderDetail((int) (int)$params['id_order_detail'] );

        if (!Validate::isLoadedObject($order) || !Validate::isLoadedObject($orderDetail) || $order->module != $this->name)
            return;

        /*
         * hookCancelProduct hook is run in a foreach
         */
        $this->_hookCancelProductCount++;
        /*
         * !!! Last foreach loop when all products have updated product_quantity values
         */
        if ($this->_hookCancelProductCount == count(Tools::getValue('id_order_detail'))){

            foreach ($order->getProducts() as $key => $product){
                $productsQuantity[$key] = (int) $product['product_quantity'] - (int) $product['product_quantity_refunded']; // count new product quantity
            }

            $tmp = array_filter($productsQuantity);
            if (empty($tmp)) // use empty array_filter to check if all products quantity is 0
            {
                $cancel_quantity = Tools::getValue('cancelQuantity');
                $products = $order->getProducts();

                foreach ($cancel_quantity as $key => $cancelValue)
                {
                    $canceledProducts[$key] = $products[$key];
                    $canceledProducts[$key]['product_quantity'] = $cancelValue;
                }

                $canceledProducts = $this->_addDiscountToProductsPriceIfPrezent($canceledProducts, $order);
                $this->_executeBillpayCancelRequest($order, $order->getProducts($canceledProducts)); // use order get products to recalculate the prodcts with the new quantity
            }
            else
            {
                $productsWithRefundedQuantity = $this->_getProductsWithRefundedAndNotRefundedQuantity($order);
                $productsWithRefundedQuantity = $this->_addDiscountToProductsPriceIfPrezent($productsWithRefundedQuantity, $order);
                $this->_executeBillpayEditCartContent($order, $order->getProducts($productsWithRefundedQuantity)); // use order get products to recalculate the prodcts with the new quantity
            }
        }

        return;
    }

    /**
     *
     * @param Order $order
     * @param array $products
     * @return array
     */
    protected function _executeBillpayInvoiceCreatedRequest($order, $products = array())
    {
        include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayInvoiceCreatedRequest.php');

        $billpayInvoiceCreatedRequest = new BillpayInvoiceCreatedRequest();
        $billpayInvoiceCreatedRequest->setBillpayInvoiceCreatedRequest($order, $products);

        if ($billpayInvoiceCreatedRequest->sendBillpayInvoiceCreatedRequest() === false)
            die(Tools::displayError('<fieldset><strong><span style=\'color: red;\'>' .
                                        $this->l('Problems activating this order with Billpay.', 'billpaypaymentmodule')). ' <br /> '. $billpayInvoiceCreatedRequest->getBillpayInvoiceCreatedRequestMerchantErrorMessage().
                                    '</span></strong></fieldset>');

        return $billpayInvoiceCreatedRequest->getBillpayInvoiceCreatedRequestResults($this->name, (int) $order->id);
    }

    /**
     *
     * @param Order $order
     * @param $products array
     * @return void
     */
    protected function _executeBillpayCancelRequest($order, $products = array())
    {
        include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayCancelRequest.php');

        $billpayCancelRequest = new BillpayCancelRequest();
        $billpayCancelRequest->setBillpayCancelParamsRequest($order, $products);

        if ($billpayCancelRequest->sendBillpayCancelRequest() === false)
            die(Tools::displayError('<fieldset><strong><span style=\'color: red;\'>' .
                                        $this->l('There was an error! Please contact Billpay.', 'billpaypaymentmodule')). ' <br /> '. $billpayCancelRequest->getBillpayCancelRequestMerchantErrorMessage().
                                    '</span></strong></fieldset>');
    }

    /**
     *
     * @param Order $order
     * @param array $products
     * @return void
     */
    protected function _executeBillpayEditCartContent($order, $products)
    {
        include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayEditCartContentRequest.php');

        $billpayEditCartContentRequest = new BillpayEditCartContentRequest();
        $billpayEditCartContentRequest->setEditCartContentRequestDefault();

        $billpayEditCartContentRequest->setEditCartContentRequestAddArticles($products, $order)
                                      ->setEditCartContentRequestTotal($order, $products);

        if ($billpayEditCartContentRequest->sendBillpayEditCartContentRequest() === false)
            die(Tools::displayError('<fieldset><strong><span style=\'color: red;\'>' .
                                        $this->l('There was an error! Please contact Billpay.', 'billpaypaymentmodule')). ' <br /> '. $billpayEditCartContentRequest->getBillpayEditCartContentRequestMerchantErrorMessage().
                                    '</span></strong></fieldset>');

        if($this->name == 'billpaytransactioncredit')
        {
            $updatedTransactionCreditData = $billpayEditCartContentRequest->getUpdatedTransactionCreditDataFromEditCartContentRequest();

            // overwrite dues from the db with the news values
            if(BillpayDbUtils::updateTransactionCreditDataToBillpayTable((string) $order->id, serialize($updatedTransactionCreditData)) === false)
                    die(Tools::displayError('<fieldset><strong><span style=\'color: red;\'>' .
                            $this->l('There was an error! Please contact Billpay.', 'billpaypaymentmodule')). ' <br /> '. $billpayEditCartContentRequest->getBillpayEditCartContentRequestMerchantErrorMessage().
                            '</span></strong></fieldset>');
        }
    }

    /**
     *
     * @param Order $order
     * @return array
     */
    private function _getProductsWithRefundedAndNotRefundedQuantity($order)
    {
        $productsWithRefundedAndNotRefundedQuantity = array();
        foreach ($order->getProducts() as $key => $product)
        {
            $productQuantity = (int) $product['product_quantity'] - (int) $product['product_quantity_refunded'];
            $productsWithRefundedAndNotRefundedQuantity[$key] = $product;
            $productsWithRefundedAndNotRefundedQuantity[$key]['product_quantity'] = $productQuantity;
        }

        return $productsWithRefundedAndNotRefundedQuantity;
    }

    /**
     *
     * @param Order $order
     * @return boolean
     */
    private function _checkProductsWithRefundedQuantityExist($order)
    {
        $productsWithRefundedQuantity = false;
        foreach ($order->getProducts() as $key => $product)
        {
            if ((int) $product['product_quantity_refunded'] > 0)
                $productsWithRefundedQuantity = true;
        }

        return $productsWithRefundedQuantity;
    }

    /**
     *
     * @param array $products
     * @param Order $order
     * @return array
     */
    private function _addDiscountToProductsPriceIfPrezent($products, $order)
    {
        $orderDiscounts = $order->getDiscounts(true);

        if (sizeof($orderDiscounts))
        {
            foreach ($products as $key => $product)
            {
                //$order->setProductPrices($product);
                $realProductPrice = $product['product_price'];
                $products[$key] = $product;
                foreach ($orderDiscounts as $discount)
                {
                    if ($discount['id_discount_type'] == 1)
                        $products[$key]['product_price'] -= $realProductPrice * ($discount['value'] / 100);

                    elseif ($discount['id_discount_type'] == 2)
                        $products[$key]['product_price'] -= (($discount['value'] * ($product['product_price_wt'] / $order->total_products_wt)) / (1.00 + ($product['tax_rate'] / 100)));
                }
            }
        }

        return $products;
    }
}
