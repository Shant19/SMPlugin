<?php
namespace SocialMedia;

use SocialMedia\database\Database;
use SocialMedia\template\Template;

class SmPublish {

  public $dir;
  public $db;
  public $template;
	public $url;

	public function start() 
	{
		$this->db       = new Database();
		$this->template = new Template($this->dir);
    $this->url      = explode('/', $_SERVER['REQUEST_URI']);

    $this->db->createTables();

    add_shortcode('base', array( $this, 'shortcodeTemplatesBase' ));
    add_shortcode('users', array( $this, 'shortcodeTemplatesUsers' ));
    add_shortcode('stream', array( $this, 'shortcodeTemplatesStream' ));
    add_shortcode('twlogin', array( $this, 'shortcodeTemplatesLogin' ));
    add_shortcode('calendar', array( $this, 'shortcodeTemplatesCalendar' ));
    add_shortcode('followers', array( $this, 'shortcodeTemplatesFollowers' ));
    add_shortcode('assignments', array( $this, 'shortcodeTemplatesAssignments' ));
    add_shortcode('smartcontent', array( $this, 'shortcodeTemplatesSmartContent' ));

    add_action('admin_menu', array( $this, 'add_menu' ));
    add_action('init', array( $this, 'createPage' ));
    add_action('init', array( $this, 'sess_start' ));
  }

    public function add_menu () 
    {
    	add_menu_page( 'KI-Publish', 'KI-Publish', 'edit_others_posts', 'ki_publish', array( $this, 'menu' ), plugins_url( 'ki-publish/images/icon.png' ), 6 );
    }

    public function sess_start () 
    {
      if (!session_id())
        session_start();
    }

    public function menu () 
    { 
    	$this->menu_header();
    	$this->menu_content();
    }

    public function menu_header() 
    {
    	return include $this->dir . 'templates/admin/header.php';
    }

    public function menu_content() 
    {
        include $this->dir . 'templates/admin/content.php';
    }

    public function menu_modals() 
    { 
    	return include $this->dir . 'templates/modals.php';
    }

    public function createPage()
    {
        global $wpdb;

        $result  = $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'ki-publish-stream'", 'ARRAY_A');
        $userId  = get_current_user_id();

        if (is_null($result)) {
            $content = '<!-- wp:shortcode -->[base][stream]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-stream',
              'post_name'     => 'ki-publish-stream',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );

            $content = '<!-- wp:shortcode -->[base][calendar]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-calendar',
              'post_name'     => 'ki-publish-calendar',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );

            $content = '<!-- wp:shortcode -->[base][users]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-users',
              'post_name'     => 'ki-publish-users',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );

            $content = '<!-- wp:shortcode -->[base][assignments]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-assignments',
              'post_name'     => 'ki-publish-assignments',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );

            $content = '<!-- wp:shortcode -->[base][smartcontent]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-smartcontent',
              'post_name'     => 'ki-publish-smartcontent',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );

            $content = '<!-- wp:shortcode -->[twlogin]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-login',
              'post_name'     => 'ki-publish-login',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );

            $content = '<!-- wp:shortcode -->[base][followers]<!-- /wp:shortcode -->';
            $my_post = array(
              'post_title'    => 'ki-publish-followers',
              'post_name'     => 'ki-publish-followers',
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
              'post_content'  => $content,
              'post_author'   => $userId,
              'post_category' => array(8, 39)
            );

            // Insert the post into the database
            wp_insert_post( $my_post );
        }
    }

    public function shortcodeTemplatesBase()
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createBase();
        }
    }

    public function shortcodeTemplatesStream()
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createStream();
        }   
    }

    public function shortcodeTemplatesCalendar() 
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createCalendar();
        }
    }

    public function shortcodeTemplatesUsers() 
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createUsers();
        }
    }

    public function shortcodeTemplatesAssignments()
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createAssignments();
        }   
    }

    public function shortcodeTemplatesSmartContent()
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createSmartContent();
        } 
    }

    public function shortcodeTemplatesLogin()
    {
        if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createLogin();
        }  
    }

    public function shortcodeTemplatesFollowers()
    {
       if (!array_search('wp-admin', $this->url)) {
            $this->template->dir = $this->dir;
            return  $this->template->createFollowers();
        }   
    }
}

