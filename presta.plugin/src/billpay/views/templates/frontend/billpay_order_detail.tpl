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

<!-- overwrite original presta table style -->
<style type="text/css">
    {literal}
        table#billpay-left tbody td,
        table#billpay-left thead td
        {
            text-align: left;
        }
    {/literal}
</style>

<div id="order-detail-content" class="table_block">
    <table class="std" id="billpay-left">
        <thead>
            <tr>
                <th colspan="2" class="first_item">{$displayName}</th>
            </tr>
        </thead>

        <tbody>
            {if $billpayInvoiceDuedate}
        	<tr class="item">
    	        <td colspan="2">
    	        	{capture name="billpay_invoice_thank_you_text"}
   				 	   {l s='Please transfer the total amount with billpay transaction number as reason for payment (%s) until %s to the following account:' mod='billpay'}
    				{/capture}
    				<strong>{$smarty.capture.billpay_invoice_thank_you_text|sprintf:$billpayBankData["invoice_reference"]: $billpayInvoiceDuedate}<strong>
    	        </td>
    	    </tr>
    	    {else}
        	<tr class="item">
    	        <td colspan="2">
    	        	{capture name="billpay_invoice_thank_you_text"}
   				 	   {l s='Please transfer the total amount with billpay transaction number as reason for payment (%s) to the following account:' mod='billpay'}
    				{/capture}
    				<strong>{$smarty.capture.billpay_invoice_thank_you_text|sprintf:$billpayBankData["invoice_reference"]}<strong>
    	        </td>
    	    </tr>
    	    {/if}

	        <tr class="item">
    	        <td>{l s='Account holder:' mod='billpay'}</td>
                <td>{$billpayBankData["account_holder"]}</td>
    	    </tr>
    	    <tr class="item">
    	        <td>{l s='Bank name:' mod='billpay'}</td>
                <td>{$billpayBankData["bank_name"]}</td>
    	    </tr>
    	    <tr class="item">
    	        <td>{l s='Bank code:' mod='billpay'}</td>
                <td>{$billpayBankData["bank_code"]}</td>
    	    </tr>
    	    <tr class="item">
    	        <td>{l s='Account number:' mod='billpay'}</td>
                <td>{$billpayBankData["account_number"]}</td>
    	    </tr>
    	    <tr class="item">
    	        <td>{l s='Due date:' mod='billpay'}</td>
                <td>{if $billpayInvoiceDuedate} {$billpayInvoiceDuedate} {else} ({l s='received within the invoice' mod='billpay'}) {/if}</td>
    	    </tr>
    	    <tr class="item">
    	        <td>{l s='Reason for payment:' mod='billpay'}</td>
                <td>{$billpayBankData["invoice_reference"]}</td>
    	    </tr>
        </tbody>
    </table>
</div>