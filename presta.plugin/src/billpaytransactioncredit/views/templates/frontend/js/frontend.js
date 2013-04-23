$(document).ready(function(){
	
	// run on page load with default 12 rates
	getBillpayTransactionCreditRatePlan();
});

function getBillpayTransactionCreditRatePlan() {
	var _monthsRatePlan = $('select[name="billpay_transaction_credit_rates"]').val()
	var _billpayPaymentTransactioncreditRatePlan = document.getElementById('billpay_payment_transactioncredit_rate_plan');
	
	$("#billpay_payment_transactioncredit_rate_plan").hide('fast');
	
	$.ajax({
		type: 'POST',
		url:'ajax_billpay_transactioncredit_rate_plan.php',
		data: "monthsRatePlan=" + _monthsRatePlan,
		success: function(data) {
			if(data != 'flase'){
				_billpayPaymentTransactioncreditRatePlan.innerHTML = data;
            } else {
            	_billpayPaymentTransactioncreditRatePlan.innerHTML = '';
            }
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#billpay_payment_transactioncredit_rate_plan").html = 'ajax error';
		}
	});
	
	$("#billpay_payment_transactioncredit_rate_plan").show('fast')
}

function acceptBillpayTermsTransactionCredit()
{
	if ($('#billpay_terms_checkbox').length && !$('input#billpay_terms_checkbox:checked').length)
	{
		alert(msg);
		return false;
	}
	else
		return true;
}