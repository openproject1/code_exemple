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
        {l s='Billpay Information' mod='billpaydirectdebit'}
    </legend>

    <p>
        <strong>{$displayName}</strong>
    </p>

    <p>
        {if !$billpayActivationPerformed}
            {l s='The due date for this order needs to be activated with Billpay.' mod='billpaydirectdebit'}<sup style="color: #FF0000;">*</sup>
        {/if}
    </p>

    <hr>

    <div style="font-style: italic;">
        <p>
            {l s='Within the next few days, we will withdraw the amount due from the bank account supplied when placing the order.' mod='billpaydirectdebit'}
        </p>
    </div>
</fieldset>