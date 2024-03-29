<?php

require_once(dirname(__FILE__).'/ipl_xml_request.php');

/**
 * @author Jan Wehrs (jan.wehrs@billpay.de)
 * @copyright Copyright 2010 Billpay GmbH
 * @license commercial 
 */
class ipl_invoice_created_request extends ipl_xml_request {
	
	private $_invoice_params = array();
	private $_payment_info_params = array();
	
	// bank account
	private $account_holder;
	private $account_number;
	private $bank_code;
	private $bank_name;
	private $invoice_reference;
	private $invoice_duedate;
	private $activation_performed;
	
	private $payment_info_html;
	private $payment_info_plain;
	
	private $dues = array();
	
	public function get_account_holder() {
		return $this->account_holder;
	}
	public function get_account_number() {
		return $this->account_number;
	}
	public function get_bank_code() {
		return $this->bank_code;
	}
	public function get_bank_name() {
		return $this->bank_name;
	}
	public function get_invoice_reference() {
		return $this->invoice_reference;
	}
	public function get_invoice_duedate() {
		return $this->invoice_duedate;
	}
	public function get_activation_performed() {
		return $this->activation_performed;
	}
	public function get_payment_info_html() {
		return $this->payment_info_html;
	}
	public function get_payment_info_plain() {
		return $this->payment_info_plain;
	}
	
	public function get_dues() {
		return $this->dues;
	}
	 	
	public function set_invoice_params($carttotalgross, $currency, $reference, $delayindays = 0) {
		$this->_invoice_params['carttotalgross'] = $carttotalgross;
		$this->_invoice_params['currency'] = $currency;
		$this->_invoice_params['reference'] = $reference;
		$this->_invoice_params['delayindays'] = $delayindays;
	}
	
	public function set_payment_info_params($showhtmlinfo, $showplaininfo) {
		$this->_payment_info_params['htmlinfo'] = $showhtmlinfo ? "1" : "0";
		$this->_payment_info_params['plaininfo'] = $showplaininfo ? "1" : "0";
	}
	
	protected function _send() {
		return ipl_core_send_invoice_request($this->_ipl_request_url, $this->_default_params, $this->_invoice_params, $this->_payment_info_params);
	}
	
	protected function _process_response_xml($data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}
	
}

?>