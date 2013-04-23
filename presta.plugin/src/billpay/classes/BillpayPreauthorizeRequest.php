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
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_preauthorize_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayPreauthorizeRequest
{
    /**
     *
     * @var ipl_module_config_request
     */
    private $_req;

    /**
     *
     * @var string
     */
    private $_paymentType;

    /**
     *
     * @param unknown_type $paymentType
     */
    public function __construct($paymentType)
    {
        $this->_paymentType = $paymentType;
    }

    /**
     *
     * @return ipl_module_config_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_preauthorize_request(BillpayRequestUtils::getConfigurationTransactionMode(), $this->_paymentType);
        }

        return $this->_req;
    }

    /**
     *
     * @param boolean $setTermsAccepted
     * @param boolean $captureRequestNecessary
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestStartDetails($setTermsAccepted = true, $captureRequestNecessary = false)
    {
        $this->_getReq()->set_default_params(Configuration::get('BILLPAY_MERCHANT_ID'),
                                             Configuration::get('BILLPAY_PORTAL_ID'),
                                             BillpayRequestUtils::getHashedBillpayApiPassword());

        $this->_getReq()->set_terms_accepted($setTermsAccepted);

        $this->_getReq()->set_capture_request_necessary($captureRequestNecessary);

        return $this;
    }

    /**
     *
     * @param string $customerGroup
     * @param string $postSalutation
     * @param string $postBirthday
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestCustomerDetails($customerGroup, $postSalutation = '', $postBirthday = '')
    {
        $this->_getReq()->set_customer_details(
            BillpayGatherDataUtils::getCurrentCustomerId(),
            BillpayGatherDataUtils::getBillpayFormattedCustomerType(),
            ($postSalutation) ? $postSalutation : BillpayGatherDataUtils::getCurrentCustomerSalutation(),
            BillpayGatherDataUtils::getCurrentCustomerTitle(),
            BillpayGatherDataUtils::getInvoiceAddressFirstName(),
            BillpayGatherDataUtils::getInvoiceAddressLastName(),
            BillpayGatherDataUtils::getInvoiceAddressStreet(),
            BillpayGatherDataUtils::getInvoiceAddressStreetNo(),
            BillpayGatherDataUtils::getInvoiceAddressAddition(),
            BillpayGatherDataUtils::getInvoiceAddressZip(),
            BillpayGatherDataUtils::getInvoiceAddressCity(),
            BillpayGatherDataUtils::getInvoiceAddressCountryISOCode(),
            BillpayGatherDataUtils::getCurrentCustomerEmail(),
            BillpayGatherDataUtils::getInvoiceAddressPhone(),
            BillpayGatherDataUtils::getInvoiceAddressPhoneMobile(),
            ($postBirthday) ? $postBirthday : BillpayGatherDataUtils::getCurrentCustomerBirthday(),
            BillpayGatherDataUtils::_getLanguageISO(),
            Tools::getRemoteAddr(),
            $customerGroup
        );

        return $this;
    }

    /**
     *
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestShippingDetails()
    {
        $addressesAreEquals = BillpayGatherDataUtils::getAddressesAreEquals();

        if ($addressesAreEquals === true) {
            // Parameters for shipping address must remain empty
            $this->_getReq()->set_shipping_details( $addressesAreEquals );
        }else{
            // Invoice address and shipping address are different
            $this->_getReq()->set_shipping_details(
                $addressesAreEquals,
                '$sSalutation',
                BillpayGatherDataUtils::getDeliveryAddressTitle(),
                BillpayGatherDataUtils::getDeliveryAddressFirstName(),
                BillpayGatherDataUtils::getDeliveryAddressLastName(),
                BillpayGatherDataUtils::getDeliveryAddressStreet(),
                BillpayGatherDataUtils::getDeliveryAddressStreetNo(),
                BillpayGatherDataUtils::getDeliveryAddressAddition(),
                BillpayGatherDataUtils::getDeliveryAddressZip(),
                BillpayGatherDataUtils::getDeliveryAddressCity(),
                BillpayGatherDataUtils::getDeliveryAddressCountryISOCode(),
                BillpayGatherDataUtils::getDeliveryAddressPhone(),
                BillpayGatherDataUtils::getDeliveryAddressPhoneMobile()
            );
        }

        return $this;
    }

    /**
     *
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestTotal()
    {
        $this->_getReq()->set_total(
            BillpayGatherDataUtils::getRebate(),
            BillpayGatherDataUtils::getRebateGross(),
            BillpayGatherDataUtils::getShippingName(),
		    BillpayGatherDataUtils::getShippingPrice(),
            BillpayGatherDataUtils::getShippingPriceGross(),
            BillpayGatherDataUtils::getCartTotalPrice(),
            BillpayGatherDataUtils::getCartTotalPriceGross(),
            BillpayGatherDataUtils::getCurrency(),
            BillpayGatherDataUtils::getReference()
        );

        return $this;
    }

    /**
     *
     * @param string $sessionMd5ID
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestFraudDetection($sessionMd5ID)
    {
        $this->_getReq()->set_fraud_detection($sessionMd5ID);

        return $this;
    }

    /**
     *
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestAddArticles()
    {
        foreach (BillpayGatherDataUtils::getArticles() as $article){
            $this->_getReq()->add_article(
                BillpayGatherDataUtils::getArticleAttributeId($article),
                BillpayGatherDataUtils::getArticleQuantity($article),
                BillpayGatherDataUtils::getArticleName($article),
                BillpayGatherDataUtils::getArticleDescription($article),
                BillpayGatherDataUtils::getArticlePrice($article),
                BillpayGatherDataUtils::getArticlePriceGross($article)
            );
        }

        return $this;
    }


    /**
     *
     * @param string $postName
     * @param string $postLegalForm
     * @param string $postRegisterNumber
     * @param string $postHolderName
     * @param string $postTaxNumber
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestCompanyDetails($postName, $postLegalForm, $postRegisterNumber, $postHolderName, $postTaxNumber)
    {
        $this->_getReq()->set_company_details(
            $postName,
		    $postLegalForm,
		    $postRegisterNumber,
		    $postHolderName,
		    $postTaxNumber
        );

        return $this;
    }

    /**
     *
     * @param string $postAccountHolder
     * @param string $postAccountNumber
     * @param string $postSortCode
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestBankAccount($postAccountHolder, $postAccountNumber, $postSortCode)
    {
        $this->_getReq()->set_bank_account(
                $postAccountHolder,
                $postAccountNumber,
                $postSortCode
        );

        return $this;
    }

    /**
     *
     * @param int $rateCount
     * @param float $totalAmount
     * @return BillpayPreauthorizeRequest
     */
    public function setBillpayPreauthorizeRequestRate($rateCount, $totalAmount)
    {
        $this->_getReq()->set_rate_request(
                $rateCount,
                BillpayUtils::getBillpayFormattedPrice((float) $totalAmount)
        );

        return $this;
    }


    /**
     *
     * @return boolean
     */
    public function sendBillpayPreauthorizeRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('preauthorizeRequest', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('preauthorizeRequest', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('preauthorizeRequest', $this->_getReq());

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function getBillpayPreauthorizeRequestStatus()
    {
        return BillpayRequestUtils::getRequestStatus('preauthorizeRequest', $this->_getReq());
    }

    /**
     *
     * @return boolean
     */
    public function checkBillpayPreauthorizeRequestHasError()
    {
        return BillpayRequestUtils::checkRequestHasError('preauthorizeRequest', $this->_getReq());
    }

    /**
     * @return string
     */
    public function getBillpayPreauthorizeRequestCustomerErrorMessage() {
		return $this->_getReq()->get_customer_error_message();
	}

	/**
	 *
	 * @return boolean
	 */
	public function getBillpayPreauthorizeRequestBptid()
	{
	    return $this->_getReq()->get_bptid();
	}

	/**
	 *
	 * @return array
	 */
	public function getBillpayPreauthorizeRequestResults()
	{
        return BillpayRequestUtils::getBillpayInvoiceCreatedInvoiceRequestResults($this->_getReq());
	}
}