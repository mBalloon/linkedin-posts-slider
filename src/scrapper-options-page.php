<?php
/*
Plugin Name: LinkedIn Posts Slider
Description: A WordPress plugin to display LinkedIn posts in a slider with admin options.
*/

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
	exit;
}

// Add custom admin menu for the plugin settings page
function linkedin_posts_slider_admin_menu_setup()
{
	add_menu_page(
		__('LinkedIn Posts Slider Settings', 'linkedin-posts-slider'),
		__('LinkedIn Slider', 'linkedin-posts-slider'),
		'manage_options',
		'linkedin_scrapper_settings',
		'display_scrapper_options_form',
		'dashicons-linkedin'
	);
}
add_action('admin_menu', 'linkedin_posts_slider_admin_menu_setup');

// Display settings form in the admin page
function display_scrapper_options_form()
{
	global $wpdb;
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';
	$posts_table = $wpdb->prefix . 'linkedin_posts';

	// Fetch statistics for stats cards
	if ($wpdb->get_var("SHOW TABLES LIKE '$posts_table'") == $posts_table) {
		// Table exists, fetch stats
		$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table");
		$published_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE published = 1");
		$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE synced = 1");
	} else {
		// Table doesn't exist, stats are zero
		$total_posts = $published_posts = $synced_posts = 0;
	}

	// Fetch settings from the database
	$results = $wpdb->get_results("SELECT * FROM $settings_table", ARRAY_A);

	$settings = array(
		'linkedin_company_url' => '',
		'linkedin_slider_open_link' => 0,
		'linkedin_update_frequency' => 0,
		'linkedin_scrapper_endpoint' => '',
		'linkedin_scrapper_last_update' => '', // Assume this setting exists in your settings table
		'linkedin_scrapper_status' => '', // Assume this setting exists in your settings table
	);

	foreach ($results as $row) {
		if (array_key_exists($row['setting_name'], $settings)) {
			$settings[$row['setting_name']] = $row['setting_value'];
		}
	}

	$last_update = $settings['linkedin_scrapper_last_update'];
	$status = $settings['linkedin_scrapper_status'];

	// Pass the statistics and settings to the form
	include(plugin_dir_path(__FILE__) . 'form.php');
}

// Enqueue styles for the admin page
function linkedin_posts_slider_enqueue_styles()
{
	wp_enqueue_style('linkedin-posts-slider-admin', plugins_url('linkedin-posts-slider-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'linkedin_posts_slider_enqueue_styles');

// Handle form submission
function handle_scrapper_settings_form_submission()
{
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}

	check_admin_referer('update_scrapper_settings');

	global $wpdb;
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';

	$settings = [
		'linkedin_company_url' => isset($_POST['linkedin_company_url']) ? esc_url_raw($_POST['linkedin_company_url']) : '',
		'linkedin_slider_open_link' => isset($_POST['linkedin_slider_open_link']) ? (int) $_POST['linkedin_slider_open_link'] : 0,
		'linkedin_update_frequency' => isset($_POST['linkedin_update_frequency']) ? max(0, (int) $_POST['linkedin_update_frequency']) : 0,
		'linkedin_scrapper_endpoint' => isset($_POST['linkedin_scrapper_endpoint']) ? esc_url_raw($_POST['linkedin_scrapper_endpoint']) : '',
	];

	foreach ($settings as $name => $value) {
		$wpdb->replace(
			$settings_table,
			['setting_name' => $name, 'setting_value' => $value],
			['%s', '%s']
		);
	}

	add_settings_error('linkedin_scrapper_settings', 'settings_updated', __('Settings updated successfully'), 'updated');
	set_transient('settings_errors', get_settings_errors(), 30);

	wp_safe_redirect(admin_url('admin.php?page=linkedin_scrapper_settings'));
	exit;
}
add_action('admin_post_update_scrapper_settings', 'handle_scrapper_settings_form_submission');

// Create tables on plugin activation
function linkedin_posts_slider_activation()
{
	global $wpdb;
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';

	if ($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") != $settings_table) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $settings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            setting_name varchar(255) NOT NULL,
            setting_value varchar(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
register_activation_hook(__FILE__, 'linkedin_posts_slider_activation');
