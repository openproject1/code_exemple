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
    <td style="background-color: #3385c1; color: #fff; font-size: 12px; font-weight: bold; padding: 0.5em 1em;" align="left">{$displayName}</td>
</tr>

<tr>
    <td>&nbsp;</td>
</tr>

<tr>
    <td align="left">
        <strong>{l s='Please transfer the total amount with transaction number (received within the invoice) as reason for payment until (date received within the invoice) to the following account:' mod='billpay'}</strong>
    </td>
</tr>

<tr>
    <td align="left">
        <table style="width: 100%; font-family: Verdana,sans-serif; font-size: 11px; color: #374953;">
            <tbody>
                <tr>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Account holder:' mod='billpay'}</td>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{$billpayBankData["account_holder"]}</td>
                </tr>

                <tr>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Bank name:' mod='billpay'}</td>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{$billpayBankData["bank_name"]}</td>
                </tr>

                <tr>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Bank code:' mod='billpay'}</td>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{$billpayBankData["bank_code"]}</td>
                </tr>
                <tr>

                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Account number:' mod='billpay'}</td>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{$billpayBankData["account_number"]}</td>
                </tr>
                <tr>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Due date:' mod='billpay'}</td>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">({l s='received within the invoice' mod='billpay'})</td>
                </tr>

                <tr>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">{l s='Reason for payment:' mod='billpay'}</td>
                    <td style="background-color: #ebecee; padding: 0.6em 0.4em;">({l s='received within the invoice' mod='billpay'})</td>
                </tr>
            </tbody>
        </table>
   </td>
</tr>

<tr>
    <td>&nbsp;</td>
</tr>