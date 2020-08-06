<?php 
session_start();

use SmFunctions\googlecustomsearch\GoogleCustomSearch;
use SmFunctions\hootsuiteoauth\HootsuiteOAuth;
use SmFunctions\linkedinoauth\LinkedinOAuth;
use SmFunctions\twitteroauth\TwitterOAuth;
use SmFunctions\bufferoauth\BufferOAuth;
use SmFunctions\sysomos\Sysomos;
use SmFunctions\vk\Vk;

class Actions {
	public $oauth_token_secret;
	public $consumer_secret;
	public $consumer_key;
	public $callback_url;
	public $hootsuiteAPI;
	public $oauth_token;
	public $linkedinAPI;
	public $twitterAPI;
	public $bufferAPI;
	public $base_url;
	public $site_url;
	public $user_id;
	public $sysomos;
	public $google;
	public $vk;
	public $db;

	public function __construct()
	{
		$this->get_configs();
		$this->set_consumer_data();
		$this->set_oauth_data();

		$this->hootsuiteAPI = new HootsuiteOAuth();
		$this->linkedinAPI = new LinkedinOAuth();
		$this->twitterAPI = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);
		$this->bufferAPI = new BufferOAuth();
		$this->sysomos = new Sysomos();
		$this->google = new GoogleCustomSearch('003663662691120026833:l_xvgvu5rsu', 'AIzaSyAJuybRsrUiDM5vP9pEkhxFiEyjmh9kwOo');
		$this->vk = new Vk();


		if (isset($_POST['action'])) {
			if (method_exists($this, $_POST['action'])) {
				call_user_func(array($this, $_POST['action']), $this->decodeJson($_POST['data']));
			}
		} else {
			if (!isset($_POST['vk_hash'])) {
				$this->check_hash();
			} else if (isset($_POST['vk_hash'])) {
				$this->get_vk_user($_POST);
			}
		}

