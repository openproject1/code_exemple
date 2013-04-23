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

class BillpayGatherDataUtils
{
    /**
     *
     * @var Customer
     */
    private static $_customerObj;

    /**
     *
     * @return Customer
     */
    private static function _getCurrentCustomer()
    {
        if(self::$_customerObj === null){
            global $cookie;
            self::$_customerObj = new Customer((int) $cookie->id_customer);
        }

        return self::$_customerObj;
    }

    /**
     *
     * @return int
     */
    public static function getCurrentCustomerId()
    {
        return self::_getCurrentCustomer()->id;
    }

    /**
     *
     * @return string|null
     */
    public static function getCurrentCustomerBirthday()
    {
        return BillpayUtils::parsePrestaBirthdateStringInBillpayformat(self::_getCurrentCustomer()->birthday);
    }

    /**
     * Title filed is used as name in presta for normal salutation filed
     *
     * @return string
     */
    public static function getCurrentCustomerSalutation()
    {
        if (self::_getCurrentCustomer()->id_gender == '1')
            return 'Mr.';
        elseif (self::_getCurrentCustomer()->id_gender == '2')
            return 'Ms.';
        else
            return '';
    }

    /**
     * @return string
     */
    public static function getCurrentCustomerEmail()
    {
        return substr(self::_getCurrentCustomer()->email, 0, 50);
    }

    /**
     *
     * TODO: Extend for existing
     * @return string
     */
    public static function getBillpayFormattedCustomerType()
    {
        if (self::_getCurrentCustomer()->is_guest == '1')
            return 'g';
        elseif (self::_getCurrentCustomer()->is_guest == '0')
            return 'n';
    }

    /**
     * Title filed is used as name in presta for normal salutation filed
     * NO title field (ex Dr.) present in presta
     *
     * @return string
     */
    public static function getCurrentCustomerTitle()
    {
        return '';
    }


    /**
     *
     * @return boolean
     */
    public static function getAddressesAreEquals()
    {
        global $cart;

        if ($cart->id_address_invoice == $cart->id_address_delivery)
            return true;
        else
            return false;
    }



    /**
     *
     * @var Address
     */
    private static $_addressInvoiceObj;

