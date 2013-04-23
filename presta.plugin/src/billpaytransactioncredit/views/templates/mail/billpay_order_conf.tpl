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
<tr>
    <td style="background-color: #3385c1; color: #fff; font-size: 12px; font-weight: bold; padding: 0.5em 1em;" align="left">{l s='Transaction Credit' mod='billpaytransactioncredit'}</td>
</tr>

<tr>
    <td>&nbsp;</td>
</tr>

<tr>
    <td align="left">
        <strong>{l s='The amounts due will be withdrawn from the bank account supplied when placing the order in monthly periods.' mod='billpaytransactioncredit'}</strong>
    </td>
</tr>

<tr>
    <td align="left">
        <table style="width: 100%; font-family: Verdana,sans-serif; font-size: 11px; color: #374953;">
            <tbody>
                {foreach from=$transactionCreditData['dues'] key=k item=v}
                   <tr>
                       <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{$k+1}. {l s='Rate:' mod='billpaytransactioncredit'}</td>
                       <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{displayPrice price=($v["value"]/100)}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
   </td>
</tr>

<tr>
    <td align="left">
        <table style="width: 100%; font-family: Verdana,sans-serif; font-size: 11px; color: #374953; text-align: right;">
            <tbody>
                <tr>
                    <td colspan="3" style="background-color: #b9babe; padding: 0.6em 0.4em; text-align: left;"><strong>{l s='Total price calculation transaction credit' mod='billpaytransactioncredit'}</strong></td>
                </tr>
                <tr>
            		<td style="background-color: #b9babe; padding: 0.6em 0.4em;">{l s='Cart total' mod='billpaytransactioncredit'}</td>
            		<td style="background-color: #b9babe; padding: 0.6em 0.4em;">&nbsp;&nbsp;=&nbsp;</td>
            		<td style="background-color: #b9babe; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['base']/100)}</td>
            	</tr>
                <tr>
        		    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Surcharge' mod='billpaytransactioncredit'}</td>
        		    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">&nbsp;&nbsp;+&nbsp;</td>
        		    <td style="background-color: #ebecee; padding: 0.6em 0.4em;"></td>
        	    </tr>
        	    <tr>
    		        <td style="background-color: #ebecee; padding: 0.6em 0.4em;">({displayPrice price=($transactionCreditData['calculation']['base']/100)} x {($transactionCreditData['calculation']['interest']/100)|number_format:2:",":"."} x {$transactionCreditData['ratesNumber']}) / 100</td>
    		        <td style="background-color: #ebecee; padding: 0.6em 0.4em;">&nbsp;&nbsp;=&nbsp;</td>
    		        <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['surcharge']/100)}</td>
    	        </tr>
    	        <tr>
            		<td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Processing fee' mod='billpaytransactioncredit'}</td>
            		<td style="background-color: #ebecee; padding: 0.6em 0.4em;">&nbsp;&nbsp;+&nbsp;</td>
            		<td style="background-color: #ebecee; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['fee']/100)}</td>
            	</tr>
            	<tr>
    		        <td style="background-color: #dde2e6; padding: 0.6em 0.4em;">{l s='Additional fees (e.g. shipping costs)' mod='billpaytransactioncredit'}</td>
    		        <td style="background-color: #dde2e6; padding: 0.6em 0.4em;">&nbsp;&nbsp;+&nbsp;</td>
    		        <td style="background-color: #dde2e6; padding: 0.6em 0.4em;">{displayPrice price=(($transactionCreditData['calculation']['cart'] - $transactionCreditData['calculation']['base'])/100)}</td>
    	        </tr>
    	        <tr style="font-weight: bold;">
            		<td style="background-color: #f1aecf;; padding: 0.6em 0.4em;">{l s='Grand total transaction credit' mod='billpaytransactioncredit'}</td>
            		<td style="background-color: #f1aecf; padding: 0.6em 0.4em;">&nbsp;&nbsp;=&nbsp;</td>
            		<td style="background-color: #f1aecf; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['total']/100)}</td>
    	        </tr>
            	<tr style="font-weight:bold">
    	            <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='effective APR' mod='billpaytransactioncredit'}</td>
    	            <td style="background-color: #ebecee; padding: 0.6em 0.4em;">&nbsp;&nbsp;=&nbsp;</td>
    	            <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{($transactionCreditData['calculation']['anual']/100)|number_format:2:",":"."} %</td>
    	        </tr>
            </tbody>
        </table>
   </td>
</tr>

<tr>
    <td align="left">
        <strong>{l s='First rate due by direct debit one month after shipping date, following rates due by direct debit monthly on corresponding calendar day of subsequent month or following day, respectively (Example: Shipping date 2012/9/19, 1st Rate 2012/10/19, following rates on 19th of each subsequent month).' mod='billpaytransactioncredit'}</strong>
    </td>
</tr>

<tr>
    <td>&nbsp;</td>
</tr>

