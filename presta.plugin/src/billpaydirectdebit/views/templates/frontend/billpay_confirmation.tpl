{**
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
 *}

<p style="margin: 1.5em 0em">
    {l s='Your order on' mod='billpaydirectdebit'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='billpaydirectdebit'}
</p>

<h2>{$displayName}</h2>

<p>
    <img src="modules/billpaydirectdebit/views/templates/frontend/img/billpay_logo_payment.png" alt="{$displayName}" title="{$displayName}"/>
</p>

<p>
	<strong>{l s='Within the next few days, we will withdraw the amount due from the bank account supplied when placing the order.' mod='billpaydirectdebit'}</strong>
</p>

<p>
	&nbsp;
</p>