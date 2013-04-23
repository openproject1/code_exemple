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

<br>

<fieldset style="width: 400px;">
    <legend>
        <img src="../modules/billpay/logo.gif">
        {l s='Billpay Information' mod='billpay'}
    </legend>

    <p>
        <strong>{$displayName}</strong>
    </p>

    <p>
        {if !$billpayBankData["activation_performed"]}
            {l s='The due date for this order needs to be activated with Billpay.' mod='billpay'}<sup style="color: #FF0000;">*</sup>
        {/if}
    </p>

    <hr>
    <div style="font-style: italic;">
        <p>
            {capture name="billpay_invoice_thank_you_text"}
                {l s='Please transfer the total amount with billpay transaction number as reason for payment ( %s ) until %s to the following account:' mod='billpay'}
            {/capture}
            {$smarty.capture.billpay_invoice_thank_you_text|sprintf:$billpayBankData["invoice_reference"]: $billpayInvoiceDuedate}
        </p>

        <div id="info">
           <table width="400px;" cellspacing="0" cellpadding="0">
        	    <tr>
        	        <td width="135px;">{l s='Account holder:' mod='billpay'}</td>
                    <td>{$billpayBankData["account_holder"]}</td>
        	    </tr>
        	    <tr>
        	        <td width="135px;">{l s='Bank name:' mod='billpay'}</td>
                    <td>{$billpayBankData["bank_name"]}</td>
        	    </tr>
        	    <tr>
        	        <td width="135px;">{l s='Bank code:' mod='billpay'}</td>
                    <td>{$billpayBankData["bank_code"]}</td>
        	    </tr>
        	    <tr>
        	        <td width="135px;">{l s='Account number:' mod='billpay'}</td>
                    <td>{$billpayBankData["account_number"]}</td>
        	    </tr>
        	    <tr>
        	        <td width="135px;">{l s='Due date:' mod='billpay'}</td>
                    <td>{$billpayInvoiceDuedate}</td>
        	    </tr>
        	    <tr>
        	        <td width="135px;">{l s='Reason for payment:' mod='billpay'}</td>
                    <td>{$billpayBankData["invoice_reference"]}</td>
        	    </tr>
        	</table>
        </div>
    </div>
</fieldset>