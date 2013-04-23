{**
 * Billpay
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer versions in the future.
 *
 * @category  Payment
 * @package   Billpay Prestashop module
 * @author    Billpay GmbH ( support@billpay.de )
 * @author    Catalin Vancea ( catalin.vancea@billpay.de )
 * @copyright Copyright 2012 Billpay GmbH
 * @license   Commercial
 * @link      https://www.billpay.de/
 *}

{capture name=path}{$displayName}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{$displayName}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div style="border:1px solid #0064b2; padding:10px;">

    <p>
         <img src="../views/templates/frontend/img/billpay_logo.png" alt="{$displayName}" title="{$displayName}"/>
    </p>

    <h3>{$displayName}</h3>

    {if $billpayConfiguration.BILLPAY_CUSTOMER_GROUP == $billpayCustomerGroups.both}
        <p>
            <input type="radio" name="billpayCustomerGroup" value="{$billpayCustomerGroups.b2c}" >{l s='Private customer' mod='billpay'}
            <br/><br/>
            <input type="radio" name="billpayCustomerGroup" value="{$billpayCustomerGroups.b2b}" >{l s='Business customer' mod='billpay'}
        </p>
    {elseif $billpayConfiguration.BILLPAY_CUSTOMER_GROUP == $billpayCustomerGroups.b2c}
        <p>
            {l s='Invoice payment:' mod='billpay'}
            {if $billpayConfiguration.BILLPAY_FEE_INVOICE_B2C}
                {l s='plus' mod='billpay'} {l s='invoice fee' mod='billpay'}
            {/if}
        </p>
    {elseif $billpayConfiguration.BILLPAY_CUSTOMER_GROUP == $billpayCustomerGroups.b2b}
        <p>
            {l s='Invoice payment:' mod='billpay'}
            {if $billpayConfiguration.BILLPAY_FEE_INVOICE_B2B}
                {l s='plus' mod='billpay'} {l s='invoice fee' mod='billpay'}
            {/if}
        </p>
    {/if}

</div>