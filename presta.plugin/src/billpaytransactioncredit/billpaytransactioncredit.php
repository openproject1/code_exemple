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

if(!@include_once (_PS_MODULE_DIR_ . 'billpay/classes/BillpayPaymentModule.php'))
{
    Module::disableByName('billpaytransactioncredit');
    return;
}

class BillpayTransactionCredit extends BillpayPaymentModule
{
    /**
     * Class constructor. Initiates parent constructor.
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = 'billpaytransactioncredit';

        $this->limited_countries = array('de');

        parent::__construct(); // The parent construct is required for translations

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Billpay transaction credit');
        $this->description = $this->l('Billpay is the payment for the successful e-commerce expert. With Billpay online merchants are able to offer their customers payment via transaction credit - and all without any risk.');
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

        if(!BillpayDbUtils::addTransactionCreditDataColumn())
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

        if(!$this->_billpayModuleConfig['hire_purchase_allowed'])
        {
            BillpayUtils::writeToLogFile('--- Hire Purchase (Transaction credit) is inactive for this portal. Hiding ' . $this->displayName);
            return false;
        }

        if ($this->_billpayFormattedOrderTotal < $this->_billpayModuleConfig['hire_purchase_min_value'])
        {
            BillpayUtils::writeToLogFile('--- Minimum value not reached for direct hire purchase ('. $this->_billpayFormattedOrderTotal .' < '. $this->_billpayModuleConfig['hire_purchase_min_value'].') Hiding ' . $this->displayName);
            return false;
        }

        if ($this->_billpayFormattedOrderTotal > $this->_billpayModuleConfig['static_limit_hire_purchase'])
        {
            BillpayUtils::writeToLogFile('--- Static limit exceeded for direct hire purchase ('. $this->_billpayFormattedOrderTotal .' > '. $this->_billpayModuleConfig['static_limit_hire_purchase'].') Hiding ' . $this->displayName);
            return false;
        }

        global $smarty;
        return $smarty->fetch(_PS_MODULE_DIR_. $this->name . '/views/templates/frontend/billpay_payment.tpl');
    }

    /**
     *
     * @param object $cart
     * @return void
     */
    public function validateOrder($cart)
    {
        $extra_vars = array();
        $transactionCreditData = $this->getFromSessionPostTransactionCreditData();

        /*
         * email html block
         */
        global $smarty;
        $smarty->assign(array(
                'transactionCreditData' => $transactionCreditData,
        ));
        $extra_vars['{billpay_order_conf_transaction_credit_totals_mail_block}'] = $smarty->fetch(_PS_MODULE_DIR_. 'billpaytransactioncredit/views/templates/mail/billpay_order_conf_transaction_credit_totals.tpl');
        $extra_vars['{billpay_order_conf_mail_block}'] = $smarty->fetch(_PS_MODULE_DIR_. 'billpaytransactioncredit/views/templates/mail/billpay_order_conf.tpl');

        /*
         * email text block
         */
        $txtSurcharge = $this->l("Surcharge for %s rates");
        $extra_vars['{billpay_order_conf_mail_block_text}']  = "\t" . sprintf($txtSurcharge, $transactionCreditData['ratesNumber']);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\t" . "(".Tools::displayPrice($transactionCreditData['calculation']['base']/100). "x" .($transactionCreditData['calculation']['interest']/100). "x"  .$transactionCreditData['ratesNumber']. ")/ 100: ". Tools::displayPrice($transactionCreditData['calculation']['surcharge']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\t" . $this->l('Processing fee').": ". Tools::displayPrice($transactionCreditData['calculation']['fee']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\t" . $this->l('Grand total transaction credit').": ". Tools::displayPrice($transactionCreditData['calculation']['total']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\n" . "-----";
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\n" . $this->l('Transaction Credit')."\n\n";
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= $this->l('The amounts due will be withdrawn from the bank account supplied when placing the order in monthly periods.');
        foreach($transactionCreditData["dues"] as $key => $due)
            $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n\n". $key+1 . "." . $this->l('Rate:') . Tools::displayPrice($due["value"]/100). ";";

        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Total price calculation:');
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Cart total').": ". Tools::displayPrice($transactionCreditData['calculation']['base']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Surcharge');
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . "(".Tools::displayPrice($transactionCreditData['calculation']['base']/100). "x" . ($transactionCreditData['calculation']['interest']/100). "x"  .$transactionCreditData['ratesNumber']. ")/100: ". Tools::displayPrice($transactionCreditData['calculation']['surcharge']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Processing fee').": ". Tools::displayPrice($transactionCreditData['calculation']['fee']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Additional fees (e.g. shipping costs)').": ". Tools::displayPrice(($transactionCreditData['calculation']['cart'] - $transactionCreditData['calculation']['base'])/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . "---";
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('Grand total transaction credit')." = ". Tools::displayPrice($transactionCreditData['calculation']['total']/100);
        $extra_vars['{billpay_order_conf_mail_block_text}'] .= "\n" . $this->l('effective APR')." = ". $transactionCreditData['calculation']['anual']/100 . "%";

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
                'agb'                         => BillpayUtils::getDatenschutzLinkTransactionCredit(),

                'transactionCreditTerms' => self::_getTransactionCreditTermsFromSessionConfig(),
                'customerBirthday'       => BillpayGatherDataUtils::getCurrentCustomerBirthday(),
                'customerSalutation'     => BillpayGatherDataUtils::getCurrentCustomerSalutation(),
                'deliveryAddressName'    => BillpayGatherDataUtils::getInvoiceAddressFirstName() . ' ' . BillpayGatherDataUtils::getInvoiceAddressLastName(),
                'years'                  => BillpayUtils::billpayDateYears(),
                'months'                 => BillpayUtils::billpayDateMonths(),
                'days'                   => BillpayUtils::billpayDateDays(),
                'total'                  => $cart->getOrderTotal(true),
        ));

