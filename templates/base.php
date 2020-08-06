<?php 
global $wpdb;
if($_SESSION['status'] != "verified") {
    echo '<script>location.href="/ki-publish-login";</script>';
} else if($_GET['logout']) {
    $table_name = $wpdb->prefix . 'buffer_profile_users';
    $wpdb->delete($table_name, array('twitUserId' => $_SESSION['request_vars']['user_id']));
    $table_name = $wpdb->prefix . 'hootsuite_profile_users';
    $wpdb->delete($table_name, array('twitUserId' => $_SESSION['request_vars']['user_id']));
    session_unset();
    echo '<script>location.href="/ki-publish-login";</script>';
}

$string = $_SERVER['REQUEST_URI'];
$pattern = '/[a-z-]+\/$/i';
$replacement = '';
$site_url = preg_replace($pattern, $replacement, $string);

$table_name = $wpdb->prefix . "sm_users";

if (isset($_SESSION['linkedin_user']['user_id']) && !empty($_SESSION['linkedin_user']['user_id']))
{
    $oauth_uid =  $_SESSION['linkedin_user']['user_id'];
    $linkedin_user = $wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = 'linkedin' AND oauth_uid = '$oauth_uid'");
}

if (isset($_SESSION['vk_user']['oauth_uid']) && !empty($_SESSION['vk_user']['oauth_uid']))
{
    $oauth_uid = $_SESSION['vk_user']['oauth_uid'];
    $vk_user   = $wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = 'vk' AND oauth_uid = '$oauth_uid'");
}

if (isset($_SESSION['hootsuite_user']['oauth_uid']) && !empty($_SESSION['hootsuite_user']['oauth_uid']))
{
    $oauth_uid      = $_SESSION['hootsuite_user']['oauth_uid'];
    $hootsuite_user = $wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = 'hootsuite' AND oauth_uid = '$oauth_uid'");
}

if (isset( $_SESSION['buffer_user']->id) && !empty( $_SESSION['buffer_user']->id))
{
    $oauth_uid =  $_SESSION['buffer_user']->id;
    $result = $wpdb->get_results("SELECT * FROM $table_name WHERE oauth_provider = 'buffer' AND oauth_uid = '$oauth_uid'");

    if ( !empty($result) && isset($result[0]) ) {
        $buffer_user = $result[0];
    }
}


if (isset($hootsuite_user) && !empty($hootsuite_user)) {
    
    $hootsuite_table = $wpdb->prefix.'hootsuite_profile_users';

    $oauth_uid = $_SESSION['hootsuite_user']['oauth_uid'];
    $tw_uid    = $_SESSION['request_vars']['user_id'];

    $hootsuite_service_name = $wpdb->get_results("SELECT * FROM $hootsuite_table WHERE userId = '$oauth_uid' AND twitUserId = '$tw_uid'");
}

if (isset($buffer_user) && !empty($buffer_user)) {
    
    $buffer_table = $wpdb->prefix.'buffer_profile_users';

    $oauth_uid = $_SESSION['buffer_user']->id;
    $tw_uid    = $_SESSION['request_vars']['user_id'];
    $buffer_service_name = $wpdb->get_results("SELECT * FROM $buffer_table WHERE userId = '$oauth_uid' AND twitUserId = '$tw_uid'");
}

if (isset($_SESSION['linkedin_user'])) {
    $linkedin_user = $_SESSION['linkedin_user'];
}

?>




<input type="hidden" data-url="<?=BASE_URL?>" id="base_url">
<div class='sm-header-links'>
    <link data-id="ki-publish" rel='stylesheet' href='<?=BASE_URL?>assets/css/pages.css'>
    <link data-id="ki-publish" rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
