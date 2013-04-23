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

include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/ipl_xml_api.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_invoice_created_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayInvoiceCreatedRequest
{
    /**
     *
     * @var ipl_invoice_created_request
     */
    private $_req;

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->_getReq()->set_default_params(Configuration::get('BILLPAY_MERCHANT_ID'),
                Configuration::get('BILLPAY_PORTAL_ID'),
                BillpayRequestUtils::getHashedBillpayApiPassword());
    }

    /**
     *
     * @return ipl_invoice_created_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_invoice_created_request(BillpayRequestUtils::getConfigurationTransactionMode());
        }

        return $this->_req;
    }

    /**
     *
     * @param object $order
     * @param array  $products; when refound was executed, new products quantity
     * @param int    $delayinDays
     * @return BillpayInvoiceCreatedRequest
     */
    public function setBillpayInvoiceCreatedRequest($order, $products = array(), $delayinDays = 0)
    {
        $this->_getReq()->set_invoice_params(
                ($products) ? BillpayGatherBackendDataUtils::getCartTotalPriceGross($products, $order->total_shipping) // refound has been executed the gross total card needs to be taken from the remaining products price sum together with shipping costs
                            : BillpayGatherBackendDataUtils::getOrderTotalPaid($order), // no refound the total equal order total
                BillpayGatherBackendDataUtils::getCurrency($order->id_currency),
                BillpayGatherBackendDataUtils::getOrderReference($order),
                $delayinDays
            );

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function sendBillpayInvoiceCreatedRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('invoiceCreated', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('invoiceCreated', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('invoiceCreated', $this->_getReq());

        return BillpayRequestUtils::checkRequestHasError('invoiceCreated', $this->_getReq());
    }

    /**
     *
     * @param string $paymentName
     * @param int    $orderId
     * @return array
     */
    public function getBillpayInvoiceCreatedRequestResults($paymentName, $orderId)
    {
        if ($paymentName == 'billpaytransactioncredit')
            return BillpayRequestUtils::getBillpayTransactionCreditCreatedInvoiceRequestResults($this->_getReq(), $orderId);

        return BillpayRequestUtils::getBillpayInvoiceCreatedInvoiceRequestResults($this->_getReq());
    }

    /**
     *
     * @return string
     */
    public function getBillpayInvoiceCreatedRequestMerchantErrorMessage()
    {
        return $this->_getReq()->get_merchant_error_message();
    }
}
