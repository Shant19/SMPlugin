<?php
namespace SmFunctions\bufferoauth;

class BufferOAuth {
	public $client_id;
	public $client_secret;
	public $redirect_url;
	public $buffer_api_url;
	public $option_table;
	public $wpdb;
	public $twuid;


	function __construct()
	{
		global $wpdb;

		$this->buffer_api_url = 'https://api.bufferapp.com/1/';
		$this->wpdb 		  = $wpdb;
		$this->options_table  = $this->wpdb->prefix . 'options';
		$this->twuid          = $_SESSION['request_vars']['user_id'];
		$this->set_configs();

	}

	private function set_configs()
	{
		$result = $this->wpdb->get_results(
			"SELECT option_value FROM $this->options_table
			 WHERE option_name IN ('sm_buffer_consumer_key', 'sm_buffer_consumer_secret', 'sm_buffer_redirect_url')
			"
		);

		$this->client_id     = $result[0]->option_value;
		$this->client_secret = $result[1]->option_value;
		$this->redirect_url  = $result[2]->option_value;
	}

	public function getBufferAuthUrl()
	{
		$url  = 'https://bufferapp.com/oauth2/authorize?';
		$url .= "client_id=$this->client_id&";
		$url .= "redirect_uri=$this->redirect_url&";
		$url .= "response_type=code";
		return $url;
	}

