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
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_update_order_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayUpdateOrderRequest
{
    /**
     *
     * @var ipl_update_order_request
     */
    private $_req;

    /**
     *
     * @return ipl_update_order_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_update_order_request(BillpayRequestUtils::getConfigurationTransactionMode());
        }

        return $this->_req;
    }

    /**
     *
     * @param string $bptid
     * @param string $reference
     *
     * @return BillpayUpdateOrderRequest
     */
    public function setBillpayUpdateOrderRequest($bptid, $reference)
    {
        $this->_getReq()->set_default_params(Configuration::get('BILLPAY_MERCHANT_ID'),
                Configuration::get('BILLPAY_PORTAL_ID'),
                BillpayRequestUtils::getHashedBillpayApiPassword());

        $this->_getReq()->set_update_params($bptid, $reference);

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function sendBillpayUpdateOrderRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('updateOrder', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('updateOrder', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('updateOrder', $this->_getReq());

        return BillpayRequestUtils::checkRequestHasError('updateOrder', $this->_getReq());
    }
}