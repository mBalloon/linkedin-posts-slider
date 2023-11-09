<?php
/*
Plugin Name: LinkedIn Posts Slider
Description: A WordPress plugin to display LinkedIn posts in a slider with admin options.
*/

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
	exit;
}

function display_scrapper_options_form()
{
	global $wpdb;
	$posts_table = $wpdb->prefix . 'linkedin_posts';

	// Fetch total number of posts
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table");

	// Fetch number of posts where 'synced' is true
	$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE synced = 1");

	// Fetch settings from WordPress options
	$settings = array(
		'linkedin_company_url' => get_option('linkedin_company_url', ''),
		'linkedin_slider_open_link' => get_option('linkedin_slider_open_link', 0),
		'linkedin_update_frequency' => get_option('linkedin_update_frequency', 0),
		'linkedin_scrapper_endpoint' => get_option('linkedin_scrapper_endpoint', ''),
		'linkedin_scrapper_last_update' => get_option('linkedin_scrapper_last_update', ''), // This setting should exist in your options
		'linkedin_scrapper_status' => get_option('linkedin_scrapper_status', ''), // This setting should exist in your options
	);

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

	$settings = [
		'linkedin_company_url' => sanitize_text_field($_POST['linkedin_company_url']),
		'linkedin_slider_open_link' => isset($_POST['linkedin_slider_open_link']) ? 1 : 0,
		'linkedin_update_frequency' => sanitize_text_field($_POST['linkedin_update_frequency']),
		'linkedin_scrapper_endpoint' => sanitize_text_field($_POST['linkedin_scrapper_endpoint']),
	];

	foreach ($settings as $name => $value) {
		update_option($name, $value);
	}

	add_settings_error('linkedin_scrapper_settings', 'settings_updated', __('Settings updated successfully'), 'updated');
	set_transient('settings_errors', get_settings_errors(), 30);

	wp_safe_redirect(admin_url('admin.php?page=linkedin_scrapper_settings'));
	exit;
}
add_action('admin_post_update_scrapper_settings', 'handle_scrapper_settings_form_submission');