    /**
     *
     * @return Address
     */
    private static function _getInvoiceAddress()
    {
        if(self::$_addressInvoiceObj === null){
            global $cart;
            self::$_addressInvoiceObj = new Address((int) $cart->id_address_invoice);
        }

        return self::$_addressInvoiceObj;
    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressFirstName()
    {
        return substr(self::_getInvoiceAddress()->firstname, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressLastName()
    {
        return substr(self::_getInvoiceAddress()->lastname, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressCompanyDni()
    {
        return self::_getInvoiceAddress()->dni;
    }

    /**
     * @return string
     */
    public static function getInvoiceAddressCompanyName()
    {
        return self::_getInvoiceAddress()->company;
    }

    /**
     * In presta street is stored in address1 filed together with number
     *
     * @return string
     */
    public static function getInvoiceAddressStreet()
    {
        return substr(self::_getInvoiceAddress()->address1, 0, 50);
    }

    /**
     * In presta street is stored in address1 filed together with number
     * NO single number filed present
     *
     * @return string
     */
    public static function getInvoiceAddressStreetNo()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressAddition()
    {
        return (string) substr(self::_getInvoiceAddress()->address2, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressZip()
    {
        return substr(self::_getInvoiceAddress()->postcode, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressCity()
    {
        return substr(self::_getInvoiceAddress()->city, 0, 50);
    }

    /**
     * @param object $cart
     * @return string|NULL
     */
    public static function getInvoiceAddressCountryISOCode()
    {
        $currentCountryObject = new Country((int) self::_getInvoiceAddress()->id_country);

        return BillpayUtils::getCountryISOAlpha3CodeByISOAlpha2Code($currentCountryObject->iso_code);
    }

    /**
     * Presta allow you to add only a phone filed fix or mobile
     * Phone filed is required for billpay get the filed that is present
     *
     * @return string
     */
    public static function getInvoiceAddressPhone()
    {
        if(self::_getInvoiceAddress()->phone)
            return substr(self::_getInvoiceAddress()->phone, 0, 50);
        else
            return substr(self::_getInvoiceAddress()->phone_mobile, 0, 50);

    }

    /**
     *
     * @return string
     */
    public static function getInvoiceAddressPhoneMobile()
    {
        return (string) substr(self::_getInvoiceAddress()->phone_mobile, 0, 50);
    }



    /**
     *
     * @var Address
     */
    private static $_addressDeliveryObj;

    /**
     *
     * @return Address
     */
    private static function _getDeliveryAddress()
    {
        if(self::$_addressDeliveryObj === null){
            global $cart;
            self::$_addressDeliveryObj = new Address((int) $cart->id_address_delivery);
        }

        return self::$_addressDeliveryObj;
    }

    /**
     * Title filed is used as name in presta for normal salutation filed
     * NO title field (ex Dr.) present in presta
     *
     * @return string
     */
    public static function getDeliveryAddressTitle()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    public static function getDeliveryAddressFirstName()
    {
        return substr(self::_getDeliveryAddress()->firstname, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getDeliveryAddressLastName()
    {
        return substr(self::_getDeliveryAddress()->lastname, 0, 50);
    }

    /**
     * In presta street is stored in address1 filed together with number
     *
     * @return string
     */
    public static function getDeliveryAddressStreet()
    {
        return substr(self::_getDeliveryAddress()->address1, 0, 50);
    }

    /**
     * In presta street is stored in address1 filed together with number
     * NO single number filed present
     *
     * @return string
     */
    public static function getDeliveryAddressStreetNo()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    public static function getDeliveryAddressAddition()
    {
        return (string) substr(self::_getDeliveryAddress()->address2, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getDeliveryAddressZip()
    {
        return substr(self::_getDeliveryAddress()->postcode, 0, 50);
    }

    /**
     *
     * @return string
     */
    public static function getDeliveryAddressCity()
    {
        return substr(self::_getDeliveryAddress()->city, 0, 50);
    }

    /**
     * @param object $cart
     * @return string|NULL
     */
    public static function getDeliveryAddressCountryISOCode()
    {
        $currentCountryObject = new Country((int) self::_getDeliveryAddress()->id_country);

        return BillpayUtils::getCountryISOAlpha3CodeByISOAlpha2Code($currentCountryObject->iso_code);
    }

    /**
     * Presta allow you to add only a phone filed fix or mobile
     * Phone filed is required for billpay get the filed that is present
     *
     * @return string
     */
    public static function getDeliveryAddressPhone()
    {
        if(self::_getDeliveryAddress()->phone)
            return substr(self::_getDeliveryAddress()->phone, 0, 50);
        else
            return substr(self::_getDeliveryAddress()->phone_mobile, 0, 50);

    }

    /**
     *
     * @return string
     */
    public static function getDeliveryAddressPhoneMobile()
    {
        return (string) substr(self::_getDeliveryAddress()->phone_mobile, 0, 50);
    }



    /**
     *
     * @var Language
     */
    private static $_languageObj;

    /**
     *
     * @return Language
     */
    private static function _geLanguage()
    {
        if(self::$_languageObj === null){
            global $cookie;
            self::$_languageObj = new Language((int) $cookie->id_lang);
        }

        return self::$_languageObj;
    }

    /**
     * @return string
     */
    public static function _getLanguageISO()
    {
        return strtolower(self::_geLanguage()->iso_code);
    }



    /**
     *
     * @var Carrier
     */
    private static $_carrierObj;

    /**
     *
     * @return Carrier
     */
    private static function _getCarrier()
    {
        if(self::$_carrierObj === null){
            global $cart;
            self::$_carrierObj = new Carrier((int) $cart->id_carrier);
        }

        return self::$_carrierObj;
    }

    /**
     * @return string
     */
    public static function getShippingName()
    {
        return (string) substr(self::_getCarrier()->name, 0, 50);
    }

    /**
     * @return integer
     */
    public static function getShippingPrice()
    {
        global $cart;
        return BillpayUtils::getBillpayFormattedPrice($cart->getOrderTotal(false, Cart::ONLY_SHIPPING));
    }

    /**
     * @return integer
     */
    public static  function getShippingPriceGross()
    {
        global $cart;
        return BillpayUtils::getBillpayFormattedPrice($cart->getOrderTotal(true, Cart::ONLY_SHIPPING));
    }

    /**
     * @return integer
     */
    public static function getCartBasePriceWithoutDiscounts()
    {
        global $cart;
        $onlyProductsTotal  = (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $onlyDiscountsPositive = (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        return BillpayUtils::getBillpayFormattedPrice($onlyProductsTotal - $onlyDiscountsPositive);
    }

    /**
     * @return integer
     */
    public static function getRebate()
    {
        global $cart;
        return BillpayUtils::getBillpayFormattedPrice(abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS)));
    }

    /**
     * @return integer
     */
    public static function getRebateGross()
    {
        global $cart;
        return BillpayUtils::getBillpayFormattedPrice(abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS)));
    }


    /**
     * @return integer
     */
    public static function getCartTotalPrice()
    {
        global $cart;
        return BillpayUtils::getBillpayFormattedPrice($cart->getOrderTotal(false));
    }

    /**
     * @return integer
     */
    public static function getCartTotalPriceGross()
    {
        global $cart;
        return BillpayUtils::getBillpayFormattedPrice($cart->getOrderTotal(true));
    }



    /**
     *
     * @return string
     */
    public static function getCurrency()
    {
        global $cart;
        $currentCurrencyObject = new Currency((int) $cart->id_currency);
        return strtoupper($currentCurrencyObject->iso_code);
    }

    /**
     *
     * @param int
     * @return string
     */
    public static function _getCurrencyBaseOnId($idCurrency)
    {
        $currentCurrencyObject = new Currency((int) $idCurrency);
        return strtoupper($currentCurrencyObject->iso_code);
    }



    /**
     *
     * @return string
     */
    public static function getReference()
    {
        return ''; // order id must be used as reference; use order update after prescore to add order id as reference
    }


    /**
     *
     * @return array Products
     */
    public static function getArticles()
    {
        global $cart;
        return $cart->getProducts();
    }

    /**
     *
     * @param array $article
     * @return integer
     */
    public static function getArticleAttributeId($article)
    {
        return $article['id_product_attribute'];
    }

    /**
     *
     * @param array $article
     * @return integer
     */
    public static function getArticleQuantity($article)
    {
        return $article['cart_quantity'];
    }


    /**
     *
     * @param array $article
     * @return string
     */
    public static function getArticleName($article)
    {
        // name - attributes; to match with the $article['name'] used in the backend
        return (string) substr(Tools::safeOutput($article['name'] . '-' . $article['attributes']), 0, 50);
    }

    /**
     *
     * @param array $article
     * @return string
     */
    public static function getArticleDescription($article)
    {
        return (string) substr(Tools::safeOutput($article['description_short']), 0, 50);
    }

    /**
     *
     * @param array $article
     * @return string
     */
    public static function getArticlePrice($article)
    {
        return BillpayUtils::getBillpayFormattedPrice((float) $article['price']);
    }

    /**
     *
     * @param array $article
     * @return number
     */
    public static function getArticlePriceGross($article)
    {
        return BillpayUtils::getBillpayFormattedPrice((float) $article['price_wt']);
    }
}
