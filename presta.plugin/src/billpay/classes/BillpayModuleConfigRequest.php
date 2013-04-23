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
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_module_config_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayModuleConfigRequest
{
    /**
     *
     * @var ipl_module_config_request
     */
    private $_req;

    /**
     *
     * @return ipl_module_config_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_module_config_request(BillpayRequestUtils::getConfigurationTransactionMode());
        }

        return $this->_req;
    }

    /**
     *
     * @param string $country
     * @param string $currency
     * @param string $language
     * @return BillpayModuleConfigRequest
     */
    public function setBillpayModuleConfigurationRequest($country, $currency, $language)
    {
        $this->_getReq()->set_default_params(Configuration::get('BILLPAY_MERCHANT_ID'),
                                             Configuration::get('BILLPAY_PORTAL_ID'),
                                             BillpayRequestUtils::getHashedBillpayApiPassword());

        $this->_getReq()->set_locale($country, $currency, $language);

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function sendBillpayModuleConfigurationRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('moduleConfig', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('moduleConfig', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('moduleConfig', $this->_getReq());

        return BillpayRequestUtils::checkRequestHasError('moduleConfig', $this->_getReq());
    }

    /**
     *
     * @return array
     */
    public function getBillpayModuleConfiguration()
    {
        $billpayModuleConfiguration = array();

        $billpayModuleConfiguration['active']                  = $this->_getReq()->is_active();
        $billpayModuleConfiguration['invoice_allowed'] 	       = $this->_getReq()->is_invoice_allowed();
        $billpayModuleConfiguration['direct_debit_allowed']    = $this->_getReq()->is_direct_debit_allowed();
        $billpayModuleConfiguration['hire_purchase_allowed']   = $this->_getReq()->is_hire_purchase_allowed();
        $billpayModuleConfiguration['invoicebusiness_allowed'] = $this->_getReq()->is_invoicebusiness_allowed();

        $billpayModuleConfiguration['invoice_min_value']         = $this->_getReq()->get_invoice_min_value();
        $billpayModuleConfiguration['direct_debit_min_value']    = $this->_getReq()->get_direct_debit_min_value();
        $billpayModuleConfiguration['hire_purchase_min_value']   = $this->_getReq()->get_hire_purchase_min_value();
        $billpayModuleConfiguration['invoicebusiness_min_value'] = $this->_getReq()->get_invoicebusiness_min_value();

        $billpayModuleConfiguration['static_limit_invoice']         = $this->_getReq()->get_static_limit_invoice();
        $billpayModuleConfiguration['static_limit_direct_debit']    = $this->_getReq()->get_static_limit_direct_debit();
        $billpayModuleConfiguration['static_limit_hire_purchase']   = $this->_getReq()->get_static_limit_hire_purchase();
        $billpayModuleConfiguration['static_limit_invoicebusiness'] = $this->_getReq()->get_static_limit_invoicebusiness();

        $billpayModuleConfiguration['terms_hire_purchase'] = $this->_getReq()->get_terms();

        BillpayUtils::writeToLogFile('--- BillpayModuleConfiguration recived: ');
        BillpayUtils::writeToLogFile($billpayModuleConfiguration);

        return $billpayModuleConfiguration;
    }
}