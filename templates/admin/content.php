<?php 

global $wpdb;
$table_name = $wpdb->prefix . "options";

$twitter_api_data   = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name IN('sm_twitter_consumer_key', 'sm_twitter_consumer_secret', 'sm_twitter_redirect_url')");
$linkedin_api_data  = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name IN('sm_linkedin_consumer_key', 'sm_linkedin_consumer_secret', 'sm_linkedin_redirect_url')");
$buffer_api_data    = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name IN('sm_buffer_consumer_key', 'sm_buffer_consumer_secret', 'sm_buffer_redirect_url')");
$vk_api_data        = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name IN('sm_vk_consumer_key', 'sm_vk_consumer_secret', 'sm_vk_redirect_url', 'sm_vk_app_id')");
$hootsuite_api_data = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name IN('sm_hootsuite_consumer_key', 'sm_hootsuite_consumer_secret', 'sm_hootsuite_redirect_url')");

$twk = $twitter_api_data != null ? $twitter_api_data[0]->option_value : '';
$tws = $twitter_api_data != null ? $twitter_api_data[1]->option_value : '';
$twr = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/ki-publish/includes/actions/actions.php';

$lwk = $linkedin_api_data != null ? $linkedin_api_data[0]->option_value : '';
$lws = $linkedin_api_data != null ? $linkedin_api_data[1]->option_value : '';
$lwr = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/ki-publish/includes/actions/actions.php';

$bwk = $buffer_api_data != null ? $buffer_api_data[0]->option_value : '';
$bws = $buffer_api_data != null ? $buffer_api_data[1]->option_value : '';
$bwr = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/ki-publish/includes/actions/actions.php';

$vwk = $vk_api_data != null ? $vk_api_data[1]->option_value : '';
$vws = $vk_api_data != null ? $vk_api_data[2]->option_value : '';
$vwa = $vk_api_data != null ? $vk_api_data[0]->option_value : '';
$vwr = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/ki-publish/includes/actions/actions.php';

$hwk = $hootsuite_api_data != null ? $hootsuite_api_data[0]->option_value : '';
$hws = $hootsuite_api_data != null ? $hootsuite_api_data[1]->option_value : '';
$hwr = 'https://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/ki-publish/includes/actions/actions.php';



