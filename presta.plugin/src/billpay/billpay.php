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

// Security reason
if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayPaymentModule.php');

class Billpay extends BillpayPaymentModule
{
    /**
     * Class constructor. Initiates parent constructor.
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = 'billpay';

        $this->limited_countries = array('de', 'at', 'ch');

        parent::__construct(); // The parent construct is required for translations

        $this->page = basename(__FILE__, '.php');
        $this->displayName  = $this->l('Billpay invoice');
        $this->description  = $this->l('Billpay is the payment for the successful e-commerce expert. With Billpay online merchants are able to offer their customers payment by invoice without any risk.');
        $this->description .= '<br>';
        $this->description .= '<span style="color: #CB1C00;"><sup>*</sup>&nbsp;' . $this->l('Do not delete this module!'). '</span>';

        $this->confirmUninstall = $this->l('Are you sure you want to uninstal this module?');

        $this->_warningsCheck();
    }

    /**
     * @see BillpayPaymentModule::install()
     *
     * @return boolean
     */
    public function install()
    {
        if (!parent::install())
            return false;

        BillpayUtils::writeToLogFile($this->displayName. ' module installed');

        return true;
    }

    /**
     *
     * @param array $params
     * @return string rendered template output
     */
    public function hookPayment($params)
    {
    	if (!parent::hookPayment($params))
            return false;

        if(!$this->_billpayModuleConfig['invoice_allowed'] && !$this->_billpayModuleConfig['invoicebusiness_allowed'])
        {
            BillpayUtils::writeToLogFile('--- Invoice payments are inactive for this portal. Hiding ' . $this->displayName);
            return false;
        }

        if ($this->_billpayFormattedOrderTotal < $this->_billpayModuleConfig['invoice_min_value'] && $this->_billpayFormattedOrderTotal < $this->_billpayModuleConfig['invoicebusiness_min_value'])
        {
            BillpayUtils::writeToLogFile('--- Minimum value not reached for Invoice payments ('. $this->_billpayFormattedOrderTotal .' < '. $this->_billpayModuleConfig['invoice_min_value'].') && ('. $this->_billpayFormattedOrderTotal .' < '. $this->_billpayModuleConfig['invoicebusiness_min_value'].') Hiding ' . $this->displayName);
            return false;
        }

        if ($this->_billpayFormattedOrderTotal > $this->_billpayModuleConfig['static_limit_invoice'] && $this->_billpayFormattedOrderTotal > $this->_billpayModuleConfig['static_limit_invoicebusiness'])
        {
            BillpayUtils::writeToLogFile('--- Static limit exceeded for Invoice payments ('. $this->_billpayFormattedOrderTotal .' > '. $this->_billpayModuleConfig['invoice_min_value'].') && ('. $this->_billpayFormattedOrderTotal .' > '. $this->_billpayModuleConfig['static_limit_invoicebusiness'].') Hiding ' . $this->displayName);
            return false;
        }

        global $smarty;
        $smarty->assign(array(
                'BILLPAY_CUSTOMER_GROUP' => Configuration::get('BILLPAY_CUSTOMER_GROUP'),
                'billpayCustomerGroups'  => parent::$billpayCustomerGroups,
                'extraBillpayFeeB2C'     => BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('INVOICE_B2C'),
                'extraBillpayFeeB2B'     => BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('INVOICE_B2B'),
        ));

        return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/frontend/billpay_payment.tpl');
    }

    /**
     *
     * @param object $cart
     * @return void
     */
    public function validateOrder($cart)
    {
        $extra_vars = array();

        /*
         * email html block
         */
        global $smarty;
        $smarty->assign(array(
                'displayName'        => $this->displayName,
                'billpayBankData'    => $this->_billpayPreauthorizeRequestResult,
        ));
        $extra_vars['{billpay_order_conf_transaction_credit_totals_mail_block}'] = '';
        $extra_vars['{billpay_order_conf_mail_block}'] = $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/mail/billpay_order_conf.tpl');

        /*
         * email text block
         */
        $extra_vars['{billpay_order_conf_mail_block_text}']  = "\n\n" . $this->displayName. "\n\n";
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= $this->l('Please transfer the total amount with transaction number (received within the invoice) as reason for payment until (date received within the invoice) to the following account:');
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\n" . $this->l('Account holder:') . " ". $this->_billpayPreauthorizeRequestResult["account_holder"];
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Bank name:') . " " . $this->_billpayPreauthorizeRequestResult["bank_name"];
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Bank code:') . " " . $this->_billpayPreauthorizeRequestResult["bank_code"];
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Account number:') . " ". $this->_billpayPreauthorizeRequestResult["account_number"];
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Due date:') . " (". $this->l('received within the invoice'). ")";
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Reason for payment:') . " (". $this->l('received within the invoice'). ")";

        parent::validateOrder($cart, $extra_vars);
    }

