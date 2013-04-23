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

    <p>
         <img src="../views/templates/frontend/img/billpay_logo.png" alt="{$displayName}" title="{$displayName}"/>
    </p>

    <h3>{$displayName}</h3>

    {if $BILLPAY_TRANSACTION_MODE == $billpayTransactionModesTest}
        <p>
            {include file="$_PS_MODULE_DIR_/billpaydirectdebit/views/templates/frontend/inc/billpay_payment_warning_info_block.tpl"}
        </p>
    {/if}

    <script type="text/javascript">
	//<![CDATA[
		var msg = "{l s='You must agree to the billpay direct debit payment terms of service before continuing.' mod='billpaydirectdebit' js=1}";
	//]]>
	</script>
    <script type="text/javascript" src="/modules/billpaydirectdebit/views/templates/frontend/js/frontend.js"></script>

    <p>
        <strong>
        {l s='Direct debit payment:' mod='billpaydirectdebit'}
        {if $extraBillpayFeeDD}
            {capture name="billpay_extra_fee"}
                    {l s='plus %s payment fee' mod='billpaydirectdebit'}
            {/capture}
            {$smarty.capture.billpay_extra_fee|sprintf:$extraBillpayFeeDD}:
        {/if}
        </strong>
    </p>

    {if isset($smarty.session.billpaySubmitErrors)}
        <div id="billpay_submit_errors" name="billpay_submit_errors" style="color: #DA0F00; margin: 0.5em 0; padding-left: 0.7em;">
            {l s='Please fill the required fields:' mod='billpaydirectdebit'}
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

    <form action="validation.php" method="post" class="std" name="billpay_payment_direct_debit_form" onsubmit="return acceptBillpayTermsDirectDebit();">
        <fieldset>
            <p class="required">
                {l s='Required field' mod='billpaydirectdebit'} <sup>*</sup>
            </p>

            <p class="required">
                {l s='Please enter your bank account data:' mod='billpaydirectdebit'}
            </p>

            <p class="required text">
    			<label for="billpay_account_holder">{l s='Account Holder:' mod='billpaydirectdebit'}</label>
    			<input type="text" size="20" maxlength="64" name="billpay_account_holder" id="billpay_account_holder" value="{$currentCustomerName}" />
    			<sup>*</sup>
    		</p>

    		<p class="required text">
    			<label for="billpay_account_number">{l s='Account Number:' mod='billpaydirectdebit'}</label>
    			<input type="text"  autocomplete="off"  size="20" maxlength="64" name="billpay_account_number" id="billpay_account_number" value="" />
    			<sup>*</sup>
    		</p>

    		<p class="required text">
    			<label for="billpay_bank_code">{l s='Bank code:' mod='billpaydirectdebit'}</label>
    			<input type="text"  autocomplete="off"  size="20" maxlength="64" name="billpay_bank_code" id="billpay_bank_code" value="" />
    			<sup>*</sup>
    		</p>

            {if !$customerSalutation || !$customerBirthday}
                <p class="required">
                {l s='Please enter your personal data:' mod='billpaydirectdebit'}
            </p>
            {/if}

            {if !$customerSalutation}
                <p class="radio required">
                	<span>{l s='Title:' mod='billpaydirectdebit'}</span>
                	<input type="radio" id="billpay_gender1" name="billpay_id_gender" value="{l s='Mr.' mod='billpaydirectdebit'}" />
                	<label for="billpay_gender1">{l s='Mr.' mod='billpaydirectdebit'}</label>
                	<input type="radio" id="billpay_gender2" name="billpay_id_gender" value="{l s='Mr.' mod='billpaydirectdebit'}" />
                	<label for="billpay_gender2">{l s='Ms.' mod='billpaydirectdebit'}</label>
                	<sup>*</sup>
                </p>
            {/if}

    		{if !$customerBirthday}
                <p class="select required text">
        				<label>{l s='Birthdate:' mod='billpaydirectdebit'}</label>
        				<select name="billpay_birthday_day" id="billpay_birthday_day">
        					<option value="">-</option>
        					{foreach from=$days item=v}
        						<option value="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
        					{/foreach}
        				</select>
        				{*
        					{l s='January' mod='billpaydirectdebit'}
        					{l s='February' mod='billpaydirectdebit'}
        					{l s='March' mod='billpaydirectdebit'}
        					{l s='April' mod='billpaydirectdebit'}
        					{l s='May' mod='billpaydirectdebit'}
        					{l s='June' mod='billpaydirectdebit'}
        					{l s='July' mod='billpaydirectdebit'}
        					{l s='August' mod='billpaydirectdebit'}
        					{l s='September' mod='billpaydirectdebit'}
        					{l s='October' mod='billpaydirectdebit'}
        					{l s='November' mod='billpaydirectdebit'}
        					{l s='December' mod='billpaydirectdebit'}
        				*}
        				<select id="billpay_birthday_month" name="billpay_birthday_month">
        					<option value="">-</option>
        					{foreach from=$months key=k item=v}
        						<option value="{$k|escape:'htmlall':'UTF-8'}">{l s="$v" mod='billpay'}</option>
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
                {capture name="billpay_translated_string_data_protection_regulations"}
                     {l s='Data Protection Regulations' mod='billpaydirectdebit'}
                {/capture}
                {capture name="billpay_terms_checkbox"}
                    {l s='I agree to the transmission of the data essential for processing the Direct Debit payment and for an identity and credit check to %s. I confirm the %s of Billpay.' mod='billpaydirectdebit'}
                {/capture}

                {$smarty.capture.billpay_terms_checkbox|sprintf:"<a href='https://www.billpay.de/endkunden' target='blank'>Billpay GmbH</a>":"<a href='{$datenschutzLink}' target='blank'>{$smarty.capture.billpay_translated_string_data_protection_regulations}</a>"}
                <sup>*</sup>
        	</p>

            <input type="hidden" name="direct_debit" value="direct_debit"/>

        </fieldset>

        <p>
            - {l s='The total amount of your order is' mod='billpaydirectdebit'} <strong class="price">{displayPrice price=$totalWithBillpayFee}</strong> {l s='(tax incl.)' mod='billpaydirectdebit'}
        </p>

        <p class="cart_navigation">
            <a href="{$link->getPageLink('order.php', true)}?step=3" class="button_large">{l s='Other payment methods' mod='billpaydirectdebit'}</a>
            <input type="submit" name="submit" value="{l s='I confirm my order' mod='billpaydirectdebit'}" class="exclusive_large" />
        </p>

    </form>

    {include file="$_PS_MODULE_DIR_/billpay/views/templates/frontend/inc/billpay_profiling_tags.tpl"}

</div>