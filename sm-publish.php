<?php 
/*
 * Plugin Name: KI-Publish
 * Plugin URI: http://privacypolicy.guru/
 * Description: Social media publishing application plugin.
 * Version: 1.0.0
 * Author: Wael Hassan
 * Author URI: http://waelhassan.com
 * Text Domain: 
 *
 */


namespace SocialMedia;

include 'vendor/autoload.php';

define('BASE_URL', plugin_dir_url(__FILE__));

$sm = new SmPublish();
$sm->dir = plugin_dir_path(__FILE__);
register_activation_hook( __FILE__, "$sm->createPage" );
$sm->start();