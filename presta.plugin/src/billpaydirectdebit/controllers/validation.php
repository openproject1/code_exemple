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

include_once(dirname(__FILE__) . '/../../../config/config.inc.php');
include_once(dirname(__FILE__) . '/../../../header.php');

$billpayDirectDebit = Module::getInstanceByName("billpaydirectdebit");

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$billpayDirectDebit->active)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

if (!$cookie->isLogged(true))
    Tools::redirect('authentication.php?back=order.php');
elseif (!Customer::getAddressesTotalById((int)($cookie->id_customer)))
    Tools::redirect('address.php?back=order.php?step=1');

//TODO submit to same page
BillpaySessionUtils::deleteSessionVar('billpaySubmitErrors');
BillpaySessionUtils::deleteSessionVar('billpayRequestError');

if($billpayDirectDebit->billpayValidatePayment() === false)
    Tools::redirect('modules/billpaydirectdebit/controllers/submit.php');

if($billpayDirectDebit->sendBillpayPreauthorizeRequest() === false)
    Tools::redirect('modules/billpaydirectdebit/controllers/submit.php');

// presta default validateOrder
$billpayDirectDebit->validateOrder($cart);

if ($billpayDirectDebit->updateBillpayOrderReferenceRequest() === false)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

if($billpayDirectDebit->saveBillpayPreauthorizeRequestResultsToDb() === false)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// reset Profiling Tag Count
BillpaySessionUtils::deleteSessionVar('billpayProfilingTagCount');

$billpayDirectDebit->redirectToOrderConfirmation($cart);