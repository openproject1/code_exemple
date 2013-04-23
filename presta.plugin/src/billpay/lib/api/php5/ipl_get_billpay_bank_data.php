<?php

require_once(dirname(__FILE__).'/ipl_xml_request.php');

/**
 * @author Jan Wehrs (jan.wehrs@billpay.de)
 * @copyright Copyright 2010 Billpay GmbH
 * @license commercial 
 */
class ipl_get_billpay_bank_data extends ipl_xml_request {
	
	// response parameters
	private $account_holder;
	private $account_number;
	private $bank_code;
	private $bank_name;
	private $invoice_reference;
	private $invoice_duedate;
	
	var $reference;
	
	public function get_account_holder() {
		if (is_null($this->reference)) {
			return 'reference not set!';
		}
		
		return $this->str_rand(10).' '.$this->str_rand(15);
		//return $this->account_holder;
	}
	public function get_account_number() {
		if (is_null($this->reference)) {
			return 'reference not set!';
		}
		
		return 'IBAN_TEST_'.str_pad(rand(1234,999999999999), 12, "0", STR_PAD_LEFT);
		//return $this->account_number;
	}
	public function get_bank_code() {
		if (is_null($this->reference)) {
			return 'reference not set!';
		}
		
		return 'BIC_TEST_'.$this->str_rand(10);
		//return $this->bank_code;
	}
	public function get_bank_name() {
		if (is_null($this->reference)) {
			return 'reference not set!';
		}
		
		$n = rand(0,1);
		if ($n == 0) {
			return 'Berliner Sparkasse';
		}
		else {
			return 'Postfinance';
		}
		//return $this->bank_name;
	}
	public function get_invoice_reference() {
		if (is_null($this->reference)) {
			return 'reference not set!';
		}
		
		return 'BP'.$this->reference.'/'.$this->_default_params['mid'];
		//return $this->invoice_reference;
	}
	//function get_invoice_duedate() {
	//	return $this->invoice_duedate;
	//}
	
	public function set_order_reference($reference) {
		$this->reference = $reference;
	}
	
	protected function _send() {
		return true;
	}
	
	protected function _process_response_xml($data) {
		//foreach ($data as $key => $value) {
		//	$this->$key = $value;
		//}
	}
	
	protected function _process_error_response_xml($data) {
		if (key_exists('status', $data)) {
			$this->status = $data['status'];
		}
	}
	
	# Snippet from PHP Share: http://www.phpshare.org

	/**
	 * Generate and return a random string
	 *
	 * The default string returned is 8 alphanumeric characters.
	 *
	 * The type of string returned can be changed with the "seeds" parameter.
	 * Four types are - by default - available: alpha, numeric, alphanum and hexidec. 
	 *
	 * If the "seeds" parameter does not match one of the above, then the string
	 * supplied is used.
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     2.1.0
	 * @link        http://aidanlister.com/repos/v/function.str_rand.php
	 * @param       int     $length  Length of string to be generated
	 * @param       string  $seeds   Seeds string should be generated from
	 */
	private function str_rand($length = 8, $seeds = 'alphanum') {
		// Possible seeds
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['hexidec'] = '0123456789abcdef';

		// Choose seed
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}

		// Seed generator
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);

		// Generate
		$str = '';
		$seeds_count = strlen($seeds);

		for ($i = 0; $length > $i; $i++) {
			$str .= $seeds{mt_rand(0, $seeds_count - 1)};
		}

		return $str;
	}
}

?>