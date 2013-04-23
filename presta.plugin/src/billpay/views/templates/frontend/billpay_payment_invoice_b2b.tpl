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

{capture name=path}{$displayName}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{$displayName}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div style="border:1px solid #0064b2; padding:10px;">

    <p>
         <img src="../views/templates/frontend/img/billpay_logo.png" alt="{$displayName}" title="{$displayName}"/>
    </p>

    <h3>{$displayName}</h3>

    {if $BILLPAY_TRANSACTION_MODE == $billpayTransactionModesTest}
        <p>
            {include file="$_PS_MODULE_DIR_/billpay/views/templates/frontend/inc/billpay_payment_warning_info_block.tpl"}
        </p>
    {/if}

    <script type="text/javascript">
	//<![CDATA[
		var msg = "{l s='You must agree to the billpay invoice payment terms of service before continuing.' mod='billpay' js=1}";
	//]]>
	</script>
    <script type="text/javascript" src="/modules/billpay/views/templates/frontend/js/frontend.js"></script>

    <p>
        <strong>
            {l s='Invoice payment:' mod='billpay'}
            {if $extraBillpayFeeB2B}
                {capture name="billpay_extra_fee"}
                    {l s='plus %s payment fee' mod='billpay'}
                {/capture}
                {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeB2B}:
            {/if}
        </strong>
    </p>

    {if isset($smarty.session.billpaySubmitErrors)}
        <div id="billpay_submit_errors" name="billpay_submit_errors" style="color: #DA0F00; margin: 0.5em 0; padding-left: 0.7em;">
            {l s='Please fill the required fields:' mod='billpay'}
            <sup>*</sup>
            <ol>
            {foreach from=$smarty.session.billpaySubmitErrors item=billpaySubmitError}
                <li style="font-weight: bold; margin: 0.2em 0 0 2.2em;">{$billpaySubmitError}</li>
            {/foreach}
            </ol>
        </div>
    {/if}

    {if isset($smarty.session.billpayRequestError)}
        <div id="billpay_request_error" name="billpay_request_error" style="font-weight: bold; color: #DA0F00; margin: 0.5em 0; padding-left: 0.7em;">
            <strong>{$smarty.session.billpayRequestError}</strong>
        </div>
    {/if}

    {include file="$_PS_MODULE_DIR_/billpay/views/templates/frontend/inc/billpay_payment_invoice_b2b_form.tpl"}

	{include file="$_PS_MODULE_DIR_/billpay/views/templates/frontend/inc/billpay_profiling_tags.tpl"}

</div>