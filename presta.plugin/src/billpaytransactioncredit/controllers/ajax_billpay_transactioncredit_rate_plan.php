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

include_once(dirname(__FILE__) . '/../../../config/config.inc.php');;
include_once(dirname(__FILE__) . '/../../../init.php');

$billpaytransactioncredit = Module::getInstanceByName("billpaytransactioncredit");
echo $billpaytransactioncredit->ajaxBillpayTransactionCreditCall($_POST['monthsRatePlan']);