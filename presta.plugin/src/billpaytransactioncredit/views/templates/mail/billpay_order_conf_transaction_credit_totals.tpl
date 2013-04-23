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

<tr style="text-align: right;">
    <td>&nbsp;</td>
    <td colspan="3" style="background-color: #ebecee; padding: 0.6em 0.4em;">
        {capture name="billpay_surcharge_for_rates"}
            {l s='Surcharge for %s rates' mod='billpaytransactioncredit'}
        {/capture}
        {$smarty.capture.billpay_surcharge_for_rates|sprintf:$transactionCreditData['ratesNumber']}</strong>
        <br>
        ({displayPrice price=($transactionCreditData['calculation']['base']/100)} x {($transactionCreditData['calculation']['interest']/100)|number_format:2:",":"."} x {$transactionCreditData['ratesNumber']}) / 100
    </td>
    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['surcharge']/100)}</td>
</tr>
<tr style="text-align: right;">
    <td>&nbsp;</td>
	<td colspan="3" style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Processing fee' mod='billpaytransactioncredit'}</td>
	<td style="background-color: #ebecee; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['fee']/100)}</td>
</tr>
<tr style="text-align: right; font-weight: bold;">
    <td>&nbsp;</td>
	<td colspan="3" style="background-color: #f1aecf;; padding: 0.6em 0.4em;">{l s='Grand total transaction credit' mod='billpaytransactioncredit'}</td>
	<td style="background-color: #f1aecf; padding: 0.6em 0.4em;">{displayPrice price=($transactionCreditData['calculation']['total']/100)}</td>
</tr>