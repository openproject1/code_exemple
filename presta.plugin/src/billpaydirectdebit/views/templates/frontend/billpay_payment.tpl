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

<p class="payment_module">
 <a href="javascript:$('#billpaydirectdebit_submit_form').submit();" title="{l s='Pay with Billpay Direct Debit' mod='billpaydirectdebit'}">
    <img style="height: 48px; width: 150px; vertical-align:middle" src="{$base_dir_ssl}modules/billpaydirectdebit/views/templates/frontend/img/billpay_logo_payment.png" alt="{l s='Pay with Billpay Direct Debit' mod='billpaydirectdebit'}" />
    <strong>{l s='Pay with Billpay Direct Debit' mod='billpaydirectdebit'}</strong>
    {if $extraBillpayFeeDD}
        {capture name="billpay_extra_fee"}
                {l s='plus %s payment fee' mod='billpaydirectdebit'}
        {/capture}
        {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeDD}
    {/if}
  </a>
</p>

<form id="billpaydirectdebit_submit_form" action="{$base_dir_ssl}modules/billpaydirectdebit/controllers/submit.php" title="{l s='Pay with Billpay Direct Debit' mod='billpaydirectdebit'}" method="post">
	<input type="hidden" name="billpay_direct_debit" value="billpay_direct_debit"/>
	<input type="hidden" name="billpay_submit" value="billpay_submit"/>
</form>