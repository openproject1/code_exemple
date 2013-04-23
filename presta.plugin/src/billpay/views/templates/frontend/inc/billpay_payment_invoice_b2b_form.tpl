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

<form action="{$this_path_ssl}controllers/validation.php" method="post" class="std" name="billpay_payment_invoice_b2b_form" onsubmit="return acceptBillpayTermsInvoice();">

    <fieldset>

        <p class="required">
            {l s='Required field' mod='billpay'}<sup>*</sup>
        </p>

        <p class="required text">
			<label for="billpay_company_name">{l s='Company Name:' mod='billpay'}</label>
			<input type="text" name="billpay_company_name" id="billpay_company_name" value="{$invoiceAddressCompanyName}" />
			<sup>*</sup>
		</p>

		<p class="required select">
		    <label for="billpay_legal_status">{l s='Legal Status:' mod='billpay'}</label>
            {** Sort the array with js this will be helpful when the select values are translated *}
			<select style="width: 152px;" name="billpay_legal_status" id="billpay_legal_status">
	  			<option value="" selected="selected">-</option>
  				<option value="ag">{l s='AG (Aktiengesellschaft)' mod='billpay'}</option>
  				<option value="eg">{l s='eG (eingetragene Genossenschaft)' mod='billpay'}</option>
  				{if $invoiceAddressCountryISO == 'DEU'  || $invoiceAddressCountryISO == 'AUT'}
  				    <option value="ek">{l s='EK (eingetragener Kaufmann)' mod='billpay'}</option>
  				    <option value="gbr">{l s='GbR/BGB (Gesellschaft bürgerlichen Rechts)' mod='billpay'}</option>
  				    <option value="gmbh_ig">{l s='GmbH in Gründung' mod='billpay'}</option>
  				    <option value="gmbh_co_kg">{l s='GmbH & Co. KG' mod='billpay'}</option>
  				    <option value="ltd_co_kg">{l s='Limited & Co. KG' mod='billpay'}</option>
  				    <option value="ohg">{l s='OHG (offene Handelsgesellschaft)' mod='billpay'}</option>
  				    <option value="ug">{l s='UG (Unternehmensgesellschaft haftungsbeschränkt)' mod='billpay'}</option>
  				{/if}
  				{if $invoiceAddressCountryISO == 'CHE'}
  				    <option value="einzel">{l s='Einzelfirma' mod='billpay'}</option>
  				    <option value="e_ges">{l s='Einfache Gesellschaft' mod='billpay'}</option>
  				    <option value="inv_kk">{l s='Investmentgesellschaft für kollektive Kapitalanlagen' mod='billpay'}</option>
  				    <option value="k_ges">{l s='Kollektivgesellschaft' mod='billpay'}</option>
  				{/if}
  				<option value="ev">{l s='e.V. (eingetragener Verein)' mod='billpay'}</option>
				<option value="freelancer">{l s='Freiberufler/Kleingewerbetreibender/Handelsvertreter' mod='billpay'}</option>
  				<option value="gmbh">{l s='GmbH (Gesellschaft mit beschränkter Haftung)' mod='billpay'}</option>
  				<option value="kg">{l s='KG (Kommanditgesellschaft)' mod='billpay'}</option>
  				<option value="kgaa">{l s='Kommanditgesellschaft auf Aktien' mod='billpay'}</option>
                <option value="ltd">{l s='Limited' mod='billpay'}</option>
  				<option value="public_inst">{l s='Öffentliche Einrichtung' mod='billpay'}</option>
  				<option value="misc_captial">{l s='Sonstige Kapitalgesellschaft' mod='billpay'}</option>
  				<option value="misc">{l s='Sonstiges Personengesellschaft' mod='billpay'}</option>
  				<option value="foundation" >{l s='Stiftung' mod='billpay'}</option>
			</select>
			<sup>*</sup>
	    </p>

		<p class="required text">
			<label for="billpay_registration_number">{l s='Registration number:' mod='billpay'}</label>
			<input type="text" name="billpay_registration_number" id="billpay_registration_number" value="" />
			<sup>*</sup>
		</p>

		<p class="required text">
			<label for="billpay_tax_id">{l s='Tax ID:' mod='billpay'}</label>
			<input type="text" name="billpay_tax_id" id="billpay_tax_id" value="{$invoiceAddressCompanyDni}" />
		</p>

		<p class="required text">
			<label for="billpay_holder_name">{l s='Holder Name:' mod='billpay'}</label>
			<input type="text" name="billpay_holder_name" id="billpay_holder_name" value="{$currentCustomerName}" />
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

    	<input type="hidden" name="cutomer_group" value="b"/>
    	<input type="hidden" name="billpay_invoice" value="billpay_invoice"/>

    </fieldset>

    <p>
        - {l s='The total amount of your order is' mod='billpay'} <strong class="price">{displayPrice price=$totalWithBillpayFeeB2B}</strong> {l s='(tax incl.)' mod='billpay'}
    </p>

    <p class="cart_navigation">
        <a href="{$link->getPageLink('order.php', true)}?step=3" class="button_large">{l s='Other payment methods' mod='billpay'}</a>
        <input type="submit" name="submit" value="{l s='I confirm my order' mod='billpay'}" class="exclusive_large" />
    </p>
</form>