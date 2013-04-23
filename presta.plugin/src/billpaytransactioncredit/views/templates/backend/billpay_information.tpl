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
        {l s='Billpay Information' mod='billpaytransactioncredit'}
    </legend>

    <p>
        <strong>{$displayName}</strong>
    </p>

    <p>
        {if !$billpayActivationPerformed}
            {l s='The due date for this order needs to be activated with Billpay.' mod='billpaytransactioncredit'}<sup style="color: #FF0000;">*</sup>
        {/if}
    </p>

    <hr>
    <div style="font-style: italic;">
        <div id="info">
            <p>
                {l s='The amounts due will be withdrawn from the bank account supplied when placing the order in monthly periods.' mod='billpaytransactioncredit'}<sup style="color: #FF0000;">*</sup>
            </p>

            <table cellspacing="0" cellpadding="0">
               {foreach from=$transactionCreditData['dues'] key=k item=v}
                   <tr>
                       <td width="55px;" style="text-align: right;">{$k+1}. {l s='Rate:' mod='billpaytransactioncredit'}</td>
                       <td style="padding-left:5px; text-align: right;">{displayPrice price=($v["value"]/100)}</td>
                       {if $v["date"]}
                           <td style="padding-left:5px; text-align: left;">({l s='due on:' mod='billpaytransactioncredit'} {$v["date"]})</td>
                       {/if}
                    </tr>
                {/foreach}
            </table>

            <hr>

            <p>
                <strong>{l s='Total price calculation:' mod='billpaytransactioncredit'}</strong>
            </p>

            <table>
            	<tr>
            		<td>{l s='Cart total' mod='billpaytransactioncredit'}</td>
            		<td>&nbsp;&nbsp;=&nbsp;</td>
            		<td style="text-align: right;">{displayPrice price=($transactionCreditData['calculation']['base']/100)}</td>
            	</tr>
                <tr>
        		    <td>{l s='Surcharge' mod='billpaytransactioncredit'}</td>
        		    <td>&nbsp;&nbsp;+&nbsp;</td>
        		    <td style="text-align: right;"></td>
        	    </tr>
        	    <tr>
    		        <td>({displayPrice price=($transactionCreditData['calculation']['base']/100)} x {($transactionCreditData['calculation']['interest']/100)|number_format:2:",":"."} x {$transactionCreditData['ratesNumber']}) / 100</td>
    		        <td>&nbsp;&nbsp;=&nbsp;</td>
    		        <td style="text-align: right;">{displayPrice price=($transactionCreditData['calculation']['surcharge']/100)}</td>
    	        </tr>
    	        <tr>
            		<td>{l s='Processing fee' mod='billpaytransactioncredit'}</td>
            		<td>&nbsp;&nbsp;+&nbsp;</td>
            		<td style="text-align: right;">{displayPrice price=($transactionCreditData['calculation']['fee']/100)}</td>
            	</tr>
            	<tr>
    		        <td>{l s='Additional fees (e.g. shipping costs)' mod='billpaytransactioncredit'}</td>
    		        <td>&nbsp;&nbsp;+&nbsp;</td>
    		        <td style="text-align: right;">{displayPrice price=(($transactionCreditData['calculation']['cart'] - $transactionCreditData['calculation']['base'])/100)}</td>
    	        </tr>
    	        <tr>
                    <td colspan="3"><hr></td>
    	        </tr>
    	        <tr style="font-weight: bold;">
            		<td>{l s='Grand total transaction credit' mod='billpaytransactioncredit'}</td>
            		<td>&nbsp;&nbsp;=&nbsp;</td>
            		<td style="text-align: right;">{displayPrice price=($transactionCreditData['calculation']['total']/100)}</td>
    	        </tr>
            	<tr style="font-weight:bold">
    	            <td>{l s='effective APR' mod='billpaytransactioncredit'}</td>
    	            <td>&nbsp;&nbsp;=&nbsp;</td>
    	            <td style="text-align: right;">{($transactionCreditData['calculation']['anual']/100)|number_format:2:",":"."} %</td>
    	        </tr>
    	    </table>

            <hr>

            <p>
                <sup style="color: #FF0000;">*</sup>{l s='First rate due by direct debit one month after shipping date, following rates due by direct debit monthly on corresponding calendar day of subsequent month or following day, respectively' mod='billpaytransactioncredit'}
                {l s='(Example: Shipping date 2012/9/19, 1st Rate 2012/10/19, following rates on 19th of each subsequent month).' mod='billpaytransactioncredit'}
            </p>
        </div>
    </div>
</fieldset>