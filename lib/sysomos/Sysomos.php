<?php
namespace SmFunctions\sysomos;

class Sysomos{
	public $api_key = '2c59d79622f0deb6aba6f61019c94422';
	public $api_url = 'http://api.sysomos.com/v1/map/search/';

	public function http($url, $method, $params = false)
	{
		$url = $this->api_url . $url;

		$array = array(
			CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => "",
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 30,
		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => $method,
		  	CURLOPT_HTTPHEADER => array(
		  		"Accept: application/json",
		    	"cache-control: no-cache"
		  	),
		);

		if ($method == 'POST' || $method == 'PUT') {
			if($params != '') $array[CURLOPT_POSTFIELDS] = $params;
			array_unshift($array[CURLOPT_HTTPHEADER], "Content-Type: application/json");
		}

		$curl = curl_init();

		curl_setopt_array($curl, $array);

		$response = curl_exec($curl);
		$response = $response ? json_decode($response) : null;

		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		  	return "cURL Error #:" . $err;
		} else {
		  	return $response;
		}
		return false;


	}

	public function get_tw_user_location($users)
	{
		$method = 'PUT';
		$url    = "twitter/profiles?apiKey=$this->api_key";

		return $this->http($url, $method, $users);
	}
}