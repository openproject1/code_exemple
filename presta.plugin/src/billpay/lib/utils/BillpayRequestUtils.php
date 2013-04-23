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

class BillpayRequestUtils
{
    /**
     *
     * @return string
     */
    public static function getConfigurationTransactionMode()
    {
        if (Configuration::get('BILLPAY_TRANSACTION_MODE') == BillpayPaymentModule::$billpayTransactionModes['test'])
            return Configuration::get('BILLPAY_API_TEST_URL');
        if (Configuration::get('BILLPAY_TRANSACTION_MODE') == BillpayPaymentModule::$billpayTransactionModes['live'])
            return Configuration::get('BILLPAY_API_LIVE_URL');

        return '';
    }

    /**
     *
     * @return string
     */
    public static function getHashedBillpayApiPassword()
    {
        return md5(Configuration::get('BILLPAY_API_PASSWORD'));
    }


    /**
     *
     * @param string $sResquestName
     * @param object $req
     * @return void
     */
    public static function writeRequestXmlToBillpayLog($resquestName,$req)
    {
        BillpayUtils::writeToLogFile('--- '. $resquestName .' request XML');
        BillpayUtils::writeToLogFile($req->get_request_xml());
    }

    /**
     *
     * @param string $sResquestName
     * @param object $req
     * @return void
     */
    public static function writeResponseXmlToBillpayLog($resquestName,$req)
    {
        BillpayUtils::writeToLogFile('--- '. $resquestName .' response XML');
        BillpayUtils::writeToLogFile($req->get_response_xml());
    }

    /**
     *
     * @param string $resquestName
     * @param object $e
     * @return void
     */
    public static function writeExceptionErrorToBillpayLog($resquestName,$e)
    {
        BillpayUtils::writeToLogFile('!!! Error sending ' . $resquestName . ' request: '. $e->getMessage(), 1);
    }

    /**
     *
     * @param string $sResquestName
     * @param object $req
     * @return boolean
     */
    public static function checkRequestHasError($resquestName, $req)
    {
        if ($req->has_error()) {
            BillpayUtils::writeToLogFile('!!! Error code (' . $req->get_error_code() . ') received (' . $resquestName . ' request): ' . $req->get_merchant_error_message(), 1);
            return false;
        }

        return true;
    }

    /**
     * @param string $sResquestName
     * @param object $req
     * @return boolean
     */
    public static function getRequestStatus($resquestName, $req)
    {
        if ( $req->get_status() == 'DENIED' ){
            BillpayUtils::writeToLogFile('!!! Error ' . $resquestName . ' request: status DENIED received', 1);
            return false;
        }

        if( $req->get_status() == 'APPROVED' ){
            BillpayUtils::writeToLogFile('--- '. $resquestName .' status APPROVED received');
            return true;
        }
    }

    /**
     *
     * @param object $req
     * @return array
     */
    public static function getBillpayInvoiceCreatedInvoiceRequestResults($req)
    {
        $result                         = array();
        $result['account_holder'] 		= $req->get_account_holder();
        $result['account_number'] 		= $req->get_account_number();
        $result['bank_code'] 			= $req->get_bank_code();
        $result['bank_name'] 			= $req->get_bank_name();
        $result['invoice_reference'] 	= $req->get_invoice_reference();
        $result['activation_performed'] = $req->get_activation_performed();
        $result['invoice_duedate'] 	    = $req->get_invoice_duedate();
       
        return $result;
    }

   /**
    *
    * @param object $req
    * @param int $orderId
    * @return array
    */
    public static function getBillpayTransactionCreditCreatedInvoiceRequestResults($req, $orderId)
    {
        $result                            = array();
        $result['account_holder'] 		   = '';
        $result['account_number'] 		   = '';
        $result['bank_code'] 			   = '';
        $result['bank_name'] 			   = '';
        $result['invoice_reference'] 	   = '';
        $result['invoice_duedate']         = $req->get_invoice_duedate();
        $result['activation_performed']    = $req->get_activation_performed();

        // overwrite dues from the db with the news values
        $billpayBankData = BillpayDbUtils::selectAllDataFromBillpayTable($orderId);
        $transactionCreditData = unserialize($billpayBankData["transaction_credit_data"]);
        $transactionCreditData['dues'] = $req->get_dues();
        $result['transaction_credit_data'] = serialize($transactionCreditData);
        
        return $result;
    }
}