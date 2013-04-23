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
 <a href="javascript:$('#billpay_submit_form').submit();" title="{l s='Pay with Billpay Invoice' mod='billpay'}">
    <img style="height: 48px; width: 150px; vertical-align:middle" src="../modules/billpay/views/templates/frontend/img/billpay_logo_payment.png" alt="{l s='Pay with Billpay Invoice' mod='billpay'}" />
        <span>
            <strong>{l s='Pay with Billpay Invoice' mod='billpay'}</strong>
            {if $BILLPAY_CUSTOMER_GROUP == $billpayCustomerGroups['b2c']}
                {if $extraBillpayFeeB2C}
                    {capture name="billpay_extra_fee"}
                        {l s='plus %s payment fee' mod='billpay'}
                    {/capture}
                    {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeB2C}
                {/if}
            {/if}
            {if $BILLPAY_CUSTOMER_GROUP == $billpayCustomerGroups['b2b']}
                {if $extraBillpayFeeB2B}
                    {capture name="billpay_extra_fee"}
                        {l s='plus %s payment fee' mod='billpay'}
                    {/capture}
                    {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeB2B}
                {/if}
            {/if}
        </span>
        {if $BILLPAY_CUSTOMER_GROUP == $billpayCustomerGroups['both']}
            <span style="float:left; margin-left: 160px; margin-top: -30px;">
            {if $extraBillpayFeeB2C}
                <br /> -
                {capture name="billpay_extra_fee"}
                    {l s='plus %s private customer payment fee' mod='billpay'}
                {/capture}
                {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeB2C}
            {/if}
            {if $extraBillpayFeeB2B}
                <br /> -
                {capture name="billpay_extra_fee"}
                    {l s='plus %s business customer payment fee' mod='billpay'}
                {/capture}
                {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeB2B}
            {/if}
            </span>
        {/if}
    <br style="clear:both;">
  </a>
</p>

<form id="billpay_submit_form" action="{$base_dir_ssl}modules/billpay/controllers/submit.php" title="{l s='Pay with Billpay Invoice' mod='billpay'}" method="post">
	<input type="hidden" name="billpay_invoice" value="billpay_invoice"/>
	<input type="hidden" name="billpay_submit" value="billpay_submit"/>
</form>