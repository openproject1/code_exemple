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


{capture name=path}{$displayName}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{$displayName}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div style="border:1px solid #0064b2; padding:10px;">

    {if $BILLPAY_TRANSACTION_MODE == $billpayTransactionModesTest}
        <p>
            {include file="$_PS_MODULE_DIR_/billpaytransactioncredit/views/templates/frontend/inc/billpay_payment_warning_info_block.tpl"}
        </p>
    {/if}

    <p>
        <strong>
            {l s='Transaction Credit payment:' mod='billpaytransactioncredit'}
        </strong>
    </p>

    <script type="text/javascript">
	//<![CDATA[
		var msg = "{l s='You must agree to the transaction credit terms of service before continuing.' mod='billpaytransactioncredit' js=1}";
	//]]>
	</script>
    <script type="text/javascript" src="/modules/billpaytransactioncredit/views/templates/frontend/js/frontend.js"></script>

    {if isset($smarty.session.billpaySubmitErrors)}
        <div id="billpay_submit_errors" name="billpay_submit_errors" style="color: #DA0F00; margin: 0.5em 0; padding-left: 0.7em;">
            {l s='Please fill the required fields:' mod='billpaytransactioncredit'}
            <sup>*</sup>
            <ol>
            {foreach from=$smarty.session.billpaySubmitErrors item=billpaySubmitError}
                <li style="font-weight: bold; margin: 0.2em 0 0 2.2em;">{$billpaySubmitError}</li>
            {/foreach}
            </ol>
        </div>
    {/if}

    {if isset($smarty.session.billpayRequestError)}
        <div id="billpay_submit_errors" name="billpay_submit_errors" style="color: #DA0F00; margin: 0.5em 0; padding-left: 0.7em;">
            <strong>{$smarty.session.billpayRequestError}</strong>
        </div>
    {/if}

    <form action="validation.php" method="post" class="std" name="billpay_payment_transaction_credit_form" onsubmit="return acceptBillpayTermsTransactionCredit();">
        <fieldset>

            <p class="required">
                {l s='Required fields' mod='billpaytransactioncredit'}
                <sup>*</sup>
            </p>

            <div class="required" style="border-bottom:1px solid #BDC2C9; padding: 0px 10px 10px 10px;">
                {l s='Please select the desired number of rates:' mod='billpaytransactioncredit'}
            	<select name="billpay_transaction_credit_rates" id="billpay_transaction_credit_rates" onchange="getBillpayTransactionCreditRatePlan()">
                    {foreach from=$transactionCreditTerms item=k}
                        <option value="{$k}" {if $k == 12}SELECTED{/if}>{$k}</option>
                    {/foreach}
                </select>
                <sup>*</sup>
            </div>

            <div id="billpay_payment_transactioncredit_rate_plan" style="display: none;">
                &nbsp;
            </div>

            <p class="required">
                {l s='Please enter your bank account data:' mod='billpaytransactioncredit'}
            </p>

            <p class="required text">
    			<label for="billpay_account_holder">{l s='Account Holder:' mod='billpaytransactioncredit'}</label>
    			<input type="text" size="20" maxlength="64" name="billpay_account_holder" id="billpay_account_holder" value="{$customerName}" />
    			<sup>*</sup>
    		</p>

    		<p class="required text">
    			<label for="billpay_account_number">{l s='Account Number:' mod='billpaytransactioncredit'}</label>
    			<input type="text"  autocomplete="off"  size="20" maxlength="64" name="billpay_account_number" id="billpay_account_number" value="" />
    			<sup>*</sup>
    		</p>

    		<p class="required text">
    			<label for="billpay_bank_code">{l s='Bank code:' mod='billpaytransactioncredit'}</label>
    			<input type="text"  autocomplete="off"  size="20" maxlength="64" name="billpay_bank_code" id="billpay_bank_code" value="" />
    			<sup>*</sup>
    		</p>

            {if !$customerSalutation || !$customerBirthday}
                <p class="required">
                {l s='Please enter your personal data:' mod='billpaytransactioncredit'}
            </p>
            {/if}

            {if !$customerSalutation}
                <p class="radio required">
                	<span>{l s='Title:' mod='billpaytransactioncredit'}</span>
                	<input type="radio" id="billpay_gender1" name="billpay_id_gender" value="{l s='Mr.' mod='billpaytransactioncredit'}" />
                	<label for="billpay_gender1">{l s='Mr.' mod='billpaytransactioncredit'}</label>
                	<input type="radio" id="billpay_gender2" name="billpay_id_gender" value="{l s='Mr.' mod='billpaytransactioncredit'}" />
                	<label for="billpay_gender2">{l s='Ms.' mod='billpaytransactioncredit'}</label>
                	<sup>*</sup>
                </p>
            {/if}

    		{if !$customerBirthday}
                <p class="select required text">
        				<label>{l s='Birthdate:' mod='billpaytransactioncredit'}</label>
        				<select name="billpay_birthday_day" id="billpay_birthday_day">
        					<option value="">-</option>
        					{foreach from=$days item=v}
        						<option value="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
        					{/foreach}
        				</select>
        				{*
        					{l s='January' mod='billpaytransactioncredit'}
        					{l s='February' mod='billpaytransactioncredit'}
        					{l s='March' mod='billpaytransactioncredit'}
        					{l s='April' mod='billpaytransactioncredit'}
        					{l s='May' mod='billpaytransactioncredit'}
        					{l s='June' mod='billpaytransactioncredit'}
        					{l s='July' mod='billpaytransactioncredit'}
        					{l s='August' mod='billpaytransactioncredit'}
        					{l s='September' mod='billpaytransactioncredit'}
        					{l s='October' mod='billpaytransactioncredit'}
        					{l s='November' mod='billpaytransactioncredit'}
        					{l s='December' mod='billpaytransactioncredit'}
        				*}
        				<select id="billpay_birthday_month" name="billpay_birthday_month">
        					<option value="">-</option>
        					{foreach from=$months key=k item=v}
        						<option value="{$k|escape:'htmlall':'UTF-8'}">{l s="$v" mod='billpaytransactioncredit'}</option>
        					{/foreach}
        				</select>
        				<select id="billpay_birthday_year" name="billpay_birthday_year">
        					<option value="">-</option>
        					{foreach from=$years item=v}
        						<option value="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
        					{/foreach}
        				</select>
        				<sup>*</sup>
        		</p>
            {/if}

            <p class="input required">
                <input id='billpay_terms_checkbox' type='checkbox' name='billpay_terms_checkbox' />
                {capture name="billpay_translated_string_terms_and_conditions"}
                     {l s='terms and conditions' mod='billpaytransactioncredit'}
                {/capture}
                {capture name="billpay_translated_string_data_protection_regulations"}
                     {l s='data protection regulations' mod='billpaytransactioncredit'}
                {/capture}
                {capture name="billpay_terms_checkbox"}
                    {l s='I agree to the %s and the %s for transaction credit.' mod='billpaytransactioncredit'}
                {/capture}

                {$smarty.capture.billpay_terms_checkbox|sprintf:"<a href='{$agb}' target='blank'>{$smarty.capture.billpay_translated_string_terms_and_conditions}</a>":"<a href='https://www.billpay.de/api/ratenkauf/datenschutz' target='blank'>{$smarty.capture.billpay_translated_string_data_protection_regulations}</a>"}
                <sup>*</sup>
        	</p>

            <input type="hidden" name="transaction_credit" value="transaction_credit"/>

        </fieldset>

        <p class="cart_navigation">
            <a href="{$link->getPageLink('order.php', true)}?step=3" class="button_large">{l s='Other payment methods' mod='billpaytransactioncredit'}</a>
            <input type="submit" name="submit" value="{l s='I confirm my order' mod='billpaytransactioncredit'}" class="exclusive_large" />
        </p>

    </form>

    {include file="$_PS_MODULE_DIR_/billpay/views/templates/frontend/inc/billpay_profiling_tags.tpl"}

</div>