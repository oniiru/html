<?php
if(!class_exists('WpPostHelper')){
	require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/common/wppost-helper.php';
}

class StripeSettings {
	const LIVE_PUBLIC_KEY = "stripe_payment_live_public_key";
	const LIVE_SECRET_KEY = "stripe_payment_live_secret_key";
	const TEST_PUBLIC_KEY = "stripe_payment_test_public_key";
	const TEST_SECRET_KEY = "stripe_payment_test_secret_key";
	const IS_LIVE_KEYS    = "stripe_payment_is_live_keys";
	const CURRENCY_SYMBOL = "stripe_payment_currency_symbol";
	const WEBHOOK_URL	  = "stripe_payment_webhook_url";
	
	
	public $isLive;
	public $livePublicKey;
	public $liveSecretKey;
	public $testPublicKey;
	public $testSecretKey;
	public $currencySymbol;
	public $webHookUrl;
	
	private $wpPostHelper;

	function __construct() {
		$this->fetchAll();
		
		$this->wpPostHelper = new Dl_Wp_Post_Helper();
	}
	
	public function setIsLive($val) {
		$this->setAndFetch(self::IS_LIVE_KEYS, $val);
	}
	public function setLivePublicKey($val) {
		$this->setAndFetch(self::LIVE_PUBLIC_KEY, $val);
	}
	public function setLiveSecretKey($val) {
		$this->setAndFetch(self::LIVE_SECRET_KEY, $val);
	}
	public function setTestPublicKey($val) {
		$this->setAndFetch(self::TEST_PUBLIC_KEY, $val);
	}
	public function setTestSecretKey($val) {
		$this->setAndFetch(self::TEST_SECRET_KEY, $val);
	}
	public function setCurrencySymbol($val) {
		$this->setAndFetch(self::CURRENCY_SYMBOL, $val);
	}
	public function getPaymentUrl() {
		$url = site_url();
		// ensure SSL
		$type = strtolower( substr($url, 0, 5) );
		if($type != 'https') {
			$url = 'https'.substr($url, 4);
		}
		return $url;
	}
	public function setWebHookUrl($val) {
		if( $val != $this->webHookUrl ) {
			$this->updatePage($this->webHookUrl, $val);
		}
		$this->setAndFetch(self::WEBHOOK_URL, $val);
	}
	public function getWebHookUrl() {

		if( empty( $this->webHookUrl ) ) {

			return false;
		}

		return site_url(null, null, 'https').'/'.$this->webHookUrl.'/';
	}
	
	public function getPublicKey() {
		if($this->isLive) {
			return $this->livePublicKey;
		}
		return $this->testPublicKey;
	}
	
	public function getSecretKey() {
		if($this->isLive) {
			return $this->liveSecretKey;
		}
		return $this->testSecretKey;
	}
	
	function isValid() {
			
		$error = "";
		if( strlen($this->getPublicKey())==0) {
			$error .= "<li>Public key is not set.</li>";
		}
		if( strlen($this->getSecretKey())==0) {
			$error .= "<li>Secret key is not set.</li>";
		}
		if( strlen($this->currencySymbol)==0) {
			$error .= "<li>Secret key is not set.</li>";
		}
		
		if(strlen($error)>0) {
			$error = "<div class='stripe-payment-config-errors'><p>Fix the following configuration errors before using the form.</p><ul>".$error."</ul></div>";
		}
		
		return $error;
	}

	
	private function setAndFetch($key, $val) {
		update_option($key, $val);
		$this->fetchAll();
	}
	
	private function fetchAll() {
		$isLiveKeys 			= get_option(self::IS_LIVE_KEYS);
		$this->isLive 			= strlen($isLiveKeys)==0 ? false : true;
		$this->livePublicKey 	= get_option(self::LIVE_PUBLIC_KEY);
		$this->liveSecretKey 	= get_option(self::LIVE_SECRET_KEY);
		$this->testPublicKey 	= get_option(self::TEST_PUBLIC_KEY);
		$this->testSecretKey 	= get_option(self::TEST_SECRET_KEY);
		$this->currencySymbol 	= get_option(self::CURRENCY_SYMBOL);
		$this->webHookUrl 		= get_option(self::WEBHOOK_URL);
	}
	
	private function updatePage($old, $new) {
		$this->wpPostHelper->deletePage($old);
		$this->wpPostHelper->updatePage($new, $new);
	}

}

?>