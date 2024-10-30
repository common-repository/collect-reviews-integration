<?php

/**
 * Collect Review Class
 *
 * @package     CollectReviews
 * @author      Ivan Timofeev, Andrew Pavlow
 * @copyright   2018 Collect-Reviews.com
 * @license     GPL-2.0+
 */

class Collect_Review_Api{

	const CR_API_URL = 'https://api.collect-reviews.com/v1.3';

	protected $clientid;
	protected $token;

	function __construct($clientid = '', $token = ''){
		$this->clientid = $clientid;
		$this->token = $token;
	}

	public function test(){
		if(empty($this->clientid))
			return false;
		else{
			$result = $this->sendProducts(array());

			#BugFu::log("sending cridentials for a test ");
			#BugFu::log($result);

			if(!is_wp_error($result) && $result['code'] == "200")
				return true;
			else
				return false;
		}
	}

	public function sendProducts($orders){
		$data = array("Token" => $this->token, "Orders" => $orders);
		$json_data = json_encode($data);
		$WP_Http = new WP_Http();
		$args = array("method" => "POST", "headers" => array('Content-Type' => 'application/json'), "body" => $json_data);
		$result = $WP_Http->request(self::CR_API_URL.'/clients/'.$this->clientid.'/orders', $args);

		#BugFu::log("Send products");
		#BugFu::log($result);
		#BugFu::log($json_data);
		return is_wp_error($result)?$result:$result['response'];
	}

	public static function activatePlugin($site){
		$WP_Http = new WP_Http();
		$res = $WP_Http->get(self::CR_API_URL."/plugins/woocomerce/".$site."/activated");

		#BugFu::log("Activation Request has been sent");
		#BugFu::log($res);

	}

	public static function deactivatePlugin($site){
		$WP_Http = new WP_Http();
		$res = $WP_Http->get(self::CR_API_URL."/plugins/woocomerce/".$site."/deactivated");
		#BugFu::log('Deactivation reques has been sent');
		#BugFu::log($res);

	}

	public static function uninstallPlugin($site){
		$WP_Http = new WP_Http();
		$WP_Http->get(self::CR_API_URL."/plugins/woocomerce/".$site."/deleted");

	}



}
