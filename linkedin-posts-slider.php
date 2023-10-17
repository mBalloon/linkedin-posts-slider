<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Include the new files
require_once plugin_dir_path(__FILE__) . 'widget-registration.php';
require_once plugin_dir_path(__FILE__) . 'options-page.php';
require_once plugin_dir_path(__FILE__) . 'db-table-creation.php';
require_once plugin_dir_path(__FILE__) . 'cron-event.php';
require_once plugin_dir_path(__FILE__) . 'admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'ajax-actions.php';
require_once plugin_dir_path(__FILE__) . 'linkedin-posts-syncing.php';

function register_slider_widget($widgets_manager)
{
	require_once(__DIR__ . '/widgets/slider-widget.php');

	$widgets_manager->register(new \Elementor_Slider_Widget());
}
add_action('elementor/widgets/register', 'register_slider_widget');

// Hook into cron event
add_action('linkedin_posts_sync_event', 'linkedin_posts_sync');

function linkedin_posts_slider_admin_table_page()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Check if a delete action is submitted
	if (isset($_POST['delete']) && isset($_POST['id'])) {
		$wpdb->delete($table_name, array('id' => $_POST['id']), array('%d'));
	}

	// Fetch all rows
	$rows = $wpdb->get_results("SELECT * FROM $table_name");

	echo '<div class="wrap">';
	echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
	echo '<style>
	    table {
	        width: 100%;
	        border-collapse: collapse;
	    }
	    th, td {
	        border: 1px solid #ddd;
	        padding: 8px;
	    }
	    th {
	        padding-top: 12px;
	        padding-bottom: 12px;
	        text-align: left;
	        background-color: #4CAF50;
	        color: white;
	    }
	</style>';
	echo '<table>';
	echo '<tr><th>ID</th><th>URL</th><th>Author</th><th>Age</th><th>Copy</th><th>Synced</th><th>Published</th><th>Action</th><th>Sync</th></tr>';
	foreach ($rows as $row) {
		echo '<tr>';
		echo '<td>' . esc_html($row->id) . '</td>';
		echo '<td>' . esc_html($row->url) . '</td>';
		echo '<td>' . esc_html($row->author) . '</td>';
		echo '<td>' . esc_html($row->age) . '</td>';
		echo '<td>' . esc_html($row->copy) . '</td>';
		echo '<td>' . esc_html($row->synced) . '</td>';
		echo '<td>' . esc_html($row->published) . '</td>';
		echo '<td>';
		echo '<button class="publish-button" data-id="' . esc_attr($row->id) . '" data-published="' . esc_attr($row->published) . '" onclick="publishButtonClicked(this)">' . ($row->published ? 'Unpublish' : 'Publish') . '</button>';
		echo '</td>';
		echo '<td>';
		echo '<form action="" method="post">';
		echo '<input type="hidden" name="id" value="' . esc_attr($row->id) . '">';
		submit_button('Delete', 'delete', 'delete', false);
		echo '</form>';
		echo '</td>';
		echo '<td>';
		echo '<button class="sync-button" data-url="' . esc_attr($row->url) . '" onclick="syncButtonClicked(this)">Sync</button>';
		echo '</td>';
		echo '<script>';
		echo 'function syncButtonClicked(buttonElement) {';
		echo 'var button = jQuery(buttonElement);';
		echo 'var url = button.data("url");';
		echo 'button.text("...");';
		echo 'jQuery.ajax({';
		echo 'url: "https://scrape-js.onrender.com/",';
		echo 'type: "POST",';
		//echo 'timeout: 30000,';
		echo 'data: JSON.stringify({';
		echo 'url: url,';
		echo 'secretKey: "PzoiJcU2ocfOeWj6AQQdkQ"';
		echo '}),';
		echo 'contentType: "application/json",';
		echo 'success: function(response) {';
		echo 'button.text("Sync");';
		echo 'var data;';
		echo 'data = response;';
		echo 'jQuery.ajax({';
		echo 'url: ajaxurl,';
		echo 'type: "POST",';
		echo 'data: {';
		echo 'action: "update_row",';
		echo 'data: JSON.stringify({ url: data.url, author: data.author, username: data.username, age: data.age, profilePicture: data.profilePicture, copy: data.copy, images: data.images, reactions: data.reactions, comments: data.comments, synced: true })';
		echo '},';
		echo 'success: function(response) {';
		echo 'button.text("Sync");';
		echo '},';
		echo 'error: function(jqXHR, textStatus, errorThrown) {';
		echo 'button.text("Sync");';
		echo '}';
		echo '});';
		echo '},';
		echo 'error: function(jqXHR, textStatus, errorThrown) {';
		echo 'button.text("Sync");';
		echo '}';
		echo '});';
		echo '}';

		echo 'function publishButtonClicked(buttonElement) {';
		echo 'var button = jQuery(buttonElement);';
		echo 'var id = button.data("id");';
		echo 'var published = button.data("published");';
		echo 'button.text("...");';
		echo 'jQuery.ajax({';
		echo 'url: ajaxurl,';
		echo 'type: "POST",';
		echo 'data: {';
		echo 'action: "publish_unpublish",';
		echo 'id: id,';
		echo 'published: !published';
		echo '},';
		echo 'success: function(response) {';
		echo 'button.text(published ? "Publish" : "Unpublish");';
		echo 'button.data("published", !published);';
		echo '},';
		echo 'error: function(jqXHR, textStatus, errorThrown) {';
		echo 'console.log("Error: " + textStatus + ", " + errorThrown);';
		echo 'button.text(published ? "Unpublish" : "Publish");';
		echo '}';
		echo '});';
		echo '}';
		echo '</script>';
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}