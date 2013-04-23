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
 <a href="javascript:$('#billpaytransactioncredit_submit_form').submit();" title="{l s='Pay with Transaction Credit' mod='billpaytransactioncredit'}">
    <img style="height: 48px; width: 150px; vertical-align:middle" src="{$base_dir_ssl}modules/billpaytransactioncredit/views/templates/frontend/img/billpay_logo_payment.png" alt="{l s='Pay with Billpay Transaction Credit' mod='billpaytransactioncredit'}" />
    <strong>
        {l s='Pay with Transaction Credit' mod='billpaytransactioncredit'}
    </strong>
  </a>
</p>

<form id="billpaytransactioncredit_submit_form" action="{$base_dir_ssl}modules/billpaytransactioncredit/controllers/submit.php" title="{l s='Pay with Transaction Credit' mod='billpaytransactioncredit'}" method="post">
	<input type="hidden" name="billpay_transaction_credit" value="billpay_transaction_credit"/>
	<input type="hidden" name="billpay_submit" value="billpay_submit"/>
</form>