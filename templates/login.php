<div class='sm-header-links'>
    <link data-id="ki-publish" rel='stylesheet' href='<?=BASE_URL?>assets/css/pages.css'>
    <link data-id="ki-publish" rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
</div>

<?php
if(isset($_SESSION['status'])) {
    echo '<script>location.href="/ki-publish-stream";</script>';
} 
$string = $_SERVER['REQUEST_URI'];
$pattern = '/[a-z-]+\/$/i';
$replacement = '';
$site_url = preg_replace($pattern, $replacement, $string);
?>

<div class="login-container">
	<div class="login-box">
		<div class="login-first-part">
			<img src="<?=BASE_URL?>images/twitter-logo-2012.png" width="120" alt="">
		</div>
		<div class="login-second-part">
			<div class="login-title">
				<a href="<?=BASE_URL?>includes/actions/actions.php?twitter=true">Sign in with Twitter</a>
			</div>
			<div class="login-description">
				<p>By signing in you agree that you are older then 13 and that you agree to our <a href="https://kidesign.click/terms-of-use/" target="_blank">terms of use</a></p>
			</div>
		</div>
	</div>
</div>
<input type="hidden" data-url="<?=BASE_URL?>" id="base_url">


<script data-id="ki-publish" src='<?=BASE_URL?>assets/js/content.js'></script>