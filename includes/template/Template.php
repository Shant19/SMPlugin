<?php 
namespace SocialMedia\template;

class Template {

	public $dir;

	public function __construct($dir)
	{
		$this->dir = $dir;
	}

	public function createBase()
	{	
		require $this->dir . 'templates/base.php';
		require $this->dir . 'templates/modals.php';	
	}

	public function createStream()
	{
		global $wpdb;

		$bTable = $wpdb->prefix.'sm_tabs';
		$sTable = $wpdb->prefix.'sm_stream';
		$uid    = $_SESSION['request_vars']['user_id'];
		$tabs   = $wpdb->get_results("
			SELECT $bTable.*,
			$sTable.stream_name, 
			$sTable.tab_id, 
			$sTable.chat_type, 
			$sTable.rss_urls, 
			$sTable.rss_feed_name,
			$sTable.team_id
			FROM $bTable 
			LEFT JOIN $sTable 
			ON $bTable.id=$sTable.tab_id 
			WHERE $bTable.user_id = '$uid'
		");

		$dTabs = array_slice($tabs, 7);
		$tabs  = array_slice($tabs, 0, 7);

		require $this->dir . 'templates/stream.php';
	}

	public function createCalendar()
	{
		require $this->dir . 'templates/calendar.php';
	}

	public function createUsers()
	{
		global $wpdb;

		$bTable = $wpdb->prefix.'sm_team';
		$sTable = $wpdb->prefix.'sm_teammate';
		$uid    = $_SESSION['request_vars']['user_id'];
		$teams  = $wpdb->get_results("
			SELECT $bTable.*,
			$sTable.user_id,
			$sTable.manager_id,
			$sTable.user_name,
			$sTable.team_id
			FROM $bTable
			LEFT JOIN $sTable
			ON $bTable.id=$sTable.team_id
			WHERE $bTable.team_manager = '$uid'
			GROUP BY $bTable.id
		");
		
		$dTabs = array_slice($teams, 7);
		$teams = array_slice($teams, 0, 7);
		
		require $this->dir . 'templates/users.php';
	}

	public function createAssignments()
	{
		require $this->dir . 'templates/assignments.php';	
	}

	public function createSmartContent()
	{
		require $this->dir . 'templates/smart-content.php';	
	}

	public function createLogin()
	{
		require $this->dir . 'templates/login.php';	
	}

	public function createFollowers()
	{
		require $this->dir . 'templates/followers.php';	
	}
}