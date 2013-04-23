function acceptBillpayTermsDirectDebit()
{
	if ($('#billpay_terms_checkbox').length && !$('input#billpay_terms_checkbox:checked').length)
	{
		alert(msg);
		return false;
	}
	else
		return true;
}