
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

<script type="text/javascript" src="../modules/billpay/views/templates/backend/js/backend.js"></script>

<div id="billpay_configuration_wrapper">

    <p>
        <img src="../modules/billpay/views/templates/backend/img/billpay_logo.png" alt="Billpay Logo"/>
    </p>

    <h2>{l s='Billpay general settings' mod='billpay'}</h2>

    <hr>

    <p>
        <b>{l s='Welcome to the configuration of your Billpay modules' mod='billpay'}</b>
    </p>

    <p>
        {l s='Billpay version' mod='billpay'}: {$version}
    </p>

    <hr>

    {$billpayPostErrorsNr = sizeof($billpayPostErrors)}
    {if ($billpayPostErrorsNr)}
        <div class="error">
        	<h3>
        	    {if $billpayPostErrorsNr == 1} {l s='Please check the following error:' mod='billpay'} {/if}
        	    {if $billpayPostErrorsNr >  1} {l s='Please check the following errors:' mod='billpay'} {/if}
    	    </h3>
    		<ol>
            {foreach from=$billpayPostErrors item=billpayPostError}
                <li>{$billpayPostError}</li>
            {/foreach}
           </ol>
    	</div>
    {else}
        {if ($settingsUpdate)}
            <div class="conf confirm">
    			<img src="../img/admin/ok.gif" alt="{l s='Settings updated'}" />
    			{l s='Settings updated'}
    		</div>
		{/if}
    {/if}

    <form action="{$smarty.server.REQUEST_URI|escape:'htmlall'}" method="post">
        <fieldset style="margin-bottom: 15px">
        <legend><img src="../img/admin/contact.gif" />{l s='Billpay settings' mod='billpay'}</legend>

            <p>
                <b>{l s='Please enter your Billpay credentials in the appropriate fields and click \'Save settings\'' mod='billpay'}</b>
            </p>
            <hr>
            <p id="billpay_merchant_id">
                <label>{l s='Merchant ID' mod='billpay'}</label>
                <input type="text" name="BILLPAY_MERCHANT_ID" value="{$billpayConfiguration.BILLPAY_MERCHANT_ID}" maxlength="5" size="40"/>
                <sup>*</sup>
            </p>
            <p id="billpay_portal_id">
                <label>{l s='Portal ID' mod='billpay'}</label>
                <input type="text" name="BILLPAY_PORTAL_ID" value="{$billpayConfiguration.BILLPAY_PORTAL_ID}" maxlength="5" size="40"/>
                <sup>*</sup>
            </p>
            <p id="billpay_api_password">
                <label>{l s='Security Key' mod='billpay'}</label>
                <input type="text" name="BILLPAY_API_PASSWORD" value="{$billpayConfiguration.BILLPAY_API_PASSWORD}" maxlength="40" size="40"/>
                <sup>*</sup>
            </p>
            <p id="billpay_api_test_url">
                <label>{l s='API URL base test mode' mod='billpay'}</label>
                <input type="text" name="BILLPAY_API_TEST_URL" value="{$billpayConfiguration.BILLPAY_API_TEST_URL}" maxlength="40" size="40"/>
                <sup>*</sup>
            </p>
            <p id="billpay_api_live_url">
                <label>{l s='API URL base live mode' mod='billpay'}</label>
                <input type="text" name="BILLPAY_API_LIVE_URL" value="{$billpayConfiguration.BILLPAY_API_LIVE_URL}" maxlength="40" size="40"/>
                <sup>*</sup>
            </p>

            <hr>
            <p id="billpay_transaction_mode">
                <label>{l s='Transaction mode' mod='billpay'}</label>
                <select style="width: 100px;" name="BILLPAY_TRANSACTION_MODE">
                        {foreach from=$billpayTransactionModes item=mode}
        				    <option value="{$mode}" style="padding: 1px 5px;" {if $billpayConfiguration.BILLPAY_TRANSACTION_MODE == $mode} selected="selected"{/if}>
        					    {$mode|capitalize}
        				    </option>
        				{/foreach}
        		</select>
        		<sup>*</sup>
            </p>

            {**
             * Prescore is not active for now in presta module
            <hr>

            <p id="billpay_prescore_mode_check">
                <label>{l s='Enable \'Prescore\' mode' mod='billpay'}</label>
                <input id="billpay_prescore_mode_check" width="240" type="checkbox" name="billpay_prescore_mode_check" $billpayPrescoreModeCheck">
                <div class="margin-form">
                <em>{l s='\'Prescore\' mode needs to be enabled also in the Billpay back office.' mod='billpay'}</em>
                <sup>*</sup>>
            </p>
            **}

            <hr>

            <p id="billpay_customer_group">
                <label>{l s='Allowed groups of customers' mod='billpay'}</label>
                <select style="width: 100px;" name="BILLPAY_CUSTOMER_GROUP">
                        {foreach from=$billpayCustomerGroups item=group}
                            <option value="{$group}" style="padding: 1px 5px;" {if $billpayConfiguration.BILLPAY_CUSTOMER_GROUP == $group} selected="selected"{/if}>
        					    {$group|upper}
        				    </option>
        				{/foreach}
        		</select>
        		<sup>*</sup>
            </p>

            <!-- TODO: finish implementation of extra payment charges

    		<hr>

    		<p id="billpay_invoice_fee_b2c">
                <label>{l s='Extra invoice fee B2C' mod='billpay'}</label>
                <input type="text" name="BILLPAY_FEE_INVOICE_B2C" value="{$billpayConfiguration.BILLPAY_FEE_INVOICE_B2C}" maxlength="40" size="40"/>
                <a id="billpay_invoice_fee_b2c" href="#billpay_invoice_fee_b2c_hint">
                    <img class="clear" style="padding-bottom: 3px;" src="../img/admin/unknown.gif" alt="" title="" />
                </a>
            </p>
            <p id="billpay_invoice_fee_b2c_hint" class="margin-form" style="display: none;">
                <em class="hint" style="display: block;">{l s='Enter a payment fee for the specific currency. Example (fixed):\'EUR:5;CHF:2\' percent:\'EUR:5%;CHF:2%;\'' mod='billpay'} </em>
            </p>

            <p id="billpay_invoice_fee_b2b">
                <label>{l s='Extra invoice fee B2B' mod='billpay'}</label>
                <input type="text" name="BILLPAY_FEE_INVOICE_B2B" value="{$billpayConfiguration.BILLPAY_FEE_INVOICE_B2B}" maxlength="40" size="40"/>
                <a id="billpay_invoice_fee_b2b" href="#billpay_invoice_fee_b2b_hint">
                    <img style="padding-bottom: 3px;" src="../img/admin/unknown.gif" alt="" title="" />
                </a>
            </p>
            <p id="billpay_invoice_fee_b2b_hint" class="margin-form" style="display: none;">
                <em class="hint" style="display: block;">{l s='Enter a payment fee for the specific currency. Example (fixed):\'EUR:5;CHF:2\' percent:\'EUR:5%;CHF:2%;\'' mod='billpay'} </em>
            </p>

            <p id="billpay_fee_dd">
                <label>{l s='Extra Direct Debit payment fee' mod='billpay'}</label>
                <input type="text" name="BILLPAY_FEE_DD" value="{$billpayConfiguration.BILLPAY_FEE_DD}" maxlength="40" size="40"/>
                <a id="billpay_invoice_fee_dd" href="#billpay_invoice_fee_dd_hint">
                    <img style="padding-bottom: 3px;" src="../img/admin/unknown.gif" alt="" title="" />
                </a>
            </p>
            <p id="billpay_invoice_fee_dd_hint" class="margin-form" style="display: none;">
                <em class="hint" style="display: block;">{l s='Enter a payment fee for the specific currency. Example (fixed):\'EUR:5;CHF:2\' percent:\'EUR:5%;CHF:2%;\'' mod='billpay'} </em>
            </p>

            -->
            <!-- end TODO: finish implementation of extra payment charges -->

            <input type="hidden" name="BILLPAY_FEE_INVOICE_B2C" value="0" />
            <input type="hidden" name="BILLPAY_FEE_INVOICE_B2B" value="0" />
            <input type="hidden" name="BILLPAY_FEE_DD" value="0" />

            <hr>

            <div class="margin-form">
                <input type="submit" name="submitBillpaySettings" value="{l s='Save settings' mod='billpay'}" class="button" />
            </div>

        </fieldset>
    </form>
</div>