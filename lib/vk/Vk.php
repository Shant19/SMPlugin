<?php 
namespace SmFunctions\vk;

class Vk {

	public $consumer_secret;
	public $consumer_key;
	public $redirect_url;
	public $vk_api_url;
	public $auth_url;
	public $app_id;
	public $token;
	public $wpdb;

	public function __construct() 
	{
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->set_configs();

		if(isset($_SESSION['vk_access_token'])) {
			$this->token = $_SESSION['vk_access_token'];
		}
	}

	public function http($method, $url, $params = '', $type = '')
	{
		$array = array(
			CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => $method,
		  	CURLOPT_HTTPHEADER => array(
			    "Accept: */*",
			    "Cache-Control: no-cache",
			    "Connection: keep-alive",
		  	),
		);

		if ($method == 'POST') {
			if($params != '') $array[CURLOPT_POSTFIELDS] = $params;
			array_push($array[CURLOPT_HTTPHEADER], "Content-Type: application/json");
		}

		$curl = curl_init();

		curl_setopt_array($curl, $array);

		$response = curl_exec($curl);
		$response = json_decode($response);

		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		  	return "cURL Error #:" . $err;
		} else {
		  	return $response;
		}
		return false;
	}

	private function set_configs()
	{
		global $wpdb;

		$consumerdata = $wpdb->get_results("
				SELECT option_value FROM
				" . $wpdb->prefix . "options
				WHERE option_name 
				IN ('sm_vk_consumer_key', 'sm_vk_consumer_secret', 'sm_vk_redirect_url', 'sm_vk_app_id')"
			);


		$this->consumer_key    = $consumerdata[1]->option_value;
		$this->consumer_secret = $consumerdata[2]->option_value;
		$this->redirect_url    = $consumerdata[3]->option_value;
		$this->app_id		   = $consumerdata[0]->option_value;
		$this->auth_url 	   = 'https://oauth.vk.com/authorize';
		$this->api_url         = 'https://api.vk.com/method/';
		if (isset($_SESSION['vk_access_token'])) {
			$this->token = $_SESSION['vk_access_token'];
		}
	}

	public function getVkAuthUrl()
	{
		$url .= "?client_id=$this->app_id&";	
		$url .= 'display=popup&';
		$url .= "redirect_uri=$this->redirect_url&";
		// $url .= 'scope=NOTIFY,FRIENDS,PHOTOS,PAGES,LINK,NOTES,ADS,DOCS,NOTIFICATIONS,STATS,EMAIL,MARKET,OFFLINE,GROUPS,AUDIO,VIDEO,STATUS,WALL&';
		$url .= 'scope=wall,offline,photo&';
		$url .= 'response_type=token&v=5.102';

		$url = $this->auth_url . $url;
		return $url;
	}

	public function get_user_info($userId, $token)
	{
		$url  = "users.get?user_id=$userId&v=5.102&";
		$url .= "access_token=$token&";
		$url .= "fields=sex,bdate,city,country,photo,followers_count,common_count";

		$method = 'GET';

		$url = $this->api_url . $url;
		return $this->http($method, $url);
	}

	public function checkUser($data)
	{
		$table_name     = $this->wpdb->prefix . 'sm_users';
		$oauth_provider = 'vk';
		$oauth_uid      = $data['oauth_uid'];
		$user_info      = $this->wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = '$oauth_provider' AND oauth_uid = '$oauth_uid'");

		if (!empty($user_info)) {
			$this->wpdb->update($table_name, $data, ['oauth_provider' => $oauth_provider, 'oauth_uid' => $oauth_uid]);
		} else {
			$this->wpdb->insert($table_name, $data);
		}

	}

	public function get_all_posts($id)
	{
		$url  = "wall.get?v=5.102&";
		$url .= "access_token=$this->token&";
		$url .= "owner_id=$id";
		
		$method = 'GET';

		$url = $this->api_url . $url;
		return $this->http($method, $url);
	}

	public function add_post($id, $data)
	{
		$url = 'wall.post?v=5.102&';
		$url .= "access_token=$this->token&";
		$url .= "owner_id=$id&";
		$url .= "message=$data->tPost&";
		$url .= "attachments=$data->tPhoto&";
		$url .= "from_group=1";

		$method = 'GET';

		$url = $this->api_url . $url;
		return $this->http($method, $url);	
	}
}