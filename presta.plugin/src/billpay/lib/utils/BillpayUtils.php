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

class BillpayUtils
{
    const INFO    = 0;
    const ERROR   = 1;

    const LOG_FILE_DATE_FORMAT = 'd.m.Y H:i:s';

    /**
     * Writes given log message. Returns write state
     *
     * @param string $message
     * @param int    $type; [INFO] = 0, [ERROR] = 1;
     * @param bool   $newline
     * @param string $logFileName
     *
     * @return bool
     */
    public static function writeToLogFile($message, $type = 0, $newline = true, $logFileName = 'billpay.log')
    {
        if (($handle = fopen(self::_determineLogFilePath() . $logFileName, 'a')) !== false )
        {
            fwrite($handle, self::_prepareLogMessageString($message, $type, $newline));
            fclose($handle);
        }
    }

    /**
     *
     * @return string
     */
    private static function _determineLogFilePath()
    {
        $logFilePath = _PS_MODULE_DIR_.'billpay/log/';

        // In case module was manualy copied and/or the write rules to the module log files are missing
        // use presta log folder as folder for billpay log file
        if (!is_writable($logFilePath) OR !$dh = opendir($logFilePath))
            $logFilePath = _PS_ROOT_DIR_.'/log/';

        return $logFilePath;
    }

    /**
     *
     * @param string $message
     * @param int    $type; [INFO] = 0, [ERROR] = 1;
     * @param bool $newline
     * @return string
     */
    private static function _prepareLogMessageString($message, $type, $newline)
    {
        if (is_array($message)) {
            $message = print_r($message, true);
        }

        $dateString = '[' . date(self::LOG_FILE_DATE_FORMAT) . ']';

        if ((int) $type == self::INFO)
            $message  = $dateString. ' [INFO] ' . $message;
        else
            $message  = $dateString. ' [ERROR] ' . $message;

        $newline ? $message .= "\n" : $message;

        return $message;
    }

    /**
     * TODO: extend country codes array when billpay gonna be available for more countries
     *
     * @param string $countryAlpha2Code
     * @return string|null
     */
    public static function getCountryISOAlpha3CodeByISOAlpha2Code($countryISOAlpha2Code)
    {
        $countryISOAlpha3CodeByISOAlpha2Code = array('DE' => 'DEU',
                                                     'AT' => 'AUT',
                                                     'CH' => 'CHE',);

        return $countryISOAlpha3CodeByISOAlpha2Code[strtoupper($countryISOAlpha2Code)];
    }

    /**
     *
     * @param float $value
     * @return integer
     */
    public static function getBillpayFormattedPrice($value)
    {
        return (int) round($value * 100);
    }

    /**
     *
     * @param string $feeType
     * @return string
     */
    public static function getExtraBillpayFeeBaseOnShopCurrency($feeType)
    {
        global $cart;

        $billpayExtraFeeArray = explode(';', Configuration::get('BILLPAY_FEE_'.$feeType));

        foreach($billpayExtraFeeArray as $billpayExtraFee)
        {
            $billpayExtraFeeCurrency = explode(":", $billpayExtraFee);

            if(strtoupper($billpayExtraFeeCurrency[0]) == strtoupper(BillpayGatherDataUtils::getCurrency()))
            {
                // when currencies match return; always one active currency in shop
                if (strstr($billpayExtraFeeCurrency[1], '%'))
                    return $billpayExtraFeeCurrency[1]; // extra costs with %

                return $billpayExtraFeeCurrency[1]; // extra costs
            }
        }
    }

    /**
     *
     * @param double $orderTotal
     * @param string $extraPaymentFeeString
     * @return Ambigous <number, unknown>|unknown
     */
    public static function getOrderTotalWithExtraBillpayPaymentFee($orderTotal, $extraPaymentFeeString = '')
    {
        if ($extraPaymentFeeString){

            // float value parse string with '.'
            $extraPaymentFeeString = str_replace(',', '.', $extraPaymentFeeString);

            if(strpos($extraPaymentFeeString, '%')) {
                $extraPaymentPercentageFee = str_replace('%', '', $extraPaymentFeeString);
                $orderTotalWithAddedPercentageFee = $orderTotal + ($orderTotal  * ((float) $extraPaymentPercentageFee / 100));

                return Tools::ps_round((float)$orderTotalWithAddedPercentageFee, 2);
            }
            else {
                $orderTotalWithAddedFee = $orderTotal + (float) $extraPaymentFeeString;

                return Tools::ps_round((float)$orderTotalWithAddedFee, 2);
            }
        }

        return $orderTotal;
    }

