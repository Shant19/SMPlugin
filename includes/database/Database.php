<?php 
namespace SocialMedia\database;

class Database {

	public function createTables() 
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "sm_users";

		$result = $wpdb->get_results("SELECT * 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = '" . $wpdb->dbname . "' 
                AND  TABLE_NAME = '" . $table_name . "'
		");

		if (!count($result)) {
			// die('error');

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
			     `id` int(11) NOT NULL AUTO_INCREMENT,
			     `oauth_provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `twitter_oauth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			     `oauth_uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `fname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `lname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `locale` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `oauth_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `oauth_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
			     `created` datetime NOT NULL,
			     `modified` datetime NOT NULL,
			     `level` int(2) NOT NULL DEFAULT 0,
			     `followers_count` int(11) NOT NULL DEFAULT 0,
			     `friends_count` int(11) NOT NULL DEFAULT 0,
			     `statuses_count` int(11) NOT NULL DEFAULT 0,
			     `allow_public_chat` int(1) NOT NULL DEFAULT 0,
			     `allow_notify_followers` int(1) NOT NULL DEFAULT 0,
			     `disconnected` int(1) NOT NULL DEFAULT 0,
			     PRIMARY KEY (`id`)
			    ) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
			);

			$table_name = $wpdb->prefix . "buffer_profile_users";

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `userId` varchar(255) DEFAULT NULL,
				  `profileId` varchar(255) DEFAULT NULL,
				  `serviceName` varchar(255) DEFAULT NULL,
				  `serviceId` varchar(255) DEFAULT NULL,
				  `status` varchar(255) DEFAULT NULL,
				  `twitUserId` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "hootsuite_profile_users"; 

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `userId` varchar(255) DEFAULT NULL,
				  `profileId` varchar(255) DEFAULT NULL,
				  `serviceName` varchar(255) DEFAULT NULL,
				  `serviceId` varchar(255) DEFAULT NULL,
				  `profileUname` varchar(255) DEFAULT NULL,
				  `twitUserId` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "sm_tabs";

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` varchar(255) DEFAULT NULL,
				  `tab_name` varchar(255) DEFAULT NULL,
				  `date` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "sm_stream"; 

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query("
				CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` bigint(20) NOT NULL,
				  `stream_name` varchar(100) DEFAULT NULL,
				  `tab_id` int(11) NOT NULL,
				  `chat_type` varchar(100) DEFAULT NULL,
				  `handler_name` varchar(100) DEFAULT NULL,
				  `search_phrase` varchar(100) DEFAULT NULL,
				  `social_media_account` varchar(100) DEFAULT NULL,
				  `rss_urls` varchar(1000) DEFAULT NULL,
				  `date` datetime DEFAULT NULL,
				  `rss_feed_name` varchar(500) DEFAULT NULL,
				  `last_update` varchar(255) DEFAULT NULL,
				  `team_id` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
			");

			$table_name = $wpdb->prefix . "sm_team"; 

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `team_name` varchar(255) NOT NULL,
				  `team_manager` varchar(255) NOT NULL,
				  `created_at` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "sm_teammate";

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` varchar(255) NOT NULL,
				  `manager_id` varchar(255) NOT NULL,
				  `user_name` varchar(255) DEFAULT NULL,
				  `team_id` int(11) NOT NULL,
				  `role` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "sm_assignments";

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` varchar(255) DEFAULT NULL,
				  `message_id` varchar(255) DEFAULT NULL,
				  `manager_id` varchar(255) DEFAULT NULL,
				  `stream_id` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "sm_google_search";

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `keyword` varchar(255) DEFAULT NULL,
				  `title` varchar(255) DEFAULT NULL,
				  `snippet` varchar(255) DEFAULT NULL,
				  `link` varchar(255) DEFAULT NULL,
				  `image` varchar(255) DEFAULT NULL,
				  `created_at` varchar(255) DEFAULT NULL,
				  `tab_id` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$table_name = $wpdb->prefix . "sm_rss_articles";

			$wpdb->query("DROP TABLE IF EXISTS `$table_name`");

			$user_table_query = $wpdb->query(
				"CREATE TABLE `$table_name` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `tab_id` int(11) DEFAULT NULL,
				  `title` varchar(255) DEFAULT NULL,
				  `feed_title` varchar(255) DEFAULT NULL,
				  `link` varchar(255) DEFAULT NULL,
				  `description` text DEFAULT NULL,
				  `rss_link` varchar(255) DEFAULT NULL,
				  `added_time` varchar(255) DEFAULT NULL,
				  `created_at` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;"
			);

		}
	}
}

