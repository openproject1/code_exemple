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

{block name=billpay_payment_warning_info_box_direct_debit}

<div style="background-color:#FFD9B3; text-align:center; border:2px solid #FF0000; padding:10px; font-weight:bold;">
        <p style="color: #FF0000;">
            {l s='Warning! Orders with this payment method WILL NOT BE SHIPPED!' mod='billpaytransactioncredit'}
        </p>
        <p>
            <a href="http://www.billpay.de/haendler/integration-plugin" target="_blank" title="{l s='Informationen for going live' mod='billpaytransactioncredit'}">{l s='Informationen for going live' mod='billpaytransactioncredit'}</a>
        </p>
    </div>

{/block}
