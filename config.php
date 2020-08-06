<?php 

global $wpdb;

$table_name        = $wpdb->prefix . "options";
$linkedin_api_data = $wpdb->get_results("SELECT * FROM $table_name WHERE option_name IN('sm_linkedin_consumer_key', 'sm_linkedin_consumer_secret', 'sm_linkedin_redirect_url')");

$LINKEDIN_API_KEY = $linkedin_api_data != null ? $linkedin_api_data[0]->option_value : '';
$LINKEDIN_API_SECRET = $linkedin_api_data != null ? $linkedin_api_data[1]->option_value : '';
$LINKEDIN_REDIRECT_URI = $linkedin_api_data != null ? $linkedin_api_data[2]->option_value : '';
$LINKEDIN_SCOPE = 'r_liteprofile r_emailaddress w_share rw_company_admin';



