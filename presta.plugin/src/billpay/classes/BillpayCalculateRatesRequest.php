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
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_calculate_rates_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayCalculateRatesRequest
{
    /**
     *
     * @var ipl_calculate_rates_request
     */
    private $_req;

    /**
     *
     * @return ipl_calculate_rates_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_calculate_rates_request(BillpayRequestUtils::getConfigurationTransactionMode());
        }

        return $this->_req;
    }

    /**
     *
     * @param integer $baseamount
     * @param integer $carttotalgross
     * @param string $country
     * @param string $currency
     * @param string $language
     * @return BillpayCalculateRatesRequest
     */
    public function setCalculateRatesConfigurationRequest($baseamount, $carttotalgross, $country, $currency, $language)
    {
        $this->_getReq()->set_default_params(Configuration::get('BILLPAY_MERCHANT_ID'),
                Configuration::get('BILLPAY_PORTAL_ID'),
                BillpayRequestUtils::getHashedBillpayApiPassword());

        $this->_getReq()->set_rate_request_params($baseamount, $carttotalgross);

        $this->_getReq()->set_locale($country, $currency, $language);

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function sendCalculateRatesConfigurationRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('calculateRates', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('calculateRates', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('calculateRates', $this->_getReq());

        return BillpayRequestUtils::checkRequestHasError('calculateRates', $this->_getReq());
    }

    /**
     *
     * @return array
     */
    public function getRatePlanOptions()
    {
        return $this->_getReq()->get_options();
    }
}