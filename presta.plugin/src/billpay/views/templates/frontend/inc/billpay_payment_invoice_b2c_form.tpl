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

<form action="{$this_path_ssl}controllers/validation.php" method="post" class="std" name="billpay_payment_invoice_b2c_form" onsubmit="return acceptBillpayTermsInvoice();">

    <fieldset>

        <p class="required">
            {l s='Required field' mod='billpay'} <sup>*</sup>
        </p>

        {if !$customerSalutation}
           <p class="radio required">
    			<span>{l s='Title:' mod='billpay'}</span>
    			<input type="radio" id="billpay_gender1" name="billpay_id_gender" value="{l s='Mr.' mod='billpay'}" />
    			<label for="billpay_gender1">{l s='Mr.' mod='billpay'}</label>
    			<input type="radio" id="billpay_gender2" name="billpay_id_gender" value="{l s='Mr.' mod='billpay'}" />
    			<label for="billpay_gender2">{l s='Ms.' mod='billpay'}</label>
    			<sup>*</sup>
    		</p>
        {/if}

        {if !$customerBirthday}
            <p class="select required text">
    				<label>{l s='Birthdate:' mod='billpay'}</label>
    				<select name="billpay_birthday_day" id="billpay_birthday_day">
    					<option value="">-</option>
    					{foreach from=$days item=v}
    						<option value="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
    					{/foreach}
    				</select>
    				{*
    					{l s='January' mod='billpay'}
    					{l s='February' mod='billpay'}
    					{l s='March' mod='billpay'}
    					{l s='April' mod='billpay'}
    					{l s='May' mod='billpay'}
    					{l s='June' mod='billpay'}
    					{l s='July' mod='billpay'}
    					{l s='August' mod='billpay'}
    					{l s='September' mod='billpay'}
    					{l s='October' mod='billpay'}
    					{l s='November' mod='billpay'}
    					{l s='December' mod='billpay'}
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
                 {l s='Data Protection Regulations' mod='billpay'}
            {/capture}
            {capture name="billpay_terms_checkbox"}
                {l s='I agree to the transmission of the data essential for processing the Invoice payment and for an identity and credit check to %s. I confirm the %s of Billpay.' mod='billpay'}
            {/capture}

            {$smarty.capture.billpay_terms_checkbox|sprintf:"<a href='https://www.billpay.de/endkunden' target='blank'>Billpay GmbH</a>":"<a href='{$datenschutzLink}' target='blank'>{$smarty.capture.billpay_translated_string_data_protection_regulations}</a>"}
            <sup>*</sup>
    	</p>

        <input type="hidden" name="cutomer_group" value="p"/>
        <input type="hidden" name="billpay_invoice" value="billpay_invoice"/>

    </fieldset>

    <p>
        - {l s='The total amount of your order is' mod='billpay'} <strong class="price">{displayPrice price=$totalWithBillpayFeeB2C}</strong> {l s='(tax incl.)' mod='billpay'}
    </p>

    <p class="cart_navigation">
        <a href="{$link->getPageLink('order.php', true)}?step=3" class="button_large">{l s='Other payment methods' mod='billpay'}</a>
        <input type="submit" name="submit" value="{l s='I confirm my order' mod='billpay'}" class="exclusive_large" />
    </p>

</form>
