$(document).ready(function(){
	
	/*
	 * b2c and b2b form logic 
	 */
	// start hide
	$("#billpay_payment_invoice_b2b_tpl").hide();
	$("#billpay_payment_invoice_b2b_tpl").hide();
	
	// set b2c as start checked 
	var billpay_customer_group = $('input:radio[name=billpay_customer_group]');
    if(billpay_customer_group.is(':checked') === false) {
    	billpay_customer_group.filter('[value=billpay_customer_group_b2c]').attr('checked', true);
    }
	
	// show/hide b2b and b2c base on default checked
	var billpay_customer_group_checked = $('input[name=billpay_customer_group]:checked').val();
	
	if (billpay_customer_group_checked == "billpay_customer_group_b2c") {
    	$("#billpay_payment_invoice_b2b_tpl").hide();
    	$("#billpay_payment_invoice_b2c_tpl").show("fast");
 	} 
 	else if (billpay_customer_group_checked == "billpay_customer_group_b2b") {
 		$("#billpay_payment_invoice_b2c_tpl").hide();
 		$("#billpay_payment_invoice_b2b_tpl").show("fast");
 	}
	
    // show/hide b2b and b2c base on click
	$("[name=billpay_customer_group]").click(function(){
	    var billpay_customer_group_clicked = $(this).val(); 
	    
	    $("#billpay_submit_errors").remove();
	    $("#billpay_request_error").remove();
	    
	    if (billpay_customer_group_clicked == "billpay_customer_group_b2c") {
	    	$("#billpay_payment_invoice_b2b_tpl").hide();
	    	$("#billpay_payment_invoice_b2c_tpl").show("fast");
	 	} 
	 	else if (billpay_customer_group_clicked == "billpay_customer_group_b2b") {
	 		$("#billpay_payment_invoice_b2c_tpl").hide();
	 		$("#billpay_payment_invoice_b2b_tpl").show("fast");
	 	}
	 });
	
	
	/*
	 * Invoice b2b select sort
	 */
	var my_options = $("#billpay_legal_status option");
	var selected = $("#billpay_legal_status").val();
	
	my_options.sort(function(a,b) {
	    if (a.text.toUpperCase() > b.text.toUpperCase()) return 1;
	    else if (a.text.toUpperCase() < b.text.toUpperCase()) return -1;
	    else return 0
	})
	
	$("#billpay_legal_status").empty().append( my_options );
	$("#billpay_legal_status").val(selected)

});

function acceptBillpayTermsInvoice()
{
	if ($('#billpay_terms_checkbox').length && !$('input#billpay_terms_checkbox:checked').length)
	{
		alert(msg);
		return false;
	}
	else
		return true;
}
