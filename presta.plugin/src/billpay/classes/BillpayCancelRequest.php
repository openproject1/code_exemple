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
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_cancel_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayCancelRequest
{
    /**
     *
     * @var ipl_canceled_request
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
     * @return ipl_canceled_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_cancel_request(BillpayRequestUtils::getConfigurationTransactionMode());
        }

        return $this->_req;
    }

    /**
     *
     * @param object $order
     * @param array $products; when refound was executed, new products quantity
     * @return BillpayCancelRequest
     */
    public function setBillpayCancelParamsRequest($order, $products = array())
    {
        $this->_getReq()->set_cancel_params(
                BillpayGatherBackendDataUtils::getOrderReference($order),
                ($products) ? BillpayGatherBackendDataUtils::getCartTotalPriceGross($products, $order->total_shipping) // refound has been executed the gross total card needs to be taken from the remaining products price sum together with shipping costs
                            : BillpayGatherBackendDataUtils::getOrderTotalPaid($order), // no refound the total equal order total
                BillpayGatherBackendDataUtils::getCurrency($order->id_currency)
            );

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function sendBillpayCancelRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('cancel', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('cancel', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('cancel', $this->_getReq());

        return BillpayRequestUtils::checkRequestHasError('cancel', $this->_getReq());
    }

    /**
     *
     * @return string
     */
    public function getBillpayCancelRequestMerchantErrorMessage()
    {
        return $this->_getReq()->get_merchant_error_message();
    }
}