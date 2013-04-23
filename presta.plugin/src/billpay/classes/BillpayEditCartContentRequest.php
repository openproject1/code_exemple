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
include_once(_PS_MODULE_DIR_ . 'billpay/lib/api/php5/ipl_edit_cart_content_request.php');
include_once(_PS_MODULE_DIR_ . 'billpay/lib/utils/BillpayRequestUtils.php');

class BillpayEditCartContentRequest
{
    /**
     *
     * @var ipl_canceled_request
     */
    private $_req;

    /**
     *
     * @return ipl_canceled_request
     */
    protected function _getReq()
    {
        if ($this->_req === null){
            $this->_req = new ipl_edit_cart_content_request(BillpayRequestUtils::getConfigurationTransactionMode());
        }

        return $this->_req;
    }


    /**
     *
     * @return BillpayEditCartContentRequest
     */
    public function setEditCartContentRequestDefault()
    {
        $this->_getReq()->set_default_params(Configuration::get('BILLPAY_MERCHANT_ID'),
                Configuration::get('BILLPAY_PORTAL_ID'),
                BillpayRequestUtils::getHashedBillpayApiPassword());

        return $this;
    }

    /**
     *
     * @param object $order
     * @param array $products
     * @return BillpayEditCartContentRequest
     */
    public function setEditCartContentRequestTotal($order, $products)
    {
        $this->_getReq()->set_total(
                BillpayGatherBackendDataUtils::getRebate(),
                BillpayGatherBackendDataUtils::getRebateGross(),
                BillpayGatherBackendDataUtils::getShippingName($order->id_carrier),
                BillpayGatherBackendDataUtils::getShippingPrice($order->total_shipping, $order->carrier_tax_rate),
                BillpayGatherBackendDataUtils::getShippingPriceGross($order->total_shipping),
                BillpayGatherBackendDataUtils::getCartTotalPrice($products, $order->total_shipping, $order->carrier_tax_rate),
                BillpayGatherBackendDataUtils::getCartTotalPriceGross($products, $order->total_shipping),
                BillpayGatherBackendDataUtils::getCurrency($order->id_currency),
                BillpayGatherBackendDataUtils::getOrderReference($order)
        );

        return $this;
    }

    /**
     *
     * @param array $products
     * @param object $order
     * @return BillpayEditCartContentRequest
     */
    public function setEditCartContentRequestAddArticles($products, $order)
    {
        foreach ($products as $article){
            if (BillpayGatherBackendDataUtils::getArticleQuantity($article) != 0){ // only articles with quantity
                $this->_getReq()->add_article(
                            BillpayGatherBackendDataUtils::getArticleAttributeId($article),
                            BillpayGatherBackendDataUtils::getArticleQuantity($article),
                            BillpayGatherBackendDataUtils::getArticleName($article),
                            BillpayGatherBackendDataUtils::getArticleDescription($article, $order->id_lang),
                            BillpayGatherBackendDataUtils::getArticlePrice($article),
                            BillpayGatherBackendDataUtils::getArticlePriceGross($article)
                );
            }
        }

        return $this;
    }


    /**
     *
     * @return boolean
     */
    public function sendBillpayEditCartContentRequest()
    {
        try {
            $this->_getReq()->send();
        }
        catch (Exception $e) {
            BillpayRequestUtils::writeExceptionErrorToBillpayLog('editCartContent', $e);
            return false;
        }

        BillpayRequestUtils::writeRequestXmlToBillpayLog('editCartContent', $this->_getReq());
        BillpayRequestUtils::writeResponseXmlToBillpayLog('editCartContent', $this->_getReq());

        return BillpayRequestUtils::checkRequestHasError('editCartContent', $this->_getReq());
    }

    /**
     *
     * @return array
     */
    public function getUpdatedTransactionCreditDataFromEditCartContentRequest()
    {
        $editCartContentRequestDueUpdate   = $this->_getReq()->get_due_update();
        $editCartContentRequestNumberRates = $this->_getReq()->get_number_of_rates();

        $updatedTransactionCreditData                = array();
        $updatedTransactionCreditData['ratesNumber'] = $editCartContentRequestNumberRates;
        $updatedTransactionCreditData['calculation'] = $editCartContentRequestDueUpdate['calculation'];
        $updatedTransactionCreditData['dues']        = $editCartContentRequestDueUpdate['dues'];

        return $updatedTransactionCreditData;
    }

    /**
     *
     * @return string
     */
    public function getBillpayEditCartContentRequestMerchantErrorMessage(){
        return $this->_getReq()->get_merchant_error_message();
    }
}