</div>
<div class='sm-header'>
    
    <div class='sm-header-left'>
        
        <img src='<?=BASE_URL?>images/kiDesignLogo/ki-design-logo.png' alt=''>
        <span class='header-text'><?=$_SESSION['request_vars']['screen_name']?></span>

        <div id="twitter-user" data-modal="openModal" data-type="twitter">
            <img src="<?=$_SESSION['request_vars']['picture']?>" alt='' class='top-avatar-img' id="get-twitter-profile">
            <img src='<?=BASE_URL?>images/logo-7.png' alt='' class='top-social-icon'>
        </div>
    
        <?php 
            
            if (isset($_SESSION['linkedin_user']['oauth_token']) && !empty($_SESSION['linkedin_user']['oauth_token']) && !empty($linkedin_user) ) {
                ?>
                    <div id="linkedin-user" data-type="linkedin" class="openModal" data-modal="openModal">
                        <img src="<?=$_SESSION['linkedin_user']['picture']?>" alt='' class='top-avatar-img' id="get-linkedin-profile">
                        <img src='<?=BASE_URL?>images/logo-15.png' alt='' class='top-social-icon'>
                        <i class="fa fa-remove cm-avatar-remove" id="remove_linkedin_account"></i>
                    </div>

                <?php
            }


            if ( !empty($_SESSION['buffer_user']) && !empty($buffer_user) ) {
                ?>
                     <div id="buffer-user" data-type="buffer" class="openModal" data-modal="openModal">
                        <img src="<?=$_SESSION['buffer_user']->image?>" alt='' class='top-avatar-img' id="get-buffer-profile">
                        <img src='<?=BASE_URL?>images/buffer-logo.png' alt='' class='top-social-icon'>
                        <i class="fa fa-remove cm-avatar-remove" id="remove_buffer_account"></i>
                    </div>
                <?php
            }

            if (isset($_SESSION['hootsuite_user']['oauth_token']) && !empty($_SESSION['hootsuite_user']['oauth_token']) && !empty($hootsuite_user) ) {
                ?>
                    <div id="hootsuite-user" data-type="hootsuite" class="openModal" data-modal="openModal">
                        <img src="<?=$_SESSION['hootsuite_user']['picture']?>" alt='' class='top-avatar-img' id="get-hootsuite-profile">
                        <img src='<?=BASE_URL?>images/hootsuite.png' alt='hootsuite' class='top-social-icon'>
                        <i class="fa fa-remove cm-avatar-remove" id="remove_hootsuite_account"></i>
                    </div>

                <?php
            }

            if (isset($_SESSION['vk_user']['oauth_token']) && !empty($_SESSION['vk_user']['oauth_token']) && !empty($vk_user) ) {
                ?>
                    <div id="vk-user" data-type="vk" class="openModal" data-modal="openModal">
                        <img src="<?=$_SESSION['vk_user']['picture']?>" alt='' class='top-avatar-img' id="get-vk-profile">
                        <img src='<?=BASE_URL?>images/VK_logo2.png' alt='' class='top-social-icon'>
                        <i class="fa fa-remove cm-avatar-remove" id="remove_vk_account"></i>
                    </div>

                <?php
            }

        ?>



        <div>
            <img src='<?=BASE_URL?>images/buttons/015__circle_plus.png' class='top-avatar openModal' data-modal='openModal' data-type='login'>
            <button class='btn-info openModal' data-modal='openModal' data-type='post'>Compose</button>

            <?php 

                if ( !empty($_SESSION['buffer_user']) && !empty($buffer_user) ) {
                    ?>
                        <button style="margin-left: 5px" class="btn-info openModal" data-modal='openModal' data-type='addBufferPost'>
                            Buffer compose
                        </button>
                    <?php
                }

            ?>

            <?php 

                if ( !empty($_SESSION['hootsuite_user']) && !empty($hootsuite_user) ) {
                    ?>
                        <button style="margin-left: 5px" class="btn-info openModal" data-modal='openModal' data-type='addHootsuitePost'>
                            Hootsuite compose
                        </button>
                    <?php
                }

            ?>

            
        </div>

    </div>

    <div class='sm-header-right'>
        <i class='fa fa-bell-o dropdown-toggle' data-toggle='dropdownSettings' aria-hidden='true' style='color:#bbbdbf;font-size: 22px'></i>
        <button class='cm-border-btn dropdown-toggle' data-toggle='dropdownReminder' aria-expanded='false'>
            <img src='<?=BASE_URL?>images/buttons/082-setting-cog@2x.png'>
        </button>
    </div>
</div>
<ul class='settings' id="dropdownReminder">
    <li id="testAPI">Setting</li>
    <li onclick="window.location.href = '/ki-publish-stream/?logout=true'">Sign out</li>
</ul>
<ul class='reminder' id="dropdownSettings">
    <li>Add Reminder</li>
</ul>
<ul class='sidebar'>
    <li>
        <a href="<?=$site_url?>ki-publish-stream">
            <img src='<?=BASE_URL?>images/sideBar/group-2.png' alt='Stream'>
            <span>Stream</span>
        </a>
    </li>
    <li>
        <a href="<?=$site_url?>ki-publish-calendar">
            <img src='<?=BASE_URL?>images/sideBar/noun-148353-cc.png' alt='Calendar'>
            <span>Calendar</span>
        </a>
    </li>
    <li>
        <a href="<?=$site_url?>ki-publish-users">
            <img src='<?=BASE_URL?>images/sideBar/103-user.png' alt='User'>
            <span>User</span>
        </a>
    </li>
    <li>
        <a href="<?=$site_url?>ki-publish-assignments">
            <img src='<?=BASE_URL?>images/sideBar/noun-148353-cc.png' alt='Assignments'>
            <span>Assignments</span>
        </a>
    </li>
    <li>
        <a href="<?=$site_url?>ki-publish-smartcontent">
            <img src='<?=BASE_URL?>images/sideBar/noun-148353-cc.png' alt='Auto publish'>
            <span>Auto publish</span>
        </a>
    </li>
    <li>
        <a href="<?=$site_url?>ki-publish-followers">
            <img src='<?=BASE_URL?>images/sideBar/noun-148353-cc.png' alt='Logs'>
            <span>Followers</span>
        </a>
    </li>
    <li>
        <a>
            <img src='<?=BASE_URL?>images/sideBar/noun-148353-cc.png' alt='Media'>
            <span>Media</span>
        </a>
    </li>
</ul>

<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/content.js"></script>



