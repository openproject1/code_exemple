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
	{l s='Your order on' mod='billpay'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='billpay'}
</p>

<h2>{$displayName}</h2>

<p>
    <img src="modules/billpay/views/templates/frontend/img/billpay_logo_payment.png" alt="{$displayName}" title="{$displayName}"/>
</p>

<p>
    {capture name="billpay_invoice_thank_you_text"}
        {l s='Please transfer the total amount with billpay transaction number as reason for payment (%s) within the payment period to the following account:' mod='billpay'}
    {/capture}
    <strong>{$smarty.capture.billpay_invoice_thank_you_text|sprintf:$billpayBankData["invoice_reference"]}</strong>
</p>

<table style="width: 65%; padding-left: 1.4em;">
    <tbody>
        <tr>
            <td>{l s='Account holder:' mod='billpay'}</td>
            <td>{$billpayBankData["account_holder"]}</td>
        </tr>

        <tr>
            <td>{l s='Bank name:' mod='billpay'}</td>
            <td>{$billpayBankData["bank_name"]}</td>
        </tr>

        <tr>
            <td>{l s='Bank code:' mod='billpay'}</td>
            <td>{$billpayBankData["bank_code"]}</td>
        </tr>
        <tr>

            <td>{l s='Account number:' mod='billpay'}</td>
            <td>{$billpayBankData["account_number"]}</td>
        </tr>
        <tr>
            <td>{l s='Due date:' mod='billpay'}</td>
            <td>({l s='received within the invoice' mod='billpay'})</td>
        </tr>

        <tr>
            <td>{l s='Reason for payment:' mod='billpay'}</td>
            <td>{$billpayBankData["invoice_reference"]}</td>
        </tr>
    </tbody>
</table>

<p>
	&nbsp;
</p>

