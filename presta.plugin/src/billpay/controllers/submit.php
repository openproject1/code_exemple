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

if (!$cookie->isLogged(true))
	Tools::redirect('authentication.php?back=order.php');
elseif (!Customer::getAddressesTotalById((int)($cookie->id_customer)))
	Tools::redirect('address.php?back=order.php?step=1');

$billpay = Module::getInstanceByName("billpay");

if(!BillpaySessionUtils::hasSessionVar('billpayModuleConfig'))
{
    $url = (_PS_VERSION_ < '1.5') ? 'order.php?step=3' : 'index.php?controller=order&step=3';
    Tools::redirect($url);
}

echo $billpay->submitPayment();

include_once(dirname(__FILE__).'/../../../footer.php');