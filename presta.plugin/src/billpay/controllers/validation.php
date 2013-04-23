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

$billpay = Module::getInstanceByName("billpay");

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$billpay->active)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

if (!$cookie->isLogged(true))
    Tools::redirect('authentication.php?back=order.php');
elseif (!Customer::getAddressesTotalById((int)($cookie->id_customer)))
    Tools::redirect('address.php?back=order.php?step=1');

//TODO
BillpaySessionUtils::deleteSessionVar('billpaySubmitErrors');
BillpaySessionUtils::deleteSessionVar('billpayRequestError');

if($billpay->billpayValidatePayment() === false)
    Tools::redirect('modules/billpay/controllers/submit.php');

if($billpay->sendBillpayPreauthorizeRequest() === false)
    Tools::redirect('modules/billpay/controllers/submit.php');

// presta default validateOrder
$billpay->validateOrder($cart);

if ($billpay->updateBillpayOrderReferenceRequest() === false)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

if($billpay->saveBillpayPreauthorizeRequestResultsToDb() === false)
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// reset Profiling Tag Count
BillpaySessionUtils::deleteSessionVar('billpayProfilingTagCount');

$billpay->redirectToOrderConfirmation($cart);