<?php 
namespace SmFunctions\hootsuiteoauth;

class HootsuiteOAuth {
	public $hootstuite_auth_url;
	public $hootstuite_api_url;
	public $client_secret;
	public $redirect_url;
	public $redirec_uri;
	public $client_id;
	public $api_url;
	public $wpdb;

	function __construct()
	{
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->set_configs();

	}

	public function http($method, $url, $type, $params = '')
	{
		if (isset($_SESSION['hootsuite_access_token'])) {
			$this->token = $_SESSION['hootsuite_access_token'];
		}

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

		if ($type == 'api') {
			array_push($array[CURLOPT_HTTPHEADER], 'Authorization: Bearer ' . $this->token);
		}

		if ($method == 'POST') {
			if($params != '') $array[CURLOPT_POSTFIELDS] = $params->params;
			array_push($array[CURLOPT_HTTPHEADER], $params->contentType);
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

	protected function set_configs()
	{
		$consumerdata = $this->wpdb->get_results("
				SELECT option_value FROM
				" . $this->wpdb->prefix . "options
				WHERE option_name 
				IN ('sm_hootsuite_consumer_key', 'sm_hootsuite_consumer_secret', 'sm_hootsuite_redirect_url')"
		);
		
		$this->client_id     = $consumerdata[0]->option_value;
		$this->client_secret = $consumerdata[1]->option_value;
		$this->redirect_url  = $consumerdata[2]->option_value;
		$this->auth_url 	 = 'https://platform.hootsuite.com/oauth2/';
		$this->api_url       = 'https://platform.hootsuite.com/v1/';
		if (isset($_SESSION['hootsuite_access_token'])) {
			$this->token = $_SESSION['hootsuite_access_token'];
		}	
	}

	public function getHoostsuiteAuthUrl()
	{
		$url  = '?response_type=code&';
		$url .= "client_id=$this->client_id&";
		$url .= 'scope=offline&';
		$url .= "redirect_uri=$this->redirect_url";

		return $this->auth_url . 'auth' . $url;
	}

	public function getAccessToken($code)
	{
		$url 	= $this->auth_url . 'token';
		$method = 'POST';

		$param  = 'grant_type=authorization_code';
		$param .= '&code=' . $code;
		$param .= '&redirect_uri=' . urlencode($this->redirect_url);
		$param .= "&client_id=$this->client_id&client_secret=$this->client_secret";
		$param  = trim($param);

		$params   		     = new \stdClass();
		$params->params 	 = $param;
		$params->contentType = 'Content-Type: application/x-www-form-urlencoded';

		return $this->http($method, $url, 'auth', $params);
	}

	public function getUserData($token)
	{
		$url    = $this->api_url . 'me';
		$method = 'GET';

		return $this->http($method, $url, 'api');
	}

	public function getProfileData()
	{
		$url    = $this->api_url . 'socialProfiles';
		$method = 'GET';

		return $this->http($method, $url, 'api');
	}

	public function checkProfiles($data, $twid)
	{
		$check      = $data[0];
		$table_name = $this->wpdb->prefix . 'hootsuite_profile_users';
		$this->wpdb->delete($table_name, array('userId' => $check->ownerId, 'twitUserId' => $twid));

		foreach ($data as $value) {
			if($value->avatarUrl) {
				$avatar = $value->avatarUrl;
			}

			$this->wpdb->insert($table_name, array(
				'userId' => $value->ownerId,
				'profileId' => $value->id,
				'serviceName' => $value->type,
				'serviceId' => $value->socialNetworkId,
				'profileUname' => $value->socialNetworkUsername,
				'twituserId' => $twid,
			));
		}

		$_SESSION['hootsuite_user']['picture'] = $avatar;
		
		$table_name = $this->wpdb->prefix . 'sm_users';
		$this->wpdb->update($table_name, ['picture' => $avatar], ['oauth_provider' => 'hootsuite', 'oauth_uid' => $value->ownerId]);
    }

    private function createMediaDate()
    {
    	$date  = date('Y-m-d H:i:s', time() + 305);
		$date  = preg_replace('/([ ])/', 'T', $date);
		$date .= 'Z';

    	return $date;
    }

    public function addPost($profile_id, $text = '', $photo = '')
    {
    	$url = $this->api_url . 'messages';

    	$post    = new \stdClass();
    	$urlData = new \stdClass();
    	$params  = new \stdClass();

    	$post->text 			 = $text;
    	$post->socialProfileIds  = array($profile_id);
    	$post->scheduledSendTime = $this->createMediaDate();
    	$post->emailNotification = true;
    	$urlData->url			 = $photo;
    	$post->mediaUrls 		 = array($urlData);
		$params->params 	 	 = json_encode($post);
		$params->contentType 	 = 'Content-Type: application/json';
		$method 				 = 'POST';

		return $this->http($method, $url, 'api', $params);
    }

    public function selectProfilesData()
    {
		$table_name = $this->wpdb->prefix . 'hootsuite_profile_users';
    	$twuid  	= $_SESSION['request_vars']['user_id'];

		return $this->wpdb->get_results("
			SELECT `profileId`, `serviceName` FROM $table_name
			WHERE twitUserId = '$twuid'
		");
    }

    private function createMessageUrl($dates, $pId)
    {
    	$sTime = $dates['interval'][0];
    	$eTime = $dates['interval'][1];

    	$url  = "messages?startTime=$sTime&";
    	$url .= "endTime=$eTime&";
    	$url .= 'state=SENT&';
    	$url .= "socialProfileIds=$pId&";
    	$url .= 'limit=100';

		return $this->api_url . $url;

    }

    public function getProfilePosts($pId)
    {
    	$sTime = time() - (3600 * 24 * 90);
    	$dates = $this->getMonthInterval($sTime);
		$url   = $this->createMessageUrl($dates, $pId);
		$count = 4;

		$method = 'GET';


		$posts = $this->http($method, $url, 'api');
		$posts = isset($posts->data) ? $posts->data : [];

		while($count--) {
			$dates = $this->getMonthInterval($dates['endTime']);
			$url   = $this->createMessageUrl($dates, $pId);
			$data  = $this->http($method, $url, 'api');
			$data  = isset($data->data) ? $data->data : [];
			$posts = array_merge($posts, $data);
		}

		return $posts;

    }

    private function getMonthInterval($sTime)
    {
		$data 			  = [];
		$data['interval'] = [];

    	$sDate  = date('Y-m-d H:i:s', $sTime);
    	$sDate  = preg_replace('/([ ])/', 'T', $sDate);
		$sDate .= 'Z';
    	$eTime  = $sTime + (3600 * 24 * 27);
    	$eDate  = date('Y-m-d H:i:s', $eTime);
    	$eDate  = preg_replace('/([ ])/', 'T', $eDate);
		$eDate .= 'Z';

		$data['interval'][] = $sDate;
		$data['interval'][] = $eDate;

		$data['endTime'] = $eTime;

		return $data;

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
			return $data;
    } 
}