    /**
     *
     * @return string rendered template output
     */
    public function submitPayment()
    {
        if (!$this->active)
            return ;

        BillpaySessionUtils::setSessionBillpayProfilingTagCount();
        BillpaySessionUtils::setSessionMd5IdToTplSessionId();

        //FIXME submit on the same page and then remove this precare hidden logic
        if(isset($_POST['billpay_submit'])){
            // clear all old _billpaySubmitErrors errors before a new validation
            BillpaySessionUtils::deleteSessionVar('billpaySubmitErrors');
            BillpaySessionUtils::deleteSessionVar('billpayRequestError');
        }

        global $smarty;

        // common B2C and B2B values
        $smarty->assign(array(
                'displayName'                 => $this->displayName,
                '_PS_MODULE_DIR_'             => _PS_MODULE_DIR_,
                'BILLPAY_TRANSACTION_MODE'    => Configuration::get('BILLPAY_TRANSACTION_MODE'),
                'billpayTransactionModesTest' => parent::$billpayTransactionModes['test'],
                'this_path_ssl'               => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/billpay/',
                'datenschutzLink'             => BillpayUtils::getDatenschutzLinkForCountryWithLanguage(),
                'customerSalutation'          => BillpayGatherDataUtils::getCurrentCustomerSalutation(),
                'invoiceAddressCountryISO'    => BillpayGatherDataUtils::getInvoiceAddressCountryISOCode(),
                ));

        /* B2C */
        if (Configuration::get('BILLPAY_CUSTOMER_GROUP') == parent::$billpayCustomerGroups['b2c']){
            self::_assignB2CValuesToSmarty();
            return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/frontend/billpay_payment_invoice_b2c.tpl');
        }

        /* B2B */
        if (Configuration::get('BILLPAY_CUSTOMER_GROUP') == parent::$billpayCustomerGroups['b2b']){
            self::_assignB2bValuesToSmarty();
            return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/frontend/billpay_payment_invoice_b2b.tpl');

        }

        /* BOTH */
        if (Configuration::get('BILLPAY_CUSTOMER_GROUP') == parent::$billpayCustomerGroups['both']){
            self::_assignB2CValuesToSmarty();
            self::_assignB2bValuesToSmarty();
            return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/frontend/billpay_payment_invoice.tpl');
        }
    }

    /**
     *
     * @return void
     */
    private static function _assignB2CValuesToSmarty()
    {
        global $smarty, $cart;

        $smarty->assign(array(
                'extraBillpayFeeB2C'     => BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('INVOICE_B2C'),
                'totalWithBillpayFeeB2C' => BillpayUtils::getOrderTotalWithExtraBillpayPaymentFee($cart->getOrderTotal(true), BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('INVOICE_B2C')),
                'customerBirthday'       => BillpayGatherDataUtils::getCurrentCustomerBirthday(),
                'years'                  => BillpayUtils::billpayDateYears(),
                'months'                 => BillpayUtils::billpayDateMonths(),
                'days'                   => BillpayUtils::billpayDateDays(),
        ));
    }

    /**
     *
     * @return void
     */
    private static function _assignB2BValuesToSmarty()
    {
        global $smarty, $cart;

        $smarty->assign(array(
                'extraBillpayFeeB2B'        => BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('INVOICE_B2B'),
                'totalWithBillpayFeeB2B'    => BillpayUtils::getOrderTotalWithExtraBillpayPaymentFee($cart->getOrderTotal(true), BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('INVOICE_B2B')),
                'invoiceAddressCompanyDni'  => BillpayGatherDataUtils::getInvoiceAddressCompanyDni(),
                'invoiceAddressCompanyName' => BillpayGatherDataUtils::getInvoiceAddressCompanyName(),
                'currentCustomerName'       => BillpayGatherDataUtils::getInvoiceAddressFirstName(). ' '. BillpayGatherDataUtils::getInvoiceAddressLastName(),
        ));
    }

