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
        div#billpay_transactioncredit_rate_plan
        {
            height: 210px;
        	background-color: #fff;
        	border-bottom:1px solid #BDC2C9;
        	padding:10px;
        }
        div#billpay_transactioncredit_rate_plan tbody td,
        div#billpay_transactioncredit_rate_plan tfoot td
        {
        	border: none;
        	padding: 0px;
        }
    {/literal}
</style>

<div id="billpay_transactioncredit_rate_plan">

     {capture name="rates_payment_installments"}
         {l s='Your rates payment in %s monthly installments' mod='billpaytransactioncredit'}
     {/capture}

    <h4 style="text-align: center;">{$smarty.capture.rates_payment_installments|sprintf:$monthsRatePlan}</h4>

    <table style="padding-top: 10px; float: left;">
    	<tr>
    		<td>{l s='Cart total:' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;=&nbsp;</td>
    		<td style="text-align: right;">{displayPrice price=($selectedRatePlan['calculation']['base']/100)}</td>
    	</tr>
    	<tr>
    		<td>{l s='Surcharge' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;+&nbsp;</td>
    		<td style="text-align: right;"></td>
    	</tr>
    	<tr>
    		<td>({displayPrice price=($selectedRatePlan['calculation']['base']/100)} x {($selectedRatePlan['calculation']['interest']/100)|number_format:2:",":"."} x {$monthsRatePlan}) / 100</td>
    		<td>&nbsp;&nbsp;=&nbsp;</td>
    		<td style="text-align: right;">{displayPrice price=($selectedRatePlan['calculation']['surcharge']/100)}</td>
    	</tr>
    	<tr>
    		<td>{l s='Processing fee excl. VAT' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;+&nbsp;</td>
    		<td style="text-align: right;">{displayPrice price=($selectedRatePlan['calculation']['fee']/100)}</td>
    	</tr>
    	<tr>
    		<td>{l s='Additional fees (e.g. shipping costs)' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;+&nbsp;</td>
    		<td style="text-align: right;">{displayPrice price=(($selectedRatePlan['calculation']['cart'] - $selectedRatePlan['calculation']['base'])/100)}</td>
    	</tr>
    	<tr>
    	    <td colspan="3"><hr></td>
    	</tr>
    	<tr style="font-weight: bold;">
    		<td>{l s='Grand total transaction credit' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;&nbsp;=&nbsp;</td>
    		<td style="text-align: right;">
    		    <span class="price">
    		        {displayPrice price=($selectedRatePlan['calculation']['total']/100)}
    		    </span>
    		</td>
    	</tr>

        <input type="hidden" name="tct" value="{$selectedRatePlan['calculation']['total']/100|floatval}"/>

    	<tr>
    		<td>{l s='Divided by number of rates' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;</td>
    		<td style="text-align: right;">{$monthsRatePlan} {l s='rates' mod='billpaytransactioncredit'}</td>
    	</tr>

    	<input type="hidden" name="tcr" value="{$monthsRatePlan}"/>

    	<tr>
    		<td>{l s='The first rate including fees amounts to' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;</td>
    		<td style="text-align: right; font-weight: bold">{displayPrice price=($selectedRatePlan['dues'][0]['value']/100)}</td>
    	</tr>
    	<tr>
    		<td>{l s='Each following rate amounts to' mod='billpaytransactioncredit'}</td>
    		<td>&nbsp;</td>
    		<td style="text-align: right; font-weight: bold">{displayPrice price=($selectedRatePlan['dues'][1]['value']/100)}</td>
    	</tr>
    	<tr>
    	    <td colspan="3"><hr></td>
    	</tr>
    	<tr style="font-weight:bold">
    	    <td>{l s='effective APR' mod='billpaytransactioncredit'}</td>
    	    <td>&nbsp;&nbsp;=&nbsp;</td>
    	    <td style="text-align: right;">{($selectedRatePlan['calculation']['anual']/100)|number_format:2:",":"."} %</td>
    	</tr>
    </table>

    <div style="padding-top: 10px; text-align: center;">
        <span>
            <a href='{$agb}' target='blank'>{l s='Terms and conditions for transaction credit' mod='billpaytransactioncredit'}</a>
        </span>
        <br>
        <span>
            <a href='https://www.billpay.de/api/ratenkauf/datenschutz' target='blank'>{l s='Data Protection Regulations' mod='billpaytransactioncredit'}</a>
        </span>
        <br>
        <span>
            <a href='https://www.billpay.de/api/ratenkauf/zahlungsbedingungen/' target='blank'>{l s='Payment Terms' mod='billpaytransactioncredit'}</a>
        </span>
    </div>

    <br style="clear: both;"/>

</div>