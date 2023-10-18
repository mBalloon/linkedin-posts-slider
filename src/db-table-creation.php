<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function linkedin_posts_slider_create_table()
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'linkedin_posts';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        url text NOT NULL,
        author text NOT NULL,
        username text NOT NULL,
        age text NOT NULL,
        profilePicture text NOT NULL,
        copy text NOT NULL,
        images text NOT NULL,
        reactions int NOT NULL,
        comments int NOT NULL,
        synced boolean NOT NULL,
        published boolean NOT NULL,
        post_order int NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
