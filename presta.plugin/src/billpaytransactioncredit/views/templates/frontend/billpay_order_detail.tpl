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
        table#billpay-left tfoot td,
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
                <th colspan="5" class="first_item">{l s='Transaction Credit' mod='billpaytransactioncredit'}</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="5" style="padding: 0.1em 0.5em;"><sup style="color: #FF0000;">*</sup>{l s='The amounts due will be withdrawn from the bank account' mod='billpaytransactioncredit'}</td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 0.1em 0.5em;">{l s='supplied when placing the order in monthly periods.' mod='billpaytransactioncredit'}</td>
            </tr>
            <tr class="item">
                <td colspan="5" style="">
                   <p class="strong">
                       <strong>{l s='Total price calculation:' mod='billpaytransactioncredit'}</strong>
                    </p>
                </td>
            </tr>
            <tr class="item">
                <td colspan="3">{l s='Cart total' mod='billpaytransactioncredit'}</td>
                <td colspan="1">&nbsp;&nbsp;=&nbsp;</td>
                <td colspan="1" style="text-align: right;"><span class="price">{displayPrice price=($transactionCreditData['calculation']['base']/100)}</span></td>
            </tr>
            <tr class="item">
                <td colspan="3">{l s='Surcharge' mod='billpaytransactioncredit'}</td>
                <td colspan="1">&nbsp;&nbsp;+&nbsp;</td>
                <td colspan="1" style="text-align: right;">&nbsp;</td>
            </tr>
            <tr class="item">
                <td colspan="3">({displayPrice price=($transactionCreditData['calculation']['base']/100)} x {($transactionCreditData['calculation']['interest']/100)|number_format:2:",":"."} x {$transactionCreditData['ratesNumber']}) / 100</td>
                <td colspan="1">&nbsp;&nbsp;=&nbsp;</td>
                <td colspan="1" style="text-align: right;"><span class="price">{displayPrice price=($transactionCreditData['calculation']['surcharge']/100)}</span></td>
            </tr>
            <tr class="item">
                <td colspan="3">{l s='Processing fee' mod='billpaytransactioncredit'}</td>
                <td colspan="1">&nbsp;&nbsp;+&nbsp;</td>
                <td colspan="1" style="text-align: right;"><span class="price">{displayPrice price=($transactionCreditData['calculation']['fee']/100)}</span></td>
            </tr>
            <tr class="item">
                <td colspan="3">{l s='Additional fees (e.g. shipping costs)' mod='billpaytransactioncredit'}</td>
                <td colspan="1">&nbsp;&nbsp;+&nbsp;</td>
                <td colspan="1" style="text-align: right;"><span class="price">{displayPrice price=(($transactionCreditData['calculation']['cart'] - $transactionCreditData['calculation']['base'])/100)}</span></td>
            </tr>
            <tr class="item">
                <td colspan="5"><hr></td>
    	    </tr>
    	    <tr class="item">
                <td colspan="3" class="bold">{l s='Grand total transaction credit' mod='billpaytransactioncredit'}</td>
                <td colspan="1" class="bold">&nbsp;&nbsp;=&nbsp;</td>
                <td colspan="1" class="bold" style="text-align: right;"><span class="price">{displayPrice price=($transactionCreditData['calculation']['total']/100)}</span></td>
            </tr>
            <tr class="item">
                <td colspan="3" class="bold">{l s='effective APR' mod='billpaytransactioncredit'}</td>
                <td colspan="1" class="bold">&nbsp;&nbsp;=&nbsp;</td>
                <td colspan="1" class="bold" style="text-align: right;"><span class="price">{($transactionCreditData['calculation']['anual']/100)|number_format:2:",":"."} %</td>
            </tr>
            <tr class="item">
                <td colspan="5"><hr></td>
    	    </tr>
            <tr class="item">
                <td colspan="5" style="padding: 0.1em 0.5em;">
                    <sup style="color: #FF0000;">*</sup>{l s='First rate due by direct debit one month after shipping date, following rates due by direct debit monthly' mod='billpaytransactioncredit'}
                </td>
            </tr>
            <tr class="item">
                <td colspan="5" style="padding: 0.1em 0.5em;">
                    {l s='on corresponding calendar day of subsequent month or following day, respectively' mod='billpaytransactioncredit'}
                </td>
             </tr>
             <tr class="item">
                <td colspan="5" style="padding: 0.1em 0.5em;">
                    {l s='(Example: Shipping date 2012/9/19, 1st Rate 2012/10/19, following rates on 19th of each subsequent month).' mod='billpaytransactioncredit'}
                </td>
            </tr>
        </tfoot>

        <tbody>
            {foreach from=$transactionCreditData['dues'] key=k item=v}
                <tr style="height: 1em;" class="item">
                    <td width="2%" style="text-align: right; width: 2%">{$k+1}.</td>
                    <td width="5%" style="text-align: right; width: 5%">{l s='Rate:' mod='billpaytransactioncredit'}</td>
                    <td width="8%" style="text-align: right; width: 8%">{displayPrice price=($v["value"]/100)}</td>

                    <td colspan="2" width="85%" style="text-align: left; width: 85%">
                        {if $v["date"]}({l s='due on:' mod='billpaytransactioncredit'} {$v["date"]}){/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>