    /**
     * TODO: further develop when new countries will be available
     * @return string
     */
    public static function getDatenschutzLinkForCountryWithLanguage()
    {
        if(BillpayGatherDataUtils::getDeliveryAddressCountryISOCode() == 'DE')
            return 'https://www.billpay.de/kunden/agb?lang=' . strtolower(BillpayGatherDataUtils::_getLanguageISO()) . '/#datenschutz';
        if(BillpayGatherDataUtils::getDeliveryAddressCountryISOCode() == 'AT')
            return 'https://www.billpay.de/kunden/agb-at?lang=' . strtolower(BillpayGatherDataUtils::_getLanguageISO()) . '/#datenschutz';
        if(BillpayGatherDataUtils::getDeliveryAddressCountryISOCode() == 'CH')
            return 'https://www.billpay.de/kunden/agb-at?lang=' . strtolower(BillpayGatherDataUtils::_getLanguageISO()) . '/#datenschutz';

        return 'https://www.billpay.de/kunden/agb?lang=' . strtolower(BillpayGatherDataUtils::_getLanguageISO()) . '/#datenschutz'; // fail safe
    }

    /**
     * @return string
     */
    public static function getDatenschutzLinkTransactionCredit()
    {
        if (Configuration::get('BILLPAY_TRANSACTION_MODE') == BillpayPaymentModule::$billpayTransactionModes['test'])
            return 'https://www.billpay.de/s/agb-beta/'.md5(substr(md5(Configuration::get('BILLPAY_API_PASSWORD')), 0, 10)).'.html';
        elseif (Configuration::get('BILLPAY_TRANSACTION_MODE') == BillpayPaymentModule::$billpayTransactionModes['live'])
            return 'https://www.billpay.de/s/agb/'.md5(substr(md5(Configuration::get('BILLPAY_API_PASSWORD')), 0, 10)).'.html';

        return ''; // fail safe
    }

    /**
     *
     * @return number
     */
    public static function billpayDateDays()
    {
        for ($i = 1; $i != 32; $i++){
            if ($i<11)
                $i  = sprintf("%02s", $i ); // add leading 0
            $tab[] = $i;
        }
        return $tab;
    }

    /**
     *
     * @return string
     */
    public static function billpayDateMonths()
    {
        for ($i = 1; $i != 13; $i++){
            if ($i < 11)
                $i  = sprintf("%02s", $i ); // add leading 0
			$tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));
        }
		return $tab;
	}

	/**
	 *
	 * @return number
	 */
	public static function billpayDateYears()
	{
	    for ($i = date('Y') - 18; $i >= 1900; $i--)
	        $tab[] = $i;

	    return $tab;
	}

	/**
	 *
	 * @param string $prestaBirthdateString
	 * @return string
	 */
	public static function parsePrestaBirthdateStringInBillpayformat($prestaBirthdateString)
	{
	    if(!$prestaBirthdateString)
	        return '';

	    return date('Ymd', strtotime( $prestaBirthdateString ) ); // billpay format YYYMMDD
	}

    /**
     *
     * @param string $billpayInvoiceDuedate
     * @return string
     */
	public static function parseBillpayInvoiceDuedateToReadableFromat($billpayInvoiceDuedate)
	{
        if(!$billpayInvoiceDuedate)
	        return '';

        return date('d.m.Y', strtotime( $billpayInvoiceDuedate ) );
	}

	/**
	 *
	 * @param string $orderID
	 * @param string $merchantID
	 * @return string
	 */
	public static function createBillpayInvoiceRefernece($orderID, $merchantID)
	{
        return 'BP' . (string) $orderID . '/' . (string) $merchantID;
	}
}