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

<h2>{l s='Transaction Credit' mod='billpaytransactioncredit'}</h2>

<p>
    &nbsp;
</p>

<p>
	<strong>{l s='The amounts due will be withdrawn from the bank account supplied when placing the order in monthly periods.' mod='billpaytransactioncredit'}</strong>
</p>

<table style="width: 70%; padding-left: 1.4em;"">
    <tbody>
        {foreach from=$transactionCreditData['dues'] key=k item=v}
           <tr>
               <td>{$k+1}. {l s='Rate:' mod='billpaytransactioncredit'}</td>
               <td>{displayPrice price=($v["value"]/100)}</td>
            </tr>
        {/foreach}
    </tbody>
</table>

<table style="width: 70%; padding-left: 1.4em;"">
    <tbody>
        <tr>
            <td colspan="3"><strong>{l s='Total price calculation:' mod='billpaytransactioncredit'}</strong></td>
        </tr>
        <tr>
    		<td>{l s='Cart total' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;=&nbsp;</td>
    		<td>{displayPrice price=($transactionCreditData['calculation']['base']/100)}</td>
    	</tr>
        <tr>
		    <td>{l s='Surcharge' mod='billpaytransactioncredit'}</td>
		    <td>&nbsp;&nbsp;+&nbsp;</td>
		    <td></td>
	    </tr>
	    <tr>
	        <td>({displayPrice price=($transactionCreditData['calculation']['base']/100)} x {($transactionCreditData['calculation']['interest']/100)|number_format:2:",":"."} x {$transactionCreditData['ratesNumber']}) / 100</td>
	        <td>&nbsp;&nbsp;=&nbsp;</td>
	        <td>{displayPrice price=($transactionCreditData['calculation']['surcharge']/100)}</td>
        </tr>
        <tr>
    		<td>{l s='Processing fee' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;+&nbsp;</td>
    		<td>{displayPrice price=($transactionCreditData['calculation']['fee']/100)}</td>
    	</tr>
    	<tr>
	        <td>{l s='Additional fees (e.g. shipping costs)' mod='billpaytransactioncredit'}</td>
	        <td>&nbsp;&nbsp;+&nbsp;</td>
	        <td>{displayPrice price=(($transactionCreditData['calculation']['cart'] - $transactionCreditData['calculation']['base'])/100)}</td>
        </tr>
        <tr>
	        <td colspan="3"><hr></td>
	    </tr>
        <tr style="font-weight: bold;">
    		<td>{l s='Grand total transaction credit' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;=&nbsp;</td>
    		<td><span class="price">{displayPrice price=($transactionCreditData['calculation']['total']/100)}</span></td>
        </tr>
    	<tr style="font-weight:bold">
            <td>{l s='effective APR' mod='billpaytransactioncredit'}</td>
            <td>&nbsp;&nbsp;=&nbsp;</td>
            <td>{($transactionCreditData['calculation']['anual']/100)|number_format:2:",":"."} %</td>
        </tr>
    </tbody>
</table>

<p>
	<strong>{l s='First rate due by direct debit one month after shipping date, following rates due by direct debit monthly on corresponding calendar day of subsequent month or following day, respectively (Example: Shipping date 2012/9/19, 1st Rate 2012/10/19, following rates on 19th of each subsequent month).' mod='billpaytransactioncredit'}</strong>
</p>

<p>
	&nbsp;
</p>