	public function http($method, $url, $params = '')
	{
		$url = $this->buffer_api_url . $url;

		$array = array(
			CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => "",
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 30,
		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => $method,
		  	CURLOPT_HTTPHEADER => array(
		    	"cache-control: no-cache"
		  	),
		);

		if ($method == 'POST') {
			if($params != '') $array[CURLOPT_POSTFIELDS] = $params;
			array_unshift($array[CURLOPT_HTTPHEADER], "Content-Type: application/x-www-form-urlencoded");
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

	public function getAccessToken($code)
	{
		$method  = 'POST';
		$url     = 'oauth2/token.json';
		$params  = "client_id=$this->client_id&client_secret=$this->client_secret";
		$params .= '&redirect_uri=' . urlencode($this->redirect_url);
		$params .= '&code=' . urlencode($code);
		$params .= '&grant_type=authorization_code';

		return $this->http($method, $url, $params);
	}

	public function getUserData($access_token)
	{	
		$method = 'GET';
		$url 	= "user.json?access_token=$access_token";

		return $this->http($method, $url);
	}

	public function selectProfilesData()
	{
		$table_name = $this->wpdb->prefix . 'buffer_profile_users';
		return $this->wpdb->get_results("
			SELECT `profileId`, `serviceName` FROM $table_name
			WHERE twitUserId = '$this->twuid'
		");
	}

	public function getProfileData($access_token)
	{
		$method = 'GET';
		$url 	= "profiles.json?access_token=$access_token";

		return $this->http($method, $url);
	}

	public  function updateSchedules($profile_id, $access_token, $schedules)
	{
		$method = 'POST';
		$url 	= "profiles/$profile_id/schedules/update.json?access_token=$access_token";
		$params = '';

		for($i = 0;$i < count($schedules['days']); $i++) {
			$i == 0 ? $params .= urlencode('schedules[0][days][]') . '=' . urlencode($schedules['days'][$i]) : $params .= '&' . urlencode('schedules[0][days][]') . '=' . urlencode($schedules['days'][$i]);
		}

		for($i = 0;$i < count($schedules['times']); $i++) {
			$params .= '&' . urlencode('schedules[0][times][]') . '=' . urlencode($schedules['times'][$i]);
		}

		return $this->http($method, $url, $params);
	}

	public function getSocialMediaUpdates($profile_id, $access_token)
	{
		$method = 'GET';
		$url 	= "profiles/$profile_id/updates/pending.json?access_token=$access_token";				   

		return $this->http($method, $url);
	}

	public function addPost($profile_id, $access_token, $text = '', $media = '', $photo = '')
	{
		$method  = 'POST';
		$url 	 = "updates/create.json?access_token=$access_token";
		$params  = 'text=' . urlencode($text) . '&';
		$params .= 'profile_ids%5B%5D=' . urlencode($profile_id) . '&';
		$params .= 'media%5Blink%5D=' . urlencode($media) . '&';
		$params .= 'media%5Bphoto%5D=' . urlencode($photo) . '&';
		$params .= 'media%5Bdescription%5D=Desc';

		$result  = $this->http($method, $url, $params);
		
		if ($result->success) {
			$post_id = $result->updates[0]->id;
			$method  = 'POST';
			$url 	 = "updates/$post_id/share.json?access_token=$access_token";

			return $this->http($method, $url);
		} else {
			return $result->message;
		}
	}

	public function getProfilePosts($profile_id, $access_token)
	{
		$method = 'GET';
		$url 	= "profiles/$profile_id/updates/sent.json?access_token=$access_token";

		return $this->http($method, $url);
	}

	public function getProfilePostsData($post_id, $access_token)
	{
		$method  = 'GET';
		$url 	 = "updates/$post_id.json?access_token=$access_token";

		return $this->http($method, $url);
	}


	public function checkUser($oauth_provider, $tw_uid, $oauth_uid, $username, $fname, $lname, $email, $locale, $oauth_token, $oauth_secret, $profile_image_url, $followers_count, $friends_count, $statuses_count)
	{
			$table_name = $this->wpdb->prefix . 'sm_users';
			$user_info  = $this->wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = '$oauth_provider' AND oauth_uid = '$oauth_uid'");
			
			$data = array(
                'oauth_provider' => !empty($oauth_provider) ? $oauth_provider : '',
                'oauth_uid' => !empty($oauth_uid) ? $oauth_uid : '',
                'twitter_oauth' => $tw_uid,
                'username' => !empty($username) ? $username : '',
                'fname' => !empty($fname) ? $fname : '',
                'lname' => !empty($lname) ? $lname : '',
                'email' => !empty($email) ? $email : '',
                'locale' => !empty($locale) ? $locale : '', 
                'oauth_token' => !empty($oauth_token) ? $oauth_token : '', 
                'oauth_secret' => !empty($oauth_secret) ? $oauth_secret : '',
                'picture' => !empty($profile_image_url) ? $profile_image_url : '', 
                'created' => date('Y-m-d H:i:s'), 
                'modified' => date('Y-m-d H:i:s'),
                'level' => 0,
                'followers_count' => !empty($followers_count) ? $followers_count : 0 , 
                'friends_count' => !empty($friends_count) ? $friends_count : 0, 
                'statuses_count' => !empty($statuses_count) ? $statuses_count : 0 , 
                'allow_public_chat' => 0,
                'allow_notify_followers' => 0,
                'disconnected' => 0,
            );	
			if (!empty($user_info)) {
				$this->wpdb->update($table_name, $data, ['oauth_provider' => $oauth_provider, 'oauth_uid' => $oauth_uid]);
			} else {
				$this->wpdb->insert($table_name, $data); 
			}
          
    } 

	public function checkProfiles($data)
	{
		$check      = $data[0];
		$table_name = $this->wpdb->prefix . 'buffer_profile_users';
		$this->wpdb->delete($table_name, array('userId' => $check->uid, 'twitUserId' => $check->twid));

		foreach ($data as $value) {
			$this->wpdb->insert($table_name, array(
				'userId' => $value->uid,
				'profileId' => $value->id,
				'serviceName' => $value->service,
				'serviceId' => $value->sid,
				'status' => $value->status,
				'twituserId' => $value->twid,
			));
		}
    }   

    public function removeBufferAccount()
    {
    	unset($_SESSION['buffer_access_token']);
    	unset($_SESSION['buffer_user']);
    	$table_name = $this->wpdb->prefix . 'sm_users';
    	$this->wpdb->delete($table_name, array('twitter_oauth' => $this->twuid, 'oauth_provider' => 'buffer'));
    	$table_name = $this->wpdb->prefix . 'buffer_profile_users';
    	$this->wpdb->delete($table_name, array('twitUserId' => $this->twuid));
    }



}