// echo "<pre>";
// var_export($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/ki-publish/includes/actions/actions.php');die;
// 'wp-content/plugins/ki-publish/includes/actions/actions.php';
if (!empty($_POST) ) {


	if ($_POST['form'] == 'twitter') {
		$twk = $sm_twitter_consumer_key    = $_POST['sm_twitter_consumer_key'];
		$tws = $sm_twitter_consumer_secret = $_POST['sm_twitter_consumer_secret'];
		$twr = $sm_twitter_redirect_url	   = $_POST['sm_twitter_redirect_url'];

		

		if(!empty($_POST['sm_twitter_consumer_key']) && !empty($_POST['sm_twitter_consumer_secret']) && !empty($_POST['sm_twitter_redirect_url'])){

			if (empty($twitter_api_data)) {
				$wpdb->query("
					INSERT INTO $table_name (option_name, option_value, autoload)
					VALUES ('sm_twitter_consumer_key', '$sm_twitter_consumer_key', 'yes'),
						   ('sm_twitter_consumer_secret', '$sm_twitter_consumer_secret', 'yes'),
						   ('sm_twitter_redirect_url', '$sm_twitter_redirect_url', 'yes')
				");

			} else {
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_twitter_consumer_key' 
					WHERE option_name='sm_twitter_consumer_key'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_twitter_consumer_secret' 
					WHERE option_name='sm_twitter_consumer_secret'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_twitter_redirect_url' 
					WHERE option_name='sm_twitter_redirect_url'
				");
				echo '<div class="notice notice-success is-dismissible">
	            	  	<p>Data successfully updated</p>
	         		  </div>';

			}
		} else {
			echo '<div class="notice notice-error is-dismissible">
		  			<p>Empty data</p>
			  	  </div>';
		}	


	}

	else if( $_POST['form'] == 'linkedin'){

		$lwk = $sm_linkedin_consumer_key    = $_POST['sm_linkedin_consumer_key'];
		$lws = $sm_linkedin_consumer_secret = $_POST['sm_linkedin_consumer_secret'];
		$lwr = $sm_linkedin_redirect_url	= $_POST['sm_linkedin_redirect_url'];

		if(!empty($_POST['sm_linkedin_consumer_key']) && !empty($_POST['sm_linkedin_consumer_secret']) && !empty($_POST['sm_linkedin_redirect_url'])){

			if (empty($linkedin_api_data)) {
				$wpdb->query("
					INSERT INTO $table_name (option_name, option_value, autoload)
					VALUES ('sm_linkedin_consumer_key', '$sm_linkedin_consumer_key', 'yes'),
						   ('sm_linkedin_consumer_secret', '$sm_linkedin_consumer_secret', 'yes'),
						   ('sm_linkedin_redirect_url', '$sm_linkedin_redirect_url', 'yes')
				");

			} else {
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_linkedin_consumer_key' 
					WHERE option_name='sm_linkedin_consumer_key'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_linkedin_consumer_secret' 
					WHERE option_name='sm_linkedin_consumer_secret'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_linkedin_redirect_url' 
					WHERE option_name='sm_linkedin_redirect_url'
				");
				echo '<div class="notice notice-success is-dismissible">
	            	  	<p>Data successfully updated</p>
	         		  </div>';

			}
		} else {
			echo '<div class="notice notice-error is-dismissible">
		  			<p>Empty data</p>
			  	  </div>';
		}	


	}




	else if( $_POST['form'] == 'buffer'){

		$bwk = $sm_buffer_consumer_key    = $_POST['sm_buffer_consumer_key'];
		$bws = $sm_buffer_consumer_secret = $_POST['sm_buffer_consumer_secret'];
		$bwr = $sm_buffer_redirect_url	= $_POST['sm_buffer_redirect_url'];

		if(!empty($_POST['sm_buffer_consumer_key']) && !empty($_POST['sm_buffer_consumer_secret']) && !empty($_POST['sm_buffer_redirect_url'])){

			if (empty($buffer_api_data)) {
				$wpdb->query("
					INSERT INTO $table_name (option_name, option_value, autoload)
					VALUES ('sm_buffer_consumer_key', '$sm_buffer_consumer_key', 'yes'),
						   ('sm_buffer_consumer_secret', '$sm_buffer_consumer_secret', 'yes'),
						   ('sm_buffer_redirect_url', '$sm_buffer_redirect_url', 'yes')
				");

			} else {
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_buffer_consumer_key' 
					WHERE option_name='sm_buffer_consumer_key'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_buffer_consumer_secret' 
					WHERE option_name='sm_buffer_consumer_secret'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_buffer_redirect_url' 
					WHERE option_name='sm_buffer_redirect_url'
				");
				echo '<div class="notice notice-success is-dismissible">
	            	  	<p>Data successfully updated</p>
	         		  </div>';

			}
		} else {
			echo '<div class="notice notice-error is-dismissible">
		  			<p>Empty data</p>
			  	  </div>';
		}	
	}

	else if( $_POST['form'] == 'hootsuite'){

		$bwk = $sm_hootsuite_consumer_key    = $_POST['sm_hootsuite_consumer_key'];
		$bws = $sm_hootsuite_consumer_secret = $_POST['sm_hootsuite_consumer_secret'];
		$bwr = $sm_hootsuite_redirect_url	 = $_POST['sm_hootsuite_redirect_url'];

		if(!empty($_POST['sm_hootsuite_consumer_key']) && !empty($_POST['sm_hootsuite_consumer_secret']) && !empty($_POST['sm_hootsuite_redirect_url'])){

			if (empty($hootsuite_api_data)) {
				$wpdb->query("
					INSERT INTO $table_name (option_name, option_value, autoload)
					VALUES ('sm_hootsuite_consumer_key', '$sm_hootsuite_consumer_key', 'yes'),
						   ('sm_hootsuite_consumer_secret', '$sm_hootsuite_consumer_secret', 'yes'),
						   ('sm_hootsuite_redirect_url', '$sm_hootsuite_redirect_url', 'yes')
				");

			} else {
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_hootsuite_consumer_key' 
					WHERE option_name='sm_hootsuite_consumer_key'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_hootsuite_consumer_secret' 
					WHERE option_name='sm_hootsuite_consumer_secret'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_hootsuite_redirect_url' 
					WHERE option_name='sm_hootsuite_redirect_url'
				");
				echo '<div class="notice notice-success is-dismissible">
	            	  	<p>Data successfully updated</p>
	         		  </div>';

			}
		} else {
			echo '<div class="notice notice-error is-dismissible">
		  			<p>Empty data</p>
			  	  </div>';
		}	
	}

	else if( $_POST['form'] == 'vk'){

		$vwk = $sm_vk_consumer_key    = $_POST['sm_vk_consumer_key'];
		$vws = $sm_vk_consumer_secret = $_POST['sm_vk_consumer_secret'];
		$vwa = $sm_vk_app_id 	      = $_POST['sm_vk_app_id'];
		$vwr = $sm_vk_redirect_url	  = $_POST['sm_vk_redirect_url'];

		if(!empty($_POST['sm_vk_consumer_key']) && !empty($_POST['sm_vk_consumer_secret']) && !empty($_POST['sm_vk_redirect_url']) && !empty($_POST['sm_vk_app_id'])){

			if (empty($vk_api_data)) {
				$wpdb->query("
					INSERT INTO $table_name (option_name, option_value, autoload)
					VALUES ('sm_vk_consumer_key', '$sm_vk_consumer_key', 'yes'),
						   ('sm_vk_consumer_secret', '$sm_vk_consumer_secret', 'yes'),
						   ('sm_vk_redirect_url', '$sm_vk_redirect_url', 'yes'),
						   ('sm_vk_app_id', '$sm_vk_app_id', 'yes')
				");

			} else {
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_vk_consumer_key' 
					WHERE option_name='sm_vk_consumer_key'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_vk_consumer_secret' 
					WHERE option_name='sm_vk_consumer_secret'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_vk_redirect_url' 
					WHERE option_name='sm_vk_redirect_url'
				");
				$wpdb->query("
					UPDATE $table_name 
					SET option_value='$sm_vk_app_id' 
					WHERE option_name='sm_vk_app_id'
				");
				echo '<div class="notice notice-success is-dismissible">
	            	  	<p>Data successfully updated</p>
	         		  </div>';

			}
		} else {
			echo '<div class="notice notice-error is-dismissible">
		  			<p>Empty data</p>
			  	  </div>';
		}	
	}
}
?>

<h2 class="sm-twitter-credentials">Twitter API credentials</h2>
<form class="sm-menu-container" method="post" action>
	<input type="hidden" name="form" value="twitter">
	<label for="consumer_key">Consumer key:</label>
	<input type="text" id="consumer_key" name="sm_twitter_consumer_key" value="<?=$twk?>">
	<label for="consumer_secret">Consumer secret:</label>
	<input type="password" id="consumer_secret" name="sm_twitter_consumer_secret" value="<?=$tws?>">
	<label for="redirect_url">Redirect URL:</label>
	<input type="text" id="redirect_url" name="sm_twitter_redirect_url" value="<?=$twr?>" readonly>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>

<h2 class="sm-linkedin-credentials">Linkedin API credentials</h2>
<form class="sm-menu-container" method="post" action>
	<input type="hidden" name="form" value="linkedin">
	<label for="consumer_linkedin_key">Client ID:</label>
	<input type="text" id="consumer_linkedin_key" name="sm_linkedin_consumer_key" value="<?=$lwk?>">
	<label for="consumer_linkedin_secret">Client secret:</label>
	<input type="password" id="consumer_linkedin_secret" name="sm_linkedin_consumer_secret" value="<?=$lws?>">
	<label for="redirect_linkedin_url">Redirect URL:</label>
	<input type="text" id="redirect_linkedin_url" name="sm_linkedin_redirect_url" value="<?=$lwr?>" readonly>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>

<h2 class="sm-buffer-credentials">Buffer API credentials</h2>
<form class="sm-menu-container" method="post" action>
	<input type="hidden" name="form" value="buffer">
	<label for="consumer_buffer_key">Client ID:</label>
	<input type="text" id="consumer_buffer_key" name="sm_buffer_consumer_key" value="<?=$bwk?>">
	<label for="consumer_buffer_secret">Client secret:</label>
	<input type="password" id="consumer_buffer_secret" name="sm_buffer_consumer_secret" value="<?=$bws?>">
	<label for="redirect_buffer_url">Redirect URL:</label>
	<input type="text" id="redirect_buffer_url" name="sm_buffer_redirect_url" value="<?=$bwr?>" readonly>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>

<h2 class="sm-buffer-credentials">VK API credentials</h2>
<form class="sm-menu-container" method="post" action>
	<input type="hidden" name="form" value="vk">
	<label for="consumer_vk_key">Client ID:</label>
	<input type="text" id="consumer_vk_key" name="sm_vk_consumer_key" value="<?=$vwk?>">
	<label for="consumer_vk_secret">Client secret:</label>
	<input type="password" id="consumer_vk_secret" name="sm_vk_consumer_secret" value="<?=$vws?>">
	<label for="vk__app_id">App ID:</label>
	<input type="text" id="vk__app_id" name="sm_vk_app_id" value="<?=$vwa?>">
	<label for="redirect_vk_url">Redirect URL:</label>
	<input type="text" id="redirect_vk_url" name="sm_vk_redirect_url" value="<?=$vwr?>" readonly>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>

<h2 class="sm-buffer-credentials">Hootsuite API credentials</h2>
<form class="sm-menu-container" method="post" action>
	<input type="hidden" name="form" value="hootsuite">
	<label for="consumer_vk_key">Client ID:</label>
	<input type="text" id="consumer_vk_key" name="sm_hootsuite_consumer_key" value="<?=$hwk?>">
	<label for="consumer_hootsuite_secret">Client secret:</label>
	<input type="password" id="consumer_hootsuite_secret" name="sm_hootsuite_consumer_secret" value="<?=$hws?>">
	<label for="redirect_hootsuite_url">Redirect URL:</label>
	<input type="text" id="redirect_hootsuite_url" name="sm_hootsuite_redirect_url" value="<?=$hwr?>" readonly>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
	</p>
</form>