    /**
     *
     * @return boolean
     */
    public function billpayValidatePayment()
    {
        if ($_POST['cutomer_group'] == 'p')
            $this->_checkAndSetErrorsBillpayValidatePaymentB2C();
        if ($_POST['cutomer_group'] == 'b')
            $this->_checkAndSetErrorsBillpayValidatePaymentB2B();

        //workaround send cutomer_group from post to session, used in the billpay_payment_invoice.tpl to set input radio as checked
        //TODO better post logic if the validation will be changed in the same submit form
        BillpaySessionUtils::setSessionVar('billpayCutomerGroup', $_POST['cutomer_group']);

        if($this->_billpaySubmitErrors){
            BillpaySessionUtils::setSessionVar('billpaySubmitErrors',$this->_billpaySubmitErrors);
            return false;
        }

        return true;
    }

    /**
     *
     * @return void
     */
    private function _checkAndSetErrorsBillpayValidatePaymentB2C()
    {
        if(!$_POST['billpay_terms_checkbox'])
            $this->_billpaySubmitErrors[] = $this->l('You must agree to the terms of service before continuing');

        if(!BillpayGatherDataUtils::getCurrentCustomerSalutation() && empty($_POST['billpay_id_gender']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your title');

        if(!BillpayGatherDataUtils::getCurrentCustomerBirthday() && (empty($_POST['billpay_birthday_day']) || empty($_POST['billpay_birthday_month']) || empty($_POST['billpay_birthday_year'])))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your date of birth');
    }

    /**
     *
     * @return void
     */
    private function _checkAndSetErrorsBillpayValidatePaymentB2B()
    {
        if(!$_POST['billpay_terms_checkbox'])
            $this->_billpaySubmitErrors[] = $this->l('You must agree to the terms of service before continuing');

        if(!trim($_POST['billpay_company_name']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your company name');

        if(!trim($_POST['billpay_legal_status']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your legal status');

        if(!trim($_POST['billpay_registration_number']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your registration number');

        if(!BillpayGatherDataUtils::getCurrentCustomerSalutation() && empty($_POST['billpay_id_gender']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your title');
    }

    /**
     *
     * @see BillpayPaymentModule::sendBillpayPreauthorizeRequest()
     */
    public function sendBillpayPreauthorizeRequest()
    {
        include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayPreauthorizeRequest.php');

        if (!parent::sendBillpayPreauthorizeRequest(new BillpayPreauthorizeRequest(IPL_CORE_PAYMENT_TYPE_INVOICE)))
            return false;

        return true;
    }

    /**
     *
     * @param array $params
     * @return string rendered template output
     */
    public function hookPaymentReturn($params)
    {
    	if (!$this->active)
    		return ;

    	$billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['objOrder']->id);


    	// the order is not yet activated build manually the invoice_reference
    	$billpayBankData['invoice_reference'] = BillpayUtils::createBillpayInvoiceRefernece($params['objOrder']->id, Configuration::get('BILLPAY_MERCHANT_ID'));

    	global $smarty;
    	$smarty->assign(array(
    			'displayName' => $this->displayName,
    			'billpayBankData' => $billpayBankData,

    	));

    	return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/frontend/billpay_confirmation.tpl');
    }

    /**
     *
     * @param array $params
     * @return string rendered template output
     */
    public function hookOrderDetailDisplayed($params)
    {
       if ($params['order']->module != $this->name)
    		return false;

    	$billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['order']->id);

    	if($billpayBankData === false)
    		return '<p class="bold" style="margin-top: 10px; clear: both; background-color: white;"><span class="color-myaccount">' . $this->l('Error loading transaction data information !') . '</span></p>';

    	// in case the order is not yet activated build manually the invoice_reference
        if (strlen($billpayBankData['invoice_reference']) > 13)
            $billpayBankData['invoice_reference'] = BillpayUtils::createBillpayInvoiceRefernece($params['order']->id, Configuration::get('BILLPAY_MERCHANT_ID'));

    	global $smarty;
    	$smarty->assign(array(
    			'displayName'           => $this->displayName,
                'billpayBankData'       => $billpayBankData,
                'billpayInvoiceDuedate' => BillpayUtils::parseBillpayInvoiceDuedateToReadableFromat($billpayBankData["invoice_duedate"]),
    	));

    	return $smarty->fetch(_PS_MODULE_DIR_. $this->name .'/views/templates/frontend/billpay_order_detail.tpl');
    }

    /**
     *
     * @param array $params
     * @return string rendered template output
     */
    public function hookAdminOrder($params)
    {
        $order = new Order((int) $params['id_order']);

        if (!Validate::isLoadedObject($order) || $order->module != $this->name)
            return false;

        $billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['id_order']);

        // in case the order is not yet activated build manually the invoice_reference
        if (strlen($billpayBankData['invoice_reference']) > 13)
            $billpayBankData['invoice_reference'] = BillpayUtils::createBillpayInvoiceRefernece($params['id_order'], Configuration::get('BILLPAY_MERCHANT_ID'));

        global $smarty;
        $smarty->assign(array(
                'displayName'           => $this->displayName,
                'billpayBankData'       => $billpayBankData,
                'billpayInvoiceDuedate' => BillpayUtils::parseBillpayInvoiceDuedateToReadableFromat($billpayBankData["invoice_duedate"]),
        ));
        return $smarty->fetch(_PS_MODULE_DIR_. 'billpay/views/templates/backend/billpay_information.tpl');
    }

    /**
     *
     * @param array $params
     * @return void
     */
    public function hookPDFInvoice($params)
    {
        $order = new Order((int) $params['id_order']);

        if (!Validate::isLoadedObject($order) || $order->module != $this->name)
            return;

        $billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['id_order']);

        // in case the order is not yet activated build manually the invoice_reference
        if (strlen($billpayBankData['invoice_reference']) > 13)
            $billpayBankData['invoice_reference'] = BillpayUtils::createBillpayInvoiceRefernece($params['id_order'], Configuration::get('BILLPAY_MERCHANT_ID'));


        // fill
        $params['pdf']->Ln(10);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 10);
        $params['pdf']->Cell(190, 55, '', 1, 0, 'L', true);

        // payment name
        $params['pdf']->Ln(5);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 10);
        $params['pdf']->Cell(50, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->displayName), 0, 0, 'L');

        // text
        $params['pdf']->SetFont($params['pdf']::fontname(), '', 8);

        $params['pdf']->Ln(8);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Please transfer the total amount with the billpay transaction number'));
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        $params['pdf']->Ln(4);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('as reason for payment ( %s ) until %s to the following account:'));
        $params['pdf']->Cell(150, 0, sprintf($text, $billpayBankData['invoice_reference'], BillpayUtils::parseBillpayInvoiceDuedateToReadableFromat($billpayBankData['invoice_duedate'])), 0, 0, 'L');


        // colums
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 8);

        $params['pdf']->Ln(6);
        $params['pdf']->Cell(30, 0, Tools::iconv('utf-8', $params['pdf']::encoding(),$this->l('Account holder:')), 0, 0, 'L');
        $params['pdf']->Cell(0, 0, $billpayBankData['account_holder'], 0, 0, 'L');

        $params['pdf']->Ln(5);
        $params['pdf']->Cell(30, 0, Tools::iconv('utf-8', $params['pdf']::encoding(),$this->l('Bank name:')), 0, 0, 'L');
        $params['pdf']->Cell(0, 0, $billpayBankData['bank_name'], 0, 0, 'L');

        $params['pdf']->Ln(5);
        $params['pdf']->Cell(30, 0, Tools::iconv('utf-8', $params['pdf']::encoding(),$this->l('Bank code:')), 0, 0, 'L');
        $params['pdf']->Cell(0, 0, $billpayBankData['bank_code'], 0, 0, 'L');

        $params['pdf']->Ln(5);
        $params['pdf']->Cell(30, 0, Tools::iconv('utf-8', $params['pdf']::encoding(),$this->l('Account number:')), 0, 0, 'L');
        $params['pdf']->Cell(0, 0, $billpayBankData['account_number'], 0, 0, 'L');

        $params['pdf']->Ln(5);
        $params['pdf']->Cell(30, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Due date:')), 0, 0, 'L');
        $params['pdf']->Cell(0, 0, BillpayUtils::parseBillpayInvoiceDuedateToReadableFromat($billpayBankData['invoice_duedate']), 0, 0, 'L');

        $params['pdf']->Ln(5);
        $params['pdf']->Cell(30, 0, Tools::iconv('utf-8', $params['pdf']::encoding(),$this->l('Invoice reference:')), 0, 0, 'L');
        $params['pdf']->Cell(0, 0, $billpayBankData['invoice_reference'], 0, 0, 'L');
    }
}