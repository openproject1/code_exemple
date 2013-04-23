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

class BillpayDbUtils
{
    /**
     *
     * @return boolean
     */
    public static function createBillpayTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'billpay` (
            `order_id` varchar(16) NOT NULL,
            `account_holder` varchar(255) NOT NULL,
			`account_number` varchar(40) NOT NULL,
			`bank_code` varchar(16) NOT NULL,
			`bank_name` varchar(255) NOT NULL,
            `invoice_reference` varchar(255) NOT NULL,
            `activation_performed` int(11) NOT NULL,
            `invoice_duedate` int(11) NOT NULL,
            PRIMARY KEY (`order_id`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8';

        if (!Db::getInstance()->Execute($sql))
            return false;

        return true;
    }

    /**
     * Create transaction credit data column if they do not exist yet
     *
     * @return void
     */
    public static function addTransactionCreditDataColumn()
    {
        $sql = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = "' . _DB_NAME_ . '"
                AND TABLE_NAME     = "' . _DB_PREFIX_ .'billpay"
                AND COLUMN_NAME    = "transaction_credit_data"';

        $row = Db::getInstance()->getRow($sql);

        if ((int) Db::getInstance()->NumRows() == 0)
        {
            $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'billpay` ADD `transaction_credit_data` TEXT NOT NULL';
            return Db::getInstance()->Execute($sql);
        }

        return true;
    }

   /**
    *
    * @param string $orderId
    * @param array $results
    * @return boolean
    */
    public static function insertBankDataResultsToBillpayTable($orderId, $results)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'billpay`
                            (`order_id`, `account_holder`, `account_number`, `bank_code`, `bank_name`, `invoice_reference`, `activation_performed`, `invoice_duedate`)
                VALUES      ("'.(string) $orderId.'", "'.$results["account_holder"].'", "'.$results["account_number"].'", "'.$results["bank_code"].'", "'.$results["bank_name"].'", "'.$results["invoice_reference"].'", '.(int) $results["activation_performed"].', '.(int) $results["invoice_duedate"].')';

        if (!Db::getInstance()->Execute($sql))
            return false;

        return true;
    }

   /**
    *
    * @param string $orderId
    * @param array $results
    * @return boolean
    */
    public static function updateBankDataResultsToBillpayTable($orderId, $results)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'billpay`
                SET   `account_holder` = "'.$results["account_holder"].'",
                      `account_number` = "'.$results["account_number"].'",
                      `bank_code` = "'.$results["bank_code"].'",
                      `bank_name` = "'.$results["bank_name"].'",
                      `invoice_reference` = "'.$results["invoice_reference"].'",
                      `activation_performed` = "'.(int) $results["activation_performed"].'",
                      `invoice_duedate` = "'.(int) $results["invoice_duedate"].'"
                WHERE `order_id` = "'.(string) $orderId.'"';

        if (!Db::getInstance()->Execute($sql))
            return false;

        return true;
    }
    
    /**
     *
     * @param string $orderId
     * @param array $results
     * @return boolean
     */
    public static function updateBankDataResultsToBillpayTableForTransactionCredit($orderId, $results)
    {
    	$sql = 'UPDATE `' . _DB_PREFIX_ . 'billpay`
                SET   `account_holder` = "'.$results["account_holder"].'",
                      `account_number` = "'.$results["account_number"].'",
                      `bank_code` = "'.$results["bank_code"].'",
                      `bank_name` = "'.$results["bank_name"].'",
                      `invoice_reference` = "'.$results["invoice_reference"].'",
                      `activation_performed` = "'.(int) $results["activation_performed"].'",
                      `invoice_duedate` = "'.(int) $results["invoice_duedate"].'",
                      `transaction_credit_data` = \''.$results["transaction_credit_data"].'\'
                WHERE `order_id` = "'.(string) $orderId.'"';
    
    	if (!Db::getInstance()->Execute($sql))
    		return false;
    
    	return true;
    }

   /**
    *
    * @param string $orderId
    * @return array
    */
    public static function selectAllDataFromBillpayTable($orderId)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'billpay`
                 WHERE `order_id` = "'.(string) $orderId.'"';

        return Db::getInstance()->getRow($sql);
    }

    /**
     *
     * @param string $orderId
     * @param string $transactionCreditData
     * @return boolean
     */
    public static function updateTransactionCreditDataToBillpayTable($orderId, $transactionCreditData)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'billpay`
                SET   `transaction_credit_data` = \''.$transactionCreditData.'\'
                WHERE `order_id` = "'.(string) $orderId.'"';

        if (!Db::getInstance()->Execute($sql))
            return false;

        return true;
    }
}