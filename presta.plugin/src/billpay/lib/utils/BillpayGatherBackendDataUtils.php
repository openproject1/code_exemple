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

class BillpayGatherBackendDataUtils
{
    /**
     *
     * @param array $article
     * @return integer
     */
    public static function getArticleAttributeId($article)
    {
        return $article['product_attribute_id'];
    }

    /**
     *
     * @param array $article
     * @return integer
     */
    public static function getArticleQuantity($article)
    {
        return (int) $article['product_quantity'];
    }


    /**
     *
     * @param array $article
     * @return string
     */
    public static function getArticleName($article)
    {
        return (string) substr(Tools::safeOutput($article['product_name']), 0, 50);
    }


    /**
     *
     * @param array $article
     * @return string
     */
    public static function getArticleDescription($article, $idLang)
    {
        $product = new Product((int) $article['product_id'], false, (int) $idLang);

        return (string) substr(Tools::safeOutput($product->description_short), 0, 50);
    }


    /**
     *
     * @param array $article
     * @return integer
     */
    public static function getArticlePrice($article)
    {
        return BillpayUtils::getBillpayFormattedPrice((float) $article['product_price']);
    }


    /**
     *
     * @param array $article
     * @return integer
     */
    public static function getArticlePriceGross($article)
    {
        return BillpayUtils::getBillpayFormattedPrice((float) $article['product_price_wt']);
    }


    /**
     *
     * @return integer
     */
    public static function getRebate()
    {
        return 0; // Don't send rebate, rebate is calculated directly in the product price
    }

    /**
     *
     * @return integer
     */
    public static function getRebateGross()
    {
        return 0; // Don't send rebate, rebate is calculated directly in the product price
    }

    /**
     *
     * @param string $idCarrier
     * @return string
     */
    public static function getShippingName($idCarrier)
    {
        $carrierObj = new Carrier((int) $idCarrier);
        return (string) substr($carrierObj->name, 0, 50);
    }

    /**
     *
     * @param string $totalShipping
     * @return integer
     */
    public static function getShippingPriceGross($totalShipping)
    {
        return BillpayUtils::getBillpayFormattedPrice(self::_calculateShippingPriceGross($totalShipping));
    }

    /**
     *
     * @param string $totalShipping
     * @return float
     */
    private static function _calculateShippingPriceGross($totalShipping)
    {
        return ((float) $totalShipping);
    }


    /**
     *
     * @param string $totalShipping
     * @param string $carrierTaxRate
     * @return integer
     */
    public static  function getShippingPrice($totalShipping, $carrierTaxRate)
    {
        return BillpayUtils::getBillpayFormattedPrice(self::_calculateShippingPrice($totalShipping, $carrierTaxRate));
    }

    /**
     *
     * @param string $totalShipping
     * @param string $carrierTaxRate
     * @return float
     */
    private static function _calculateShippingPrice($totalShipping, $carrierTaxRate)
    {
        if($carrierTaxRate && $carrierTaxRate != '0.00')
            return ((float) $totalShipping / (1 + ($carrierTaxRate / 100)));
        else
            return ((float) $totalShipping);
    }

    /**
     *
     * @param array $productsRefunded
     * @return integer
     */
    public static function getCartTotalPrice($productsRefunded, $totalShipping, $carrierTaxRate)
    {
        $totalPrice = 0;
        foreach ($productsRefunded as $article){
            $totalPrice = $totalPrice + (float) $article['total_price'];
        }

        return BillpayUtils::getBillpayFormattedPrice((float) $totalPrice + self::_calculateShippingPrice($totalShipping, $carrierTaxRate));
    }

    /**
     *
     * @param array $products
     * @param string $totalShipping
     * @return integer
     */
    public static function getCartTotalPriceGross($products, $totalShipping)
    {
        $totalWt = 0;
        foreach ($products as $article){
            $totalWt = $totalWt + (float) $article['total_wt'];
        }

        return BillpayUtils::getBillpayFormattedPrice((float) $totalWt + self::_calculateShippingPriceGross($totalShipping));
    }

    /**
     * @param string $idCurrency
     * @return string
     */
    public static function getCurrency($idCurrency)
    {
        $currentCurrencyObject = new Currency((int) $idCurrency);
        return strtoupper($currentCurrencyObject->iso_code);
    }

    /**
     * @param Order $order
     * @return string
     */
    public static function getOrderReference($order)
    {
        return $order->id; // presta order id is the billpay reference
    }

    /**
     *
     * @param Order $order
     * @return integer
     */
    public static function getOrderTotalPaid($order)
    {
        return BillpayUtils::getBillpayFormattedPrice((float) $order->total_paid);
    }
}