        $this->_sendCalculateRatesConfigurationRequest();

        return $smarty->fetch(_PS_MODULE_DIR_. 'billpaytransactioncredit/views/templates/frontend/billpay_payment_transactioncredit.tpl');
    }

    /**
     *
     * @return array
     */
    private static function _getTransactionCreditTermsFromSessionConfig()
    {
        $billpayModuleConfig   = BillpaySessionUtils::getSessionVar('billpayModuleConfig');
        return $billpayModuleConfig['terms_hire_purchase'];
    }

    /**
     *
     * @return boolean
     */
    private function _sendCalculateRatesConfigurationRequest()
    {
        include_once(_PS_MODULE_DIR_ . '/billpay/classes/BillpayCalculateRatesRequest.php');
        $billpayCalculateRatesRequest = new BillpayCalculateRatesRequest();

        global $cart, $cookie;
        $billpayCalculateRatesRequest->setCalculateRatesConfigurationRequest(BillpayGatherDataUtils::getCartBasePriceWithoutDiscounts(),
                                                                             BillpayGatherDataUtils::getCartTotalPriceGross(),
                                                                             parent::_getCountryISOCode($cart),
                                                                             BillpayGatherDataUtils::_getCurrencyBaseOnId($cart->id_currency),
                                                                             parent::_getLanguageISOCode($cookie));

        if($billpayCalculateRatesRequest->sendCalculateRatesConfigurationRequest() === false)
            return false;

        BillpaySessionUtils::setSessionVar('billpayRatePlan', $billpayCalculateRatesRequest->getRatePlanOptions());
    }


    /**
     *
     * @param string $ratesMonths
     */
    public function ajaxBillpayTransactionCreditCall($monthsRatePlan)
    {
        $billpayRatePlan = BillpaySessionUtils::getSessionVar('billpayRatePlan');

        global $smarty;
        $smarty->assign(array(
                'monthsRatePlan'   => $monthsRatePlan,
                'selectedRatePlan' => $billpayRatePlan[$monthsRatePlan],
                'agb'              => BillpayUtils::getDatenschutzLinkTransactionCredit(),
        ));

        return $smarty->fetch(_PS_MODULE_DIR_ . 'billpaytransactioncredit/views/templates/frontend/inc/billpay_payment_transactioncredit_rate_plan.tpl');
    }

    /**
     *
     * @return boolean
     */
    public function billpayValidatePayment()
    {
        $this->_checkAndSetErrorsBillpayValidatePaymentTransactionCredit();

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
    private function _checkAndSetErrorsBillpayValidatePaymentTransactionCredit()
    {
        if (!$_POST['billpay_transaction_credit_rates'])
            $this->_billpaySubmitErrors[] = $this->l('Transaction credit rates value is missing');

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

        if (!parent::sendBillpayPreauthorizeRequest(new BillpayPreauthorizeRequest(IPL_CORE_PAYMENT_TYPE_RATE_PAYMENT)))
            return false;

        return true;
    }

    /**
     *
     * @return boolean|array
     */
    public function getFromSessionPostTransactionCreditData()
    {
        if (!isset($_POST['billpay_transaction_credit_rates']) || BillpaySessionUtils::hasSessionVar('billpayRatePlan') === false)
            return false;

        $sessionBillpayRatePlan  = BillpaySessionUtils::getSessionVar('billpayRatePlan');
        $postSelectedRatesNumber = (string) trim($_POST['billpay_transaction_credit_rates']);

        $transactionCreditData = array();
        $transactionCreditData['ratesNumber'] = $postSelectedRatesNumber;
        $transactionCreditData['calculation'] = $sessionBillpayRatePlan[$postSelectedRatesNumber]['calculation'];
        $transactionCreditData['dues']        = $sessionBillpayRatePlan[$postSelectedRatesNumber]['dues'];

        return $transactionCreditData;
    }

    /**
     *
     * @param array $transactionCreditData
     * @return boolean
     */
    public function saveTransactionCreditDataToDb($transactionCreditData)
    {
        if(BillpayDbUtils::updateTransactionCreditDataToBillpayTable((string) $this->currentOrder, serialize($transactionCreditData)) === false)
        {
            BillpayUtils::writeToLogFile('--- Billpay Transaction Credit Data results NOT SUCCESSFULLY saved to `' . _DB_PREFIX_ . 'billpay` table, Order:'.$this->currentOrder , 1);
            return false;
        }

        BillpayUtils::writeToLogFile('--- Billpay Transaction Credit Data results successfully saved to `' . _DB_PREFIX_ . 'billpay` table, Order:'.$this->currentOrder);
    }

    /**
     *
     * @param array $params
     */
    public function hookPaymentReturn($params)
    {
    	if (!$this->active)
    		return ;

    	$billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['objOrder']->id);

    	$transactionCreditData = $this->_parseDbBankDataDuedatesToReadableFromat($billpayBankData["transaction_credit_data"]);

    	global $smarty;
    	$smarty->assign(array(
    			'transactionCreditData' => unserialize($transactionCreditData),
    	));

    	return $smarty->fetch(_PS_MODULE_DIR_. 'billpaytransactioncredit/views/templates/frontend/billpay_confirmation.tpl');
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

        $transactionCreditData = $this->_parseDbBankDataDuedatesToReadableFromat($billpayBankData["transaction_credit_data"]);

        global $smarty;
        $smarty->assign(array(
                'transactionCreditData'      => unserialize($transactionCreditData),
        ));

        return $smarty->fetch(_PS_MODULE_DIR_. $this->name .'/views/templates/frontend/billpay_order_detail.tpl');
    }

    /**
     *
     * @param unknown_type $params
     * @return string rendered template output
     */
    public function hookAdminOrder($params)
    {
        $order = new Order((int) $params['id_order']);

        if (!Validate::isLoadedObject($order) || $order->module != $this->name)
            return false;

        $billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['id_order']);
        $transactionCreditData = $this->_parseDbBankDataDuedatesToReadableFromat($billpayBankData["transaction_credit_data"]);

        global $smarty;
        $smarty->assign(array(
                'displayName'                => $this->displayName,
                'billpayActivationPerformed' => $billpayBankData["activation_performed"],
                'transactionCreditData'      => unserialize($transactionCreditData),
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

        $billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($params['id_order']);
        $transactionCreditData = unserialize($this->_parseDbBankDataDuedatesToReadableFromat($billpayBankData["transaction_credit_data"]));

        $currency = Currency::getCurrencyInstance((int)($order->id_currency));

        $processingFee              = (double)$transactionCreditData['calculation']['fee'] / 100;
        $processingFeeVatPercentage = 19;
        $processingFeeVat           = $processingFee * $processingFeeVatPercentage / (100 + $processingFeeVatPercentage);
        $processingFeeNetto         = $processingFee - $processingFeeVat;


        // line for payment name
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 8);
        $params['pdf']->Ln(4);
        $params['pdf']->Cell(190, 0, '', 1, 0, 'R');

        // payment name with fill
        $params['pdf']->Ln(0.1);
        $params['pdf']->Cell(190, 5, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Transaction credit')), 0, 0, 'R', true);

        // fill for totals
        $params['pdf']->Ln(7);
        $params['pdf']->Cell(110, 0, '', 0, 0, 'R');
        $params['pdf']->Cell(0, 20, '', 1, 0, 'R' , true);

        // totals
        $params['pdf']->Ln(4);
        $params['pdf']->Cell(165, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Surcharge')). ' : ', 0, 0, 'R');
        $params['pdf']->Cell(0, 0, number_format($transactionCreditData['calculation']['surcharge']/100, 2) .' '. self::_convertSign($currency->sign), 0, 0, 'R');
        $params['pdf']->Ln(4);

        $params['pdf']->Cell(165, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Processing fee excl. VAT')). ' : ', 0, 0, 'R');
        $params['pdf']->Cell(0, 0, number_format($processingFeeNetto, 2) .' '. self::_convertSign($currency->sign), 0, 0, 'R');
        $params['pdf']->Ln(4);

        $params['pdf']->Cell(165, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Processing fee VAT')). ' : ', 0, 0, 'R');
        $params['pdf']->Cell(0, 0, number_format($processingFeeVat, 2) .' '. self::_convertSign($currency->sign), 0, 0, 'R');
        $params['pdf']->Ln(4);

        $params['pdf']->Cell(165, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Grand total transaction credit')). ' : ', 0, 0, 'R');
        $params['pdf']->Cell(0, 0, number_format($transactionCreditData['calculation']['total']/100, 2) .' '. self::_convertSign($currency->sign), 0, 0, 'R');
        $params['pdf']->Ln(4);

        // write on the next page
        $params['pdf']->AddPage();

        // fill
        $params['pdf']->Ln(10);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 10);
        $params['pdf']->Cell(190, self::_getTransactionCreditFillHeight((string) $transactionCreditData['ratesNumber']), '', 1, 0, 'L', true);

        // payment name
        $params['pdf']->Ln(5);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 10);
        $params['pdf']->Cell(50, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Transaction credit')), 0, 0, 'L');

        // text
        $params['pdf']->SetFont($params['pdf']::fontname(), '', 8);
        $params['pdf']->Ln(8);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('The amounts due will be withdrawn from the bank account supplied when placing the order in monthly periods.'));
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');
        $params['pdf']->Ln(2);

        foreach ($transactionCreditData["dues"] as $key => $dues)
        {
            $params['pdf']->Ln(4);
            $text = $key+1 . '. ';
            $params['pdf']->Cell(10, 0, $text, 0, 0, 'R');

            $text = Tools::iconv('utf-8', $params['pdf']::encoding(), 'Rate:');
            $params['pdf']->Cell(8, 0, $text, 0, 0, 'R');

            $text  = number_format($dues['value']/100, 2). ' '. self::_convertSign($currency->sign);
            $text .= ($dues['date']) ? ' ('. Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('due on:')) . ' ' .$dues['date'] .')' : '';
            $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');
        }

        // Total price calculation
        $params['pdf']->Ln(8);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 8);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Total price calculation:')). ' : ', 0, 0, 'R');

        // Cart total
        $params['pdf']->Ln(4);
        $params['pdf']->SetFont($params['pdf']::fontname(), '', 8);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Cart total')). ' = ', 0, 0, 'R');

        $text = number_format($transactionCreditData['calculation']['base']/100, 2) .' '. self::_convertSign($currency->sign);
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        // Surcharge
        $params['pdf']->Ln(4);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Surcharge')). ' + ', 0, 0, 'R');

        $params['pdf']->Ln(4);
        $text = '(' . number_format($transactionCreditData['calculation']['base']/100, 2) .' '. self::_convertSign($currency->sign);
        $text .= ' x ' . number_format($transactionCreditData['calculation']['interest']/100, 2) . ' x ' . $transactionCreditData['ratesNumber'] .') / 100';
        $params['pdf']->Cell(60, 0, $text. ' = ', 0, 0, 'R');

        $text = number_format($transactionCreditData['calculation']['surcharge']/100, 2) .' '. self::_convertSign($currency->sign);
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        // Processing fee
        $params['pdf']->Ln(4);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Processing fee')). ' + ', 0, 0, 'R');

        $text = number_format($transactionCreditData['calculation']['fee']/100, 2) .' '. self::_convertSign($currency->sign);
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        // Additional fees (e.g. shipping costs)
        $params['pdf']->Ln(4);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Additional fees (e.g. shipping costs)')). ' + ', 0, 0, 'R');

        $text = number_format(($transactionCreditData['calculation']['cart'] - $transactionCreditData['calculation']['base']) /100, 2) .' '. self::_convertSign($currency->sign);
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        // line
        $params['pdf']->Ln(3);
        $params['pdf']->Cell(3, 0, '', 0, 0, 'L');
        $params['pdf']->Cell(80, 0, '', 1, 0, 'L');

        // Grand total transaction credit
        $params['pdf']->Ln(3);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 8);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('Grand total transaction credit')). ' = ', 0, 0, 'R');

        $text = number_format($transactionCreditData['calculation']['total']/100, 2) .' '. self::_convertSign($currency->sign);
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        // effective APR
        $params['pdf']->Ln(4);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'B', 8);
        $params['pdf']->Cell(60, 0, Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('effective APR')). ' = ', 0, 0, 'R');

        $text = number_format($transactionCreditData['calculation']['anual']/100, 2) .' %';
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        // small text
        $params['pdf']->Ln(6);
        $params['pdf']->SetFont($params['pdf']::fontname(), 'I', 6);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('First rate due by direct debit one month after shipping date, following rates due by direct debit monthly'));
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        $params['pdf']->Ln(4);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('on corresponding calendar day of subsequent month or following day, respectively'));
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');

        $params['pdf']->Ln(4);
        $text = Tools::iconv('utf-8', $params['pdf']::encoding(), $this->l('(Example: Shipping date 2012/9/19, 1st Rate 2012/10/19, following rates on 19th of each subsequent month).'));
        $params['pdf']->Cell(150, 0, $text, 0, 0, 'L');
    }


    /**
     *
     * @param string $transactionCreditData
     * @return string
     */
    private function _parseDbBankDataDuedatesToReadableFromat($transactionCreditData)
    {
        $transactionCreditData = unserialize($transactionCreditData);
        foreach ($transactionCreditData["dues"] as $key => $dues)
            $transactionCreditData["dues"][$key]["date"] = BillpayUtils::parseBillpayInvoiceDuedateToReadableFromat($dues["date"]);

        return serialize($transactionCreditData);
    }

    /**
     *
     * @param string $s
     * @return string
     */
    private static function _convertSign($s)
    {
        return str_replace(array('€', '£', '¥'), array(chr(128), chr(163), chr(165)), $s);
    }

    /**
     *
     * @param string $ratesNumber
     * @return int string
     */
    private static function _getTransactionCreditFillHeight($ratesNumber)
    {
        $fillHeight = array(
            "6"  => 96,
            "9"  => 112,
            "12" => 120,
            "18" => 143,
            "24" => 168,
        );

        return $fillHeight[$ratesNumber];
    }
}