		if($_GET['vk']) { $this->vk_auth(); }
		if($_GET['buffer']) { $this->buffer_auth(); }
		if($_GET['twitter']) { $this->twitter_auth(); }
		if($_GET['linkedin']) { $this->linkedin_auth(); }
		if($_GET['hootsuite']) { $this->hootsuite_auth(); }
		if(isset($_GET['state']) && isset($_GET['code']) && !isset($_GET['scope'])) { $this->get_buffer_token(); }
		if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) { $this->set_twitter_data(); }
		if(isset($_GET['code']) && count($_GET) == 1) { $this->get_linkedin_token(); }
		if(isset($_GET['expires_in']) && isset($_GET['user_id'])) { $this->get_vk_user(); }
		if(isset($_GET['code']) && isset($_GET['scope']) && isset($_GET['state']) && $_GET['scope'] == 'offline') { $this->get_hootsuite_access_token(); }
	}

	private function getRss($url)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$res = simplexml_load_string($response);
		$res = json_encode($res);
		
		return json_decode($res, true);
	}

	private function post($data, $method = 'POST')
	{
		$params = [];

		foreach ($data as $key => $value) {
			$params[] = "$key=$value";
		}
		$params = implode('&', $params);

		$curl = curl_init();
		// https://publisher.ki.social/wpApi/api.php
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://publisher.ki.social/wpApi/api.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => $method,
		  CURLOPT_POSTFIELDS => $params,
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/x-www-form-urlencoded"
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	public function check_hash()
	{
		?>
		<form action="" id="hash" method="post">
			<input name="vk_hash" value="window.location.hash" type="hidden">
		</form>
		<script>
			if(window.location.hash) {
				var form = document.getElementById('hash')
				form.children[0].value = window.location.hash;
				form.submit()
			}
		</script>
		<?php
	}


	public function updateStatusesCount($post_status)
	{	
		global $wpdb;

		$table_name = $wpdb->prefix."sm_users";
		$user = $post_status->user;

		$user_id = isset($user->id) ? $user->id : 0 ;
		$statuses_count = isset($user->statuses_count) ? $user->statuses_count : 0 ;

		$user_info = $wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = 'twitter' AND oauth_uid = '$user_id'");

		if (!empty($user_info)) {
			
			$wpdb->update($table_name, array('statuses_count'=>$statuses_count), array('oauth_provider'=>'twitter','oauth_uid' => $user_id ));

		}

	}

	public function showToken($data)
	{
		print_r($_SESSION);
	}

	public function decodeJson($json)
	{
		return json_decode(stripslashes($json));
	}

	public function get_all_linkedin_posts($data)
	{
		echo json_encode($this->linkedinAPI->getPosts());
	}

	public function linkedin_auth()
	{
		header('Location:' . $this->linkedinAPI->getLinkedinAuthUrl());
	}

	public function get_linkedin_token()
	{
		$res = $this->linkedinAPI->getAccessToken($_GET['code']);
		$_SESSION['linkedin_access_token'] = $res->access_token;
		$this->set_linkedin_user_data();
	}

	public function set_linkedin_user_data()
	{
		$key       = '';
		$user_info = $this->linkedinAPI->getUserInfo();

	    $put = new stdClass();
	    $put->uId = md5("{$user_info->id}{$_SERVER['SERVER_NAME']}");
	    $put->consumer_key = '';
	    $put->consumer_secret = '';
		$put->token = $_SESSION['linkedin_access_token'];
		$put->token_secret = '';
		$put->media = 'linkedin';

		$this->post($put);

		foreach ($user_info->firstName->localized as $k => $value) {$key = $k;}

		$data = [];
		$data['oauth_provider'] = 'linkedin';
		$data['twitter_oauth'] = $_SESSION['request_vars']['user_id'];
		$data['oauth_uid'] = $user_info->id;
		$data['username'] = $user_info->firstName->localized->{$key} . ' ' . $user_info->lastName->localized->{$key};
		$data['fname'] = $user_info->firstName->localized->{$key};
		$data['lname'] = $user_info->lastName->localized->{$key};
		$data['email'] = isset($user_info->email) ? $user_info->email : '';
		$data['locale'] = $user_info->firstName->preferredLocale->language;
		$data['oauth_token'] = $_SESSION['linkedin_access_token'];
		$data['oauth_secret'] = '';
		$data['picture'] = $user_info->profilePicture->{'displayImage~'}->elements[2]->identifiers[0]->identifier;
		$data['created'] = date("Y-m-d H:i:s");
		$data['modified'] = date("Y-m-d H:i:s");
		$data['level'] = '0';
		$data['followers_count'] = '0';
		$data['friends_count'] = '0';
		$data['statuses_count'] = '0';
		$data['allow_public_chat'] = '0';
		$data['allow_notify_followers'] = '0';
		$data['disconnected'] = '0';

		$this->linkedinAPI->checkUser($data);

		$_SESSION['linkedin_user'] = $data;
		header("Location: $this->site_url");

	}

	public function add_linkedin_post($data)
	{
		$this->linkedinAPI->addPost($data);
	}

	public function twitter_auth()
	{
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
		$request_token = $connection->getRequestToken($this->callback_url);

	    $fp = fopen('lidn.txt', 'w');
		fwrite($fp, $request_token['oauth_token'] . ';');
		fwrite($fp, $request_token['oauth_token_secret'] . ';');
		fwrite($fp, $_GET['url'] . ';');
		fclose($fp);

		$twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
		header("Location: $twitter_url");
	}

	public function set_twitter_data()
	{
		global $wpdb;

		$fh   = fopen('lidn.txt', 'r');
		$line = explode(';', fgets($fh));

		$connection   = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $line[0] , $line[1]);
	    $access_token = $connection->getAccessToken($_GET['oauth_verifier']);


		$user_info  = $connection->get('account/verify_credentials', array('include_email' => 'true'));
		$table_name = $wpdb->prefix . "sm_users";

	    $put = new stdClass();
	    $put->uId = md5("{$user_info->id}{$_SERVER['SERVER_NAME']}");
	    $put->consumer_key = $this->consumer_key;
	    $put->consumer_secret = $this->consumer_secret;
		$put->token = $access_token['oauth_token'];
		$put->token_secret = $access_token['oauth_token_secret'];
		$put->media = 'twitter';

		$this->post($put);

		$user_info->lang = empty($user_info->extended_entities->lang) ? 'en' : $user_info->extended_entities->lang;

		$name  = explode(" ",$user_info->name);
	    $fname = isset($name[0])?$name[0]:'';
	    $lname = isset($name[1])?$name[1]:'';

	    property_exists($user_info, 'email') ? $user_info->email = $user_info->email : $user_info->email = '';

		$data = [];
		$data['oauth_provider'] = 'twitter';
		$data['oauth_uid'] = $user_info->id;
		$data['username'] = $user_info->screen_name;
		$data['fname'] = $fname;
		$data['lname'] = $lname;
		$data['email'] = $user_info->email;
		$data['locale'] = $user_info->lang;
		$data['oauth_token'] = $access_token['oauth_token'];
		$data['oauth_secret'] = $access_token['oauth_token_secret'];
		$data['picture'] = $user_info->profile_image_url;
		$data['created'] = date("Y-m-d H:i:s");
		$data['modified'] = date("Y-m-d H:i:s");
		$data['level'] = '0';
		$data['followers_count'] = $user_info->followers_count;
		$data['friends_count'] = $user_info->friends_count;
		$data['statuses_count'] = $user_info->statuses_count;
		$data['allow_public_chat'] = '0';
		$data['allow_notify_followers'] = '0';
		$data['disconnected'] = '0';

		$res = $wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = 'twitter' AND oauth_uid = '$user_info->id'");
		if (empty($res)) {
			$res = $wpdb->insert($table_name, $data);
		} else {
			$where = array('oauth_provider' => 'twitter', 'oauth_uid' => $data['oauth_uid']);
			$res = $wpdb->update($table_name, $data, $where);
		}
		$_SESSION['status'] = 'verified';
	    $_SESSION['request_vars']['screen_name'] = $data['username'];
	    $_SESSION['request_vars']['user_id'] = $data['oauth_uid'];
	    $_SESSION['request_vars']['oauth_token'] = $data['oauth_token'];
	    $_SESSION['request_vars']['oauth_token_secret'] = $data['oauth_secret'];
	    $_SESSION['request_vars']['picture'] = $data['picture'];
	    $_SESSION['request_vars']['followers'] = $data['followers_count'];
	    $_SESSION['request_vars']['friends_count'] = $data['friends_count'];
	    $_SESSION['request_vars']['statuses_count'] = $data['statuses_count'];
	    $_SESSION['username'] = $data['fname'];



	    header('Location: /ki-publish-stream');
	}

	public function add_twitter_post($data)
	{
		$media_info = $this->twitterAPI->post('media/upload', array('media_data' => base64_encode( file_get_contents($data->tPhoto) ) ), 1);

		$status_arr = array(
		    'status' => $data->tPost,
		    'in_reply_to_status_id' => ''
		);

		if(isset($media_info->media_id)){
        	$status_arr['media_ids'] = $media_info->media_id;
		}

	    $res = $this->twitterAPI->post('statuses/update', $status_arr);
	    echo json_encode($res);
	}

	public function get_all_twitter_posts($data)
	{
		$res = $this->twitterAPI->get('statuses/user_timeline', array('user_id' => $_SESSION['request_vars']['user_id']));
		echo json_encode($res);
	}

	public function get_twitter_updates($data)
	{
		$res = $this->twitterAPI->get('statuses/lookup', array('id' => implode(',', $data)));
		echo json_encode($res);
	}

	public function get_tw_followers($data)
	{
		$res = $this->twitterAPI->get('followers/list', array('user_id' => $_SESSION['request_vars']['user_id']));
		
		// if(isset($res->users)) {
		// 	foreach ($res->users as $value) {
		// 		$users .= $value->screen_name;
		// 	}
		// 	$res = $this->sysomos->get_tw_user_location($users);
		// }

		
		echo json_encode($res);
	}

	public function get_tw_locations()
	{
		$res = $this->twitterAPI->get('geo/search', array('query' => 'Egypt'));
		echo json_encode($res);
	}

	public function buffer_auth()
	{
		header("Location:" . $this->bufferAPI->getBufferAuthUrl());

	}

	public function delete_buffer_account()
	{
		$this->bufferAPI->removeBufferAccount();
	}

	public function get_buffer_profiles()
	{
		echo json_encode($this->bufferAPI->selectProfilesData());
	}

	public function get_all_buffer_posts($data)
	{
		$profile_id = $data->prID;
		echo json_encode($this->bufferAPI->getProfilePosts($profile_id, $_SESSION['buffer_access_token']));
	}

	public function get_buffer_updates($data)
	{
		$updates = [];

		foreach ($data as $value) {
			$updates[] = $this->bufferAPI->getProfilePostsData($value, $_SESSION['buffer_access_token']);
		}

		echo json_encode($updates);
	}

	public function add_buffer_post($data)
	{
		$result = $this->bufferAPI->addPost($data->bProfile, $_SESSION['buffer_access_token'], $data->bPost, $data->bMedia, $data->bPhoto);
		echo json_encode($result);
	}

	public function get_buffer_token()
	{
		$result = $this->bufferAPI->getAccessToken($_GET['code']);
		$_SESSION['buffer_access_token'] = $result->access_token;
		$userData = $this->bufferAPI->getUserData($_SESSION['buffer_access_token']);
		$this->set_buffer_user_data($userData);

	}

	public function set_buffer_user_data($userData)
	{
		$userInfo        = new stdClass();
		$userInfo->id    = $userData->id;
		$userInfo->twId  = $_SESSION['request_vars']['user_id'];
		$userInfo->name  = $userData->name;
		$userInfo->fname = explode(' ', $userInfo->name)[0];
		$userInfo->lname = explode(' ', $userInfo->name)[1];
		$userInfo->email = '';
		$userInfo->image = '';

		$results = $this->bufferAPI->getProfileData($_SESSION['buffer_access_token']);
		$data    = [];

		foreach ($results as  $result) {
			if ($userInfo->image == '' && $result->avatar != '') { $userInfo->image = $result->avatar; }

			$profileInfo = new stdClass();
			$profileInfo->uid     = $userInfo->id;
			$profileInfo->id      = $result->id;
			$profileInfo->service = $result->service;
			$profileInfo->sid     = $result->service_id;
			$profileInfo->status  = 1;
			$profileInfo->twid    = $userInfo->twId;

			array_push($data, $profileInfo);
		}

		$_SESSION['buffer_user'] = $userInfo;

		$user_data = $this->bufferAPI->checkUser(
			'buffer',
			$userInfo->twId,
			$userInfo->id,
			$userInfo->name,
			$userInfo->fname,
			$userInfo->lname,
			$userInfo->email,
			$userInfo->lang,
			$_SESSION['buffer_access_token'],
			'',
			$userInfo->image,
			'0',
			'0',
			'0'
		);

		$this->bufferAPI->checkProfiles($data);
		header("Location: $this->site_url");
	}

	public function add_user($data)
	{

		$data = json_decode(stripslashes($data['data']));
		$media_info = $this->twitterAPI->post('media/upload', array('media_data' => base64_encode(file_get_contents($data->image))), 1);
		$status_arr = array(
		    'status' => $data->status,
		    'media_ids' => $media_info->media_id,
		    'in_reply_to_status_id' => ''
		);
		
		$post_status = $this->twitterAPI->post('statuses/update', $status_arr);

		$this->updateStatusesCount($post_status);


		if (isset($post_status->id)) {
			 return array('message'=>'Tweet posted successfully.', 'error' => 0)  ;
		}else{
			 return json_encode( array('message'=>'Tweet posting error.Please try again', 'error' => 1) ) ;
		}
	}

	private function get_configs()
	{
		include $_SERVER['CONTEXT_DOCUMENT_ROOT'].'/wp-config.php';
		include $_SERVER['CONTEXT_DOCUMENT_ROOT'].'/wp-load.php';
	}

	private function set_consumer_data()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'options';
		$consumerdata = $wpdb->get_results("
						SELECT option_value FROM $table_name
						WHERE option_name 
						IN ('sm_twitter_consumer_key', 'sm_twitter_consumer_secret', 'sm_twitter_redirect_url')");

		$this->consumer_key    = $consumerdata[0]->option_value;
		$this->consumer_secret = $consumerdata[1]->option_value;
		$this->callback_url    = $consumerdata[2]->option_value;
	}

	private function set_oauth_data()
	{
		$this->user_id      	  = $_SESSION['request_vars']['user_id'];
		$this->oauth_token  	  = $_SESSION['request_vars']['oauth_token'];
		$this->oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];

		$this->site_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ki-publish-stream/';
	}

	public function removeBufferAccount()
	{
		$this->bufferAPI->removeBufferAccount();
	}

	public function vk_auth()
	{
		header('Location:' . $this->vk->getVkAuthUrl());
	}

	public function get_vk_user($data)
	{
		preg_match('/#access_token=([A-z0-9]+)&expires_in=([A-z0-9]+)&user_id=([A-z0-9]+)/', $data['vk_hash'], $data);
		$_SESSION['vk_access_token'] = $data[1];
		
		$user_info = $this->vk->get_user_info($data[3], $data[1])->response[0];
		
		$data = [];
		$data['oauth_provider'] = 'vk';
		$data['twitter_oauth'] = $_SESSION['request_vars']['user_id'];
		$data['oauth_uid'] = $user_info->id;
		$data['username'] = $user_info->first_name . ' ' . $user_info->last_name;
		$data['fname'] = $user_info->first_name;
		$data['lname'] = $user_info->last_name;
		$data['email'] = isset($user_info->email) ? $user_info->email : '';
		$data['locale'] = isset($user_info->lang) ? $user_info->lang : '';
		$data['oauth_token'] = $_SESSION['vk_access_token'];
		$data['oauth_secret'] = '';
		$data['picture'] = $user_info->photo;
		$data['created'] = date("Y-m-d H:i:s");
		$data['modified'] = date("Y-m-d H:i:s");
		$data['level'] = '0';
		$data['followers_count'] = '0';
		$data['friends_count'] = $user_info->common_count;
		$data['statuses_count'] = '0';
		$data['allow_public_chat'] = '0';
		$data['allow_notify_followers'] = '0';
		$data['disconnected'] = '0';

		$this->vk->checkUser($data);

		$_SESSION['vk_user'] = $data;
		header("Location: $this->site_url");
	}

	public function get_all_vk_posts($data)
	{
		$res = $this->vk->get_all_posts($_SESSION['vk_user']['oauth_uid']);
		echo json_encode($res->response->items);
	}

	public function add_vk_post($data)
	{
		$res = $this->vk->add_post($_SESSION['vk_user']['oauth_uid'], $data);

		echo json_encode($res);
	}

	public function hootsuite_auth()
	{
		header('Location:' . $this->hootsuiteAPI->getHoostsuiteAuthUrl());
	}

	public function get_hootsuite_access_token()
	{
		$access_token = $this->hootsuiteAPI->getAccessToken($_GET['code']);

		$_SESSION['hootsuite_access_token'] = $access_token->access_token;

		$this->get_hootsuite_user($access_token->access_token);
	}

	public function get_hootsuite_user($access_token)
	{
		$res = $this->hootsuiteAPI->getUserData($access_token);

		$user_data = $this->hootsuiteAPI->checkUser(
			'hootsuite',
			$_SESSION['request_vars']['user_id'],
			$res->data->id,
			$res->data->fullName,
			$res->data->fullName,
			'',
			$res->data->email,
			$res->data->language,
			$access_token,
			'',
			'',
			'0',
			'0',
			'0'
		);

		$_SESSION['hootsuite_user'] = $user_data;

		$profiles = $this->hootsuiteAPI->getProfileData();
		$profiles = $profiles->data;

		 
		header("Location: $this->site_url");
	}

	public function add_hootsuite_post($data)
	{
		$res = $this->hootsuiteAPI->addPost($data->hProfile, $data->hPost, $data->hPhoto);
		
		echo json_encode($res);
	}

	public function get_hootsuite_profiles($data)
	{
		echo json_encode($this->hootsuiteAPI->selectProfilesData());
	}

	public function get_all_hootsuite_posts($data)
	{
		echo json_encode($this->hootsuiteAPI->getProfilePosts($data->profileId));
	}

	public function remove_vk_account($data)
	{
		global $wpdb;

		$twId 	    = $_SESSION['request_vars']['user_id'];
		$table_name = $wpdb->prefix . 'sm_users';

		$wpdb->delete($table_name, array('oauth_provider' => 'vk', 'twitter_oauth' => $_SESSION['request_vars']['user_id']));

		unset($_SESSION['vk_access_token']);
		unset($_SESSION['vk_user']);

		echo json_encode(['success' => 'logout']);
	}

	public function remove_linkedin_account($data)
	{
		global $wpdb;

		$InId      = $_SESSION['request_vars']['user_id'];
		$table_name = $wpdb->prefix . 'sm_users';

		$res = $wpdb->delete($table_name, array('oauth_provider' => 'linkedin', 'twitter_oauth' => $_SESSION['request_vars']['user_id']));

		unset($_SESSION['linkedin_access_token']);
		unset($_SESSION['linkedin_user']);

		echo json_encode(['success' => 'logout']);
	}

	public function remove_hootsuite_account($data)
	{
		global $wpdb;

		$InId = $_SESSION['request_vars']['user_id'];

		$wpdb->delete($wpdb->prefix . 'sm_users', array('oauth_provider' => 'hootsuite', 'twitter_oauth' => $_SESSION['request_vars']['user_id']));
		$wpdb->delete($wpdb->prefix . 'hootsuite_profile_users', array('twitUserId' => $_SESSION['request_vars']['user_id']));

		unset($_SESSION['hootsuite_access_token']);
		unset($_SESSION['hootsuite_user']);

		echo json_encode(['success' => 'logout']);
	}

	public function add_tab($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_tabs';
		$uid   = $_SESSION['request_vars']['user_id'];

		$res = $wpdb->get_results("SELECT id FROM $table WHERE tab_name = '$data->tabName' AND user_id = '$uid'");

		if (!count($res)) {
			$insert = array('user_id' => $uid, 'tab_name' => $data->tabName, 'date' => time());
			$wpdb->insert($table, $insert);
			echo json_encode(['id' => $wpdb->insert_id, 'user_id' => $uid, 'tab_name' => $data->tabName, 'date' => time()]);
		} else {
			echo json_encode(['error' => 'You already create this tab!']);
		}
	}

	public function remove_tab($data)
	{
		global $wpdb;

		$uid = $_SESSION['request_vars']['user_id'];

		$wpdb->delete($wpdb->prefix . 'sm_tabs', array('user_id' => $uid, 'id' => $data));
		$wpdb->delete($wpdb->prefix . 'sm_stream', array('user_id' => $uid, 'tab_id' => $data));

		echo json_encode(['success' => 'delete']);
	}

	public function stream_action($data)
	{
		global $wpdb;
		$table = $wpdb->prefix.'sm_stream';
		$uid   = $_SESSION['request_vars']['user_id'];
		$res   = $wpdb->get_results("SELECT * FROM $table WHERE user_id = '$uid' AND tab_id = '$data->tabId'")[0];

		if($data->searchPhrase) {
			$this->updateSearchStream($data);
			$data->chatType = 'search';
		}elseif($data->rssUrls) {
			$this->updateRssStream($data);
			$data->chatType = 'rss';
		}

		if(!$res) {
			$insert = array(
				'user_id' => $uid,
				'stream_name' => $data->chatType,
				'tab_id' => $data->tabId,
				'chat_type' => $data->chatType,
				'handler_name' => '',
				'search_phrase' => $data->searchPhrase,
				'social_media_account' => $data->socialMediaAccount,
				'rss_urls' => $data->rssUrls,
				'date' => time(),
				'rss_feed_name' => $data->rssFeedName,
				'last_update' => time(),
				'team_id' => ''
			);

			$wpdb->insert($table, $insert);
		}else {
			$update = array(
				'stream_name' => trim($data->chatType) ? $data->chatType : $res->stream_name,
				'chat_type' => trim($data->chatType) ? $data->chatType : $res->chat_type,
				'search_phrase' => trim($data->searchPhrase) ? $data->searchPhrase : $res->search_phrase,
				'social_media_account' => trim($data->socialMediaAccount) ? $data->socialMediaAccount : $res->social_media_account,
				'rss_urls' => trim($data->rssUrls) ? $data->rssUrls : $res->rss_urls,
				'rss_feed_name' => trim($data->rssFeedName) ? $data->rssFeedName : $res->rss_feed_name,
				'last_update' => time()
			);			

			$wpdb->update($table, $update, array('id' => $res->id));
		}
	}

	private function updateSearchStream($data)
	{
		global $wpdb;
		
		$wpdb->delete("{$wpdb->prefix}sm_google_search", array('tab_id' => $data->tabId));
		$res = $this->google->search($data->searchPhrase, 1, 10, ['sort' => 'date']);

		foreach ($res->results as $value) {
			$res = $wpdb->insert(
				"{$wpdb->prefix}sm_google_search",
				[
					'keyword' => $data->searchPhrase,
					'title' => $value->title,
					'snippet' => isset(explode(' ... ', $value->snippet)[1]) ? explode(' ... ', $value->snippet)[1] : explode(' ... ', $value->snippet)[0],
					'link' => $value->link,
					'image' => $value->image,
					'created_at' => time(),
					'tab_id' => $data->tabId
				]
			);
		}
	}

	private function updateRssStream($data)
	{
		global $wpdb;

		$wpdb->delete("{$wpdb->prefix}sm_rss_articles", array('tab_id' => $data->tabId));
		$res = $this->getRss($data->rssUrls);

		foreach ($res['entry'] as $value) {
			$link = array_values($value['link'])[0]['href'];
			$info = $wpdb->insert(
				"{$wpdb->prefix}sm_rss_articles",
				[
					'tab_id' => $data->tabId,
					'title' => $res['title'],
					'feed_title' => $value['title'],
					'link' => $link,
					'description' => $value['content'],
					'rss_link' => $data->rssUrls,
					'added_time' => implode(' ', explode('T', substr($value['updated'], 0, (strlen($value['updated']) - 1)))),
					'created_at' => time()
				]
			);
		}
		
	}

	public function add_team($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_team';
		$uid   = $_SESSION['request_vars']['user_id'];

		$res = $wpdb->get_results("SELECT id FROM $table WHERE team_name = '$data->team_name' AND team_manager = '$uid'");

		if(!count($res)) {
			$insert = array('team_name' => $data->team_name, 'team_manager' => $uid, 'created_at' => time());
			$wpdb->insert($table, $insert);
			echo json_encode(['id' => $wpdb->insert_id, 'team_manager' => $uid, 'team_name' => $data->team_name, 'created_at' => time()]);
		} else {
			echo json_encode(['error' => 'You already create this team!']);
		}
	}

	public function get_teammates($data)
	{
		global $wpdb;



		$table = $wpdb->prefix.'sm_teammate';
		$uid   = $_SESSION['request_vars']['user_id'];
		$role  = [
			['name' => 'all', 'value' => 'All'],
			['name' => 'repost', 'value' => 'Retweet post'],
			['name' => 'send', 'value' => 'Respond to post'],
			['name' => 'message', 'value' => 'Respond to message']
		];
		$res = $wpdb->get_results("SELECT * FROM $table WHERE manager_id = '$uid' AND team_id = '$data'");
		echo json_encode(['res' => $res, 'role' => $role]);
	}

	public function remove_teammate($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_teammate';
		$uid   = $_SESSION['request_vars']['user_id'];

		$res = $wpdb->delete($table, array('id' => $data->tmId));
		echo json_encode($res);
	}

	public function find_user($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_users';

		$res = $wpdb->get_results("SELECT * FROM $table WHERE username LIKE '%$data->sQuery%' LIMIT 10");
		echo json_encode($res);
	}

	public function insert_user_team($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_users';

		$user = $wpdb->get_results("SELECT * FROM $table WHERE username = '$data->userName'")[0];

		if($user) {
			$table = $wpdb->prefix.'sm_teammate';
			$uid   = $_SESSION['request_vars']['user_id'];
			$res   = $wpdb->get_results("SELECT * FROM $table WHERE user_id = '$user->oauth_uid' AND manager_id = '$uid' AND team_id = '$data->teamId'")[0];

			if($res) {
				echo json_encode(['info'=>'This user already on your team!']);
			}else {
				$insert = array('user_id' => $user->oauth_uid, 'user_name' => $user->username, 'team_id' => $data->teamId, 'manager_id' => $uid, 'role' =>'all');
				$wpdb->insert($table, $insert);
				echo json_encode(['info'=>'User successfully added!']);
			}
		}else{
			echo json_encode(['info'=>'This user is not registered in our database!']);
		}
	}

	public function add_tab_team($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_stream';

		$wpdb->update($table, array('statuses_count'=>$statuses_count), array('oauth_provider'=>'twitter','oauth_uid' => $user_id ));
	}

	public function get_manager_teams($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_team';
		$uid   = $_SESSION['request_vars']['user_id'];

		$res = $wpdb->get_results("SELECT * FROM $table WHERE team_name LIKE '%$data->sQuery%' AND team_manager = '$uid' LIMIT 10");

		echo json_encode($res);
	}

	public function assign_team($data)
	{
		global $wpdb;

		$table = $wpdb->prefix.'sm_team';
		$uid   = $_SESSION['request_vars']['user_id'];

		$res = $wpdb->get_results("SELECT * FROM $table WHERE team_name = '$data->team' AND team_manager = '$uid'")[0];

		if($res) {
			$table = $wpdb->prefix . 'sm_stream';
			$wpdb->update($table, array('team_id' => $res->id), array('tab_id' => $data->tabId));
			echo json_encode(['id' => $res->id, 'success' => 'Team successfully assign!']);
		} else {
			echo json_encode(['error' => 'There is no team to assign!']);
		}
	}

	public function change_user_role($data)
	{
		global $wpdb;

		$table = $wpdb->prefix . 'sm_teammate';

		var_dump($wpdb->update($table, array('role' => $data->role), array('id' => $data->teammateId)));
	}

	public function get_tab_data($data, $api = 'user')
	{
		global $wpdb;

		$table           = $wpdb->prefix . 'sm_stream';
		$res             = $wpdb->get_results("SELECT search_phrase, rss_urls, chat_type, id, handler_name FROM $table WHERE tab_id = '$data->tabId'");
		$data->stream_id = $res[0]->id;
		$searchPhrase    = $res[0]->search_phrase;

		if(isset($res[0]->chat_type)) {
			$res = $res[0]->chat_type;
			$handler_name = $res[0]->handler_name;
		}
		if($api == 'user') {
			$api = $this->twitterAPI;
		}
		switch($res) {
			case 'home':
				$res = $api->get('statuses/home_timeline', array('count' => 200));
				$res = ['result' => $res, 'type' => 'home'];
			break;
			case 'inbox':
				if(!isset($data->assignments)) {
					$res = $api->get('direct_messages/events/list');
					$res = isset($res->events) ? $this->inbox_schema($res->events) : [];
					$res = ['result' => $res, 'type' => 'inbox'];
				}else {
					$res = ['result' => $this->get_assigned_messages($data, $api), 'type' => 'inbox'];
				}
			break;
			case 'my_tweets':
				$res = $api->get('statuses/user_timeline', array('count' => 200));
				$res = ['result' => $res, 'type' => 'home'];
			break;
			case 'my_mentions':
				$res = $api->get('statuses/mentions_timeline');
				$res = ['result' => $res, 'type' => 'home'];
			break;
			case 'user_likes':
				$res = $api->get('favorites/list', array('screen_name' => $_SESSION['request_vars']['screen_name'], 'count' => 200));
				$res = ['result' => $res, 'type' => 'home'];
			break;
			case 'search':
				$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sm_google_search WHERE tab_id = '$data->tabId'");
				$res[0]->created_at = date('Y-m-d H:i:s');
				$res = ['result' => $res, 'type' => 'search'];
			break;
			case 'rss':
				$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sm_rss_articles WHERE tab_id = '$data->tabId'");
				$res[0]->created_at = date('Y-m-d H:i:s');
				$res = ['result' => $res, 'type' => 'rss'];
			break;
			default:
				$res = ['result' => []];
			break;
		}
		echo json_encode($res);
	}

	private function get_assigned_messages($data, $api)
	{
		global $wpdb;

		$messages = $wpdb->get_results("
			SELECT 
				message_id,
				manager_id
			FROM {$wpdb->prefix}sm_assignments
			WHERE stream_id = '$data->stream_id'
			AND user_id = '{$_SESSION['request_vars']['user_id']}'
		");

		if($messages) {
			$events = [];
			foreach ($messages as $value) {
				$res        			= $api->get('direct_messages/events/show', ['id' => $value->message_id]);
				if(isset($res->event)) {
					$res->event->manager_id = $value->manager_id;
					$events[]   			= $res->event;
				}
			}
			
			return $this->inbox_schema($events, true);
		}

		return [];
	}

	private function inbox_schema($data, $status = false)
	{
		$res = [];

		foreach ($data as $value) {
			$item = [];
			$item['message_id']   = $value->id;
			$item['time']         = $value->created_timestamp;
			$item['sender_id']    = $value->message_create->sender_id;
			$item['recipient_id'] = $value->message_create->target->recipient_id;
			$item['text']	      = $value->message_create->message_data->text;

			$manager_id = $status ? $value->manager_id : $_SESSION['request_vars']['user_id'];

			if($value->message_create->sender_id == $manager_id) {
				$item['sender'] = true;
				$user_id        = $value->message_create->target->recipient_id;
			}else {
				$item['sender'] = false;
				$user_id        = $value->message_create->sender_id;
			}
			if(isset($res[$user_id])) {
				if($item['sender_id'] == $res[$user_id][0]['sender_id']) {
					$item['sender_name']     = $res[$user_id][0]['sender_name'];
					$item['sender_image']    = $res[$user_id][0]['sender_image'];
					$item['recipient_name']  = $res[$user_id][0]['recipient_name'];
					$item['recipient_image'] = $res[$user_id][0]['recipient_image'];
				}else {
					$item['sender_name']     = $res[$user_id][0]['recipient_name'];
					$item['sender_image']    = $res[$user_id][0]['recipient_image'];
					$item['recipient_name']  = $res[$user_id][0]['sender_name'];
					$item['recipient_image'] = $res[$user_id][0]['sender_image'];
				}
				$res[$user_id][] = $item;
			}else {
				$this->get_dm_users($item);

				$res[$user_id]   = [];
				$res[$user_id][] = $item;
			}
		}
		return $res;
	}

	private function get_dm_users(&$data)
	{
		$res = $this->twitterAPI->get('users/show', array('user_id' => $data['recipient_id']));

		$data['recipient_name']  = $res->screen_name;
		$data['recipient_image'] = $res->profile_image_url_https;

		$res = $this->twitterAPI->get('users/show', array('user_id' => $data['sender_id']));

		$data['sender_name']     = $res->screen_name;
		$data['sender_image']    = $res->profile_image_url_https;
	}

	private function get_assigment_data($data)
	{
		global $wpdb;

		$uId = $_SESSION['request_vars']['user_id'];

		if($data->tabType == 'stream') {
			$res = $wpdb->get_results("
				SELECT 
					st.id,
					st.tab_id,
					st.chat_type,
					te.team_name,
					te.created_at,
					tm.role,
					ta.tab_name,
					u.username AS manager 
				FROM {$wpdb->prefix}sm_stream st
					LEFT JOIN {$wpdb->prefix}sm_users u ON st.user_id = u.oauth_uid
					LEFT JOIN {$wpdb->prefix}sm_team te ON st.team_id = te.id
					LEFT JOIN {$wpdb->prefix}sm_teammate tm ON te.id = tm.team_id
					LEFT JOIN {$wpdb->prefix}sm_tabs ta ON st.tab_id = ta.id
				WHERE tm.user_id = '$uId'
			");

			echo json_encode($res);
		}else {
			$res = $wpdb->get_results("
				SELECT 
					te.team_name,
					te.created_at,
					u.username,
					u.fname,
					u.picture
				FROM {$wpdb->prefix}sm_team te
					LEFT JOIN {$wpdb->prefix}sm_teammate tm ON te.id = tm.team_id
					LEFT JOIN {$wpdb->prefix}sm_users u ON te.team_manager = u.oauth_uid
				WHERE tm.user_id = '$uId'
			");
			echo json_encode(['result' => $res, 'type' => 'teams']);
		}
	}

	private function get_assign_stream_data($data)
	{
		global $wpdb;

		$tabId 		  = new stdClass();
		$tabId->tabId = $data->tab_id;
		if(isset($data->assignments)){
			$tabId->assignments = true;
		}

		$res = $wpdb->get_results("
			SELECT
				ag.*,
				u.oauth_token,
				u.oauth_secret,
				tmm.role
			FROM {$wpdb->prefix}sm_assignments ag
				LEFT JOIN {$wpdb->prefix}sm_users u ON ag.manager_id = u.oauth_uid
				LEFT JOIN {$wpdb->prefix}sm_stream st ON ag.stream_id = st.id
				LEFT JOIN {$wpdb->prefix}sm_teammate tmm ON st.team_id = tmm.team_id
			WHERE ag.stream_id = '$data->id'
			AND ag.user_id = '{$_SESSION['request_vars']['user_id']}'
		");

		$api = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $res[0]->oauth_token, $res[0]->oauth_secret);

		$this->get_assign_posts($res, $api);
	}

	private function get_assign_posts($data, $api)
	{
		$tweets = ['result' => [], 'type' => 'post'];

		foreach ($data as $value) {
			$res = $api->get('statuses/show', array('id' => $value->message_id));
			$res->role = $value->role;
			if (!isset($res->errors)) {
				$tweets['result'][] = $res;
			}
		}

		echo json_encode($tweets);
	}

	private function get_assign_teammates($data)
	{
		global $wpdb;

		switch ($data->type) {
			case 'message':
				$cond = "('message', 'all')";
			break;
			case 'post':
				$cond = "('repost', 'send', 'all')";
			break;
		}

		$res = $wpdb->get_results("
			SELECT
				user_id,
				user_name
			FROM {$wpdb->prefix}sm_teammate
			WHERE team_id = '$data->team_id'
			AND role IN $cond
		");

		echo json_encode($res);

	}

	private function assign_message($data) {
		global $wpdb;

		$res = $wpdb->get_results("
			SELECT
				*
			FROM {$wpdb->prefix}sm_assignments
			WHERE user_id = '$data->user_id'
			AND message_id = '$data->message_id'
			AND manager_id = '{$_SESSION['request_vars']['user_id']}'
		");

		if(!count($res)) {
			$res = $wpdb->get_results("
				SELECT
					id
				FROM {$wpdb->prefix}sm_stream
				WHERE tab_id = '$data->tab_id'
			")[0];

			$data = (array)($data);
			$data['manager_id'] = $_SESSION['request_vars']['user_id'];
			$data['stream_id']  = $res->id;
			unset($data['tab_id']);
			$res = $wpdb->insert("{$wpdb->prefix}sm_assignments", $data);

			$res = $res ? ['success' => 'add'] : ['error' => $res];
		}else {
			$res = ['error' => 'Already exist!'];
		}

		echo json_encode($res);
	}

	private function check_func_type($data)
	{
		echo "<pre>";
		switch($data->type) {
			case 'like':
				$res = $this->twitterAPI->post('favorites/create', array('id' => (int)$data->postId));
			break;
			case 'retweet':
				$res = $this->twitterAPI->post("statuses/retweet", array('id' => (int)$data->postId));
			break;
		}
		print_r($res);die;
	}

	private function add_schedule_post($data)
	{
		global $wpdb;

		switch ($data->media) {
			case 'twitter':
				$data->uId  = md5("{$_SESSION['request_vars']['user_id']}{$_SERVER['SERVER_NAME']}");
				$data->pUId = $_SESSION['request_vars']['user_id'];
			break;
			case 'linkedin':
				$data->uId  = md5("{$_SESSION['linkedin_user']['oauth_uid']}{$_SERVER['SERVER_NAME']}");
				$data->pUId = $_SESSION['linkedin_user']['oauth_uid'];
			break;
		}

		$data->tPhoto = preg_replace('/&/', '%26', $data->tPhoto);
		$data->tPost  = preg_replace('/&/', '%26', $data->tPost);

		echo $this->post($data);
	}
	
}

$actions = new Actions();
