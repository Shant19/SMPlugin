<?php 
namespace SmFunctions\linkedinoauth;

class LinkedinOAuth {

	public $linkedin_api_url;
	public $consumer_secret;
	public $consumer_key;
	public $redirect_url;
	public $auth_url;
	public $api_url;
	public $token;
	public $wpdb;
	public $uId;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->set_configs();
		$this->uId = $_SESSION['linkedin_user']['oauth_uid'];
	}

	public function http($method, $url, $params = '', $type = '')
	{
		$url = $type != 'token' ? $this->api_url . $url : $this->auth_url . $url;

		$array = array(
			CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
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
				IN ('sm_linkedin_consumer_key', 'sm_linkedin_consumer_secret', 'sm_linkedin_redirect_url')"
			);


		$this->consumer_key    = $consumerdata[0]->option_value;
		$this->consumer_secret = $consumerdata[1]->option_value;
		$this->redirect_url    = $consumerdata[2]->option_value;
		$this->auth_url 	   = 'https://api.linkedin.com/oauth/v2/';
		$this->api_url         = 'https://api.linkedin.com/v2/';
		if (isset($_SESSION['linkedin_access_token'])) {
			$this->token = $_SESSION['linkedin_access_token'];
		}
	}

	public function getLinkedinAuthUrl()
	{
		$url .= 'authorization?response_type=code&';	
		$url .= "client_id=$this->consumer_key&";
		$url .= "redirect_uri=$this->redirect_url&";
		$url .= "scope=r_liteprofile%20r_emailaddress%20w_member_social";

		$url = $this->auth_url . $url;
		return $url;
	}

	public function getAccessToken($code)
	{
		$url .= 'accessToken?grant_type=authorization_code&';	
		$url .= "code=$code&";
		$url .= "redirect_uri=$this->redirect_url&";
		$url .= "client_id=$this->consumer_key&";
		$url .= "client_secret=$this->consumer_secret";

		$method = 'GET';
		$params = '';
		$type   = 'token';
		$result = $this->http($method, $url, $params, $type);
		return $result;
	}

	public function getUserInfo()
	{
		$this->token = $_SESSION['linkedin_access_token'];
		
		$url  = 'me';
		$url .= '?projection=' . urlencode('(id,firstName,lastName,email,profilePicture(displayImage~:playableStreams))');
		$url .= "&oauth2_access_token=$this->token";

		$method = 'GET';
		
		return $this->http($method, $url);
	}

	public function checkUser($data)
	{
		$table_name     = $this->wpdb->prefix . 'sm_users';
		$oauth_provider = 'linkedin';
		$oauth_uid      = $data['oauth_uid'];
		$user_info      = $this->wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = '$oauth_provider' AND oauth_uid = '$oauth_uid'");

		if (!empty($user_info)) {
			$this->wpdb->update($table_name, $data, ['oauth_provider' => $oauth_provider, 'oauth_uid' => $oauth_uid]);
		} else {
			$this->wpdb->insert($table_name, $data); 
		}

	}

	public function addPost($data)
	{
		$bigData = new \stdClass();
		$content = new \stdClass();
		$contentEntities = [];
		$contentEntitiesData = new \stdClass();
		$contentEntitiesData->entityLocation = $data->tPhoto;
		$thumbnails = [];
		$thumbnailsData = new \stdClass();
		$thumbnailsData->resolvedUrl = $data->tPhoto;
		$thumbnails[] = $thumbnailsData; 
		$contentEntitiesData->thumbnails = $thumbnails;
		$contentEntities[] = $contentEntitiesData;
		$content->contentEntities = $contentEntities;
		$content->title = $data->tPost;
		$bigData->content = $content;
		$distribution = new \stdClass();
		$distribution->linkedInDistributionTarget = new \stdClass();
		$bigData->distribution = $distribution;
		$bigData->owner = 'urn:li:person:' . $_SESSION['linkedin_user']['oauth_uid'];
		$bigData->subject = 'qwe';
		$text = new \stdClass();
		$text->text = $data->tPost;
		$bigData->text = $text;
		$bigData = json_encode($bigData);


		$method = 'POST';
		$type   = 'token';
		$url    = "shares?oauth2_access_token=$this->token";
		$result = $this->http($method, $url, $bigData);

		return json_encode($result);
	}

	public function getPosts()
	{
		$method  = 'GET';
		$url     = "shares?q=owners&owners=urn:li:person:$this->uId&sharesPerOwner=100action=registerUpload&oauth2_access_token=$this->token";

		return $this->http($method, $url);
	}
}