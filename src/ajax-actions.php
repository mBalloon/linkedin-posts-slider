<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function update_row()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Check if necessary data is set in the AJAX request
	if (!isset($_POST['data'])) {
		wp_send_json_error('No data received');
		return;
	}

	// Decode the JSON data
	$data = json_decode(stripslashes($_POST['data']), true);

	// Sanitize and validate the data
	$url = sanitize_text_field($data['url']);
	$author = sanitize_text_field($data['author']);
	$username = sanitize_text_field($data['username']);
	$age = sanitize_text_field($data['age']);
	$profilePicture = esc_url_raw($data['profilePicture']);
	$copy = sanitize_text_field($data['copy']);
	$images = array_map('esc_url_raw', $data['images']);
	$reactions = intval($data['reactions']);
	$comments = intval($data['comments']);
	$synced = boolval($data['synced']);

	// Update the row in the database
	$result = $wpdb->update(
		$table_name,
		array(
			'url' => $url,
			'author' => $author,
			'username' => $username,
			'age' => $age,
			'profilePicture' => $profilePicture,
			'copy' => $copy,
			'images' => json_encode($images),
			'reactions' => $reactions,
			'comments' => $comments,
			'synced' => $synced
		),
		array('url' => $url),
		array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d'),
		array('%s')
	);

	if ($result === false) {
		wp_send_json_error('Failed to update row');
	} else {
		wp_send_json_success('Row updated successfully');
	}
}
add_action('wp_ajax_update_row', 'update_row');

function publish_unpublish()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Check if necessary data is set in the AJAX request
	if (!isset($_POST['id']) || !isset($_POST['published'])) {
		wp_send_json_error('No data received');
		return;
	}

	// Sanitize and validate the data
	$id = intval($_POST['id']);
	$published = boolval($_POST['published']);

	// Update the row in the database
	$result = $wpdb->update(
		$table_name,
		array('published' => $published),
		array('id' => $id),
		array('%d'),
		array('%d')
	);

	if ($result === false) {
		wp_send_json_error('Failed to update row');
	} else {
		wp_send_json_success('Row updated successfully');
	}
}
add_action('wp_ajax_publish_unpublish', 'publish_unpublish');

function get_linkedin_posts()
{
	global $wpdb;

	// Check if the synced and published values are set to true
	$synced = true;
	$published = true;

	// Fetch rows from the linkedin_posts table
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE synced = %d AND published = %d",
			$synced,
			$published
		),
		ARRAY_A
	);

	// Decode the JSON-encoded images array
	foreach ($rows as &$row) {
		$row['images'] = json_decode($row['images']);
	}

	// Send the data back to the frontend
	wp_send_json_success($rows);
}

add_action('wp_ajax_get_linkedin_posts', 'get_linkedin_posts');
add_action('wp_ajax_nopriv_get_linkedin_posts', 'get_linkedin_posts');




function update_post_order()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$post_order = $_POST['post_order'];

	foreach ($post_order as $index => $postId) {
		$wpdb->update(
			$table_name,
			['post_order' => $index],
			['id' => $postId],
			['%d'],
			['%d']
		);
	}

	wp_send_json_success();
}
add_action('wp_ajax_update_post_order', 'update_post_order');
