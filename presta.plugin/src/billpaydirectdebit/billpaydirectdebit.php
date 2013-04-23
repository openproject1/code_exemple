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

if(!@include_once (_PS_MODULE_DIR_ . 'billpay/classes/BillpayPaymentModule.php')){
    Module::disableByName('billpaydirectdebit');
    return;
}

class BillpayDirectDebit extends BillpayPaymentModule
{
    /**
     * Class constructor. Initiates parent constructor.
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = 'billpaydirectdebit';

        $this->limited_countries = array('de');

        parent::__construct(); // The parent construct is required for translations

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Billpay direct debit');
        $this->description = $this->l('Billpay is the payment for the successful e-commerce expert. With Billpay online merchants are able to offer their customers payment via direct debit - and all without any risk.');
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

        if(!$this->_billpayModuleConfig['direct_debit_allowed'])
        {
            BillpayUtils::writeToLogFile('--- Direct Debit payment is inactive for this portal. Hiding ' . $this->displayName);
            return false;
        }

        if ($this->_billpayFormattedOrderTotal < $this->_billpayModuleConfig['direct_debit_min_value'])
        {
            BillpayUtils::writeToLogFile('--- Minimum value not reached for direct debit payments ('. $this->_billpayFormattedOrderTotal .' < '. $this->_billpayModuleConfig['direct_debit_min_value'].') Hiding ' . $this->displayName);
            return false;
        }

        if ($this->_billpayFormattedOrderTotal > $this->_billpayModuleConfig['static_limit_direct_debit'])
        {
            BillpayUtils::writeToLogFile('--- Static limit exceeded for direct debit payments ('. $this->_billpayFormattedOrderTotal .' > '. $this->_billpayModuleConfig['static_limit_direct_debit'].') Hiding ' . $this->displayName);
            return false;
        }

        global $smarty;
        $smarty->assign(array(
            'extraBillpayFeeDD'    => BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('DD'),
        ));

        return $smarty->fetch(_PS_MODULE_DIR_. 'billpaydirectdebit/views/templates/frontend/billpay_payment.tpl');
    }

    /**
     *
     * @param object $cart
     * @return void
     */
    public function validateOrder($cart)
    {
        /*
         * email html block
         */
        global $smarty;
        $smarty->assign(array(
                'displayName' => $this->displayName,
        ));
        $extra_vars['{billpay_order_conf_transaction_credit_totals_mail_block}'] = '';
        $extra_vars['{billpay_order_conf_mail_block}'] = $smarty->fetch(_PS_MODULE_DIR_. 'billpaydirectdebit/views/templates/mail/billpay_order_conf.tpl');

        /*
         * email text block
         */
        $extra_vars['{billpay_order_conf_mail_block_text}'] = "\n\n" . $this->displayName . "\n\n" . $this->l('Within the next few days, we will withdraw the amount due from the bank account supplied when placing the order.');

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

        global $smarty, $cart;

        $smarty->assign(array(
                'displayName'                 => $this->displayName,
                '_PS_MODULE_DIR_'             => _PS_MODULE_DIR_,
                'BILLPAY_TRANSACTION_MODE'    => Configuration::get('BILLPAY_TRANSACTION_MODE'),
                'billpayTransactionModesTest' => parent::$billpayTransactionModes['test'],
                'extraBillpayFeeDD'           => BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('DD'),
                'datenschutzLink'             => BillpayUtils::getDatenschutzLinkForCountryWithLanguage(),
                'customerBirthday'            => BillpayGatherDataUtils::getCurrentCustomerBirthday(),
                'customerSalutation'          => BillpayGatherDataUtils::getCurrentCustomerSalutation(),
                'currentCustomerName'         => BillpayGatherDataUtils::getInvoiceAddressFirstName() . ' ' . BillpayGatherDataUtils::getInvoiceAddressLastName(),
                'years'                       => BillpayUtils::billpayDateYears(),
                'months'                      => BillpayUtils::billpayDateMonths(),
                'days'                        => BillpayUtils::billpayDateDays(),
                'totalWithBillpayFee'         => BillpayUtils::getOrderTotalWithExtraBillpayPaymentFee($cart->getOrderTotal(true), BillpayUtils::getExtraBillpayFeeBaseOnShopCurrency('DD')),
        ));

        return $smarty->fetch(_PS_MODULE_DIR_. 'billpaydirectdebit/views/templates/frontend/billpay_payment_directdebit.tpl');
    }

    /**
     *
     * @return boolean
     */
    public function billpayValidatePayment()
    {
        $this->_checkAndSetErrorsBillpayValidatePaymentDirectDebit();

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
    private function _checkAndSetErrorsBillpayValidatePaymentDirectDebit()
    {
        if(!$_POST['billpay_terms_checkbox'])
            $this->_billpaySubmitErrors[] = $this->l('You must agree to the terms of service before continuing');

        if(!trim($_POST['billpay_account_holder']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter the account holder');

        if(!trim($_POST['billpay_account_number']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter the account number');

        if(!trim($_POST['billpay_bank_code']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter the bank code');

        if(!BillpayGatherDataUtils::getCurrentCustomerSalutation() && empty($_POST['billpay_id_gender']))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your title');

        if(!BillpayGatherDataUtils::getCurrentCustomerBirthday() && (empty($_POST['billpay_birthday_day']) || empty($_POST['billpay_birthday_month']) || empty($_POST['billpay_birthday_year'])))
            $this->_billpaySubmitErrors[] = $this->l('Please enter your date of birth');
    }

    /**
     *
     * @see BillpayPaymentModule::sendBillpayPreauthorizeRequest()
     */
    public function sendBillpayPreauthorizeRequest()
    {
        include_once(_PS_MODULE_DIR_ . 'billpay/classes/BillpayPreauthorizeRequest.php');

        if (!parent::sendBillpayPreauthorizeRequest(new BillpayPreauthorizeRequest(IPL_CORE_PAYMENT_TYPE_DIRECT_DEBIT)))
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

    	global $smarty;
    	$smarty->assign(array(
    			'displayName' => $this->displayName,
    	));

    	return $smarty->fetch(_PS_MODULE_DIR_ .'billpaydirectdebit/views/templates/frontend/billpay_confirmation.tpl');
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

    	global $smarty;
    	$smarty->assign(array(
    			'displayName'                => $this->displayName,
    	));

    	return $smarty->fetch(_PS_MODULE_DIR_. 'billpaydirectdebit/views/templates/frontend/billpay_order_detail.tpl');
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

        global $smarty;
        $smarty->assign(array(
                'displayName'                => $this->displayName,
                'billpayActivationPerformed' => $billpayBankData["activation_performed"],
        ));

        return $smarty->fetch(_PS_MODULE_DIR_. $this->name .'/views/templates/backend/billpay_information.tpl');
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

        // fill
        $params['pdf']->Ln(10);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 10);
        $params['pdf']->Cell(190, 25, '', 1, 0, 'L', true);

        // payment name
        $params['pdf']->Ln(5);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 10);
        $params['pdf']->Cell(50, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->displayName), 0, 0, 'L');

        // text
        $params['pdf']->SetFont($params['pdf']::fontname(), '', 8);

        $params['pdf']->Ln(8);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Within the next few days, we will withdraw the amount due from the bank account supplied when placing the order.'));
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');
    }
}