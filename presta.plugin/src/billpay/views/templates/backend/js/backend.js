$(document).ready(function(){

    $('a#billpay_invoice_fee_b2c').click(function(){
        $("#billpay_invoice_fee_b2c_hint").slideToggle();
    });
    $('a#billpay_invoice_fee_b2b').click(function(){
        $("#billpay_invoice_fee_b2b_hint").slideToggle();
    });
    $('a#billpay_invoice_fee_dd').click(function(){
        $("#billpay_invoice_fee_dd_hint").slideToggle();
    });

});