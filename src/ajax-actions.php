<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}



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
		array('published' => !$published),
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
			"SELECT * FROM {$table_name} WHERE synced = %d AND published = %d ORDER BY post_order ASC",
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


function delete_post()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Check if necessary data is set in the AJAX request
	if (!isset($_POST['id'])) {
		wp_send_json_error('No data received');
		return;
	}

	// Sanitize and validate the data
	$id = intval($_POST['id']);

	// Delete the row from the database
	$result = $wpdb->delete(
		$table_name,
		array('id' => $id),
		array('%d')
	);

	if ($result === false) {
		wp_send_json_error('Failed to delete row');
	} else {
		wp_send_json_success('Row deleted successfully');
	}
}
add_action('wp_ajax_delete_post', 'delete_post');


// Function to process scraped data
function process_scraped_data($data_array)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$new_posts_added = false;

	foreach ($data_array as $data) {
		if ($data['company-name'][0] !== 'Alpine Laser') {
			continue;  // Skip to the next object if company-name is not "Alpine Laser"
		}

		$urn = $data['URN'][0];
		$reactions = intval($data['reactions'][0]);  // Sanitize as integer
		$comments = sanitize_text_field($data['comments'][0]);  // Sanitize as text
		$age = sanitize_text_field($data['age'][0]);  // Sanitize as text

		// Check for existing post by urn
		$existing_post = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_name WHERE urn = %s",
			$urn
		));

		if ($existing_post) {
			// Update existing post
			$wpdb->update(
				$table_name,
				array(
					'reactions' => $reactions,
					'comments' => $comments,
					'age' => $age + " •"
				),
				array('urn' => $urn)
			);
		} else {
			// Create new post
			$wpdb->insert(
				$table_name,
				array(
					'urn' => $urn,
					'synced' => false,
					'published' => false
				)
			);
			$new_posts_added = true;  // Set flag if a new post is added
		}
	}
	if ($new_posts_added) {
		sync_unsynced_posts();  // Call sync_unsynced_posts if any new posts were added
	}
}

function sync_unsynced_posts()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';
	while (true) {
		// Find first unsynced post
		$unsynced_post = $wpdb->get_row("SELECT * FROM $table_name WHERE synced = false LIMIT 1");

		if (!$unsynced_post) {
			// Break if no unsynced posts found
			break;
		}

		$urn = $unsynced_post->urn;
		$request_url = "http://localhost:3001/scrape";
		$post_url = 'https://www.linkedin.com/feed/update/' . $urn;

		// Prepare request data
		$request_data = array(
			"secret_key" => "test",
			"url" => $post_url,
			"postSelector" => 'section[class="mb-3"]',
			"selectorsArray" => ['section[class="mb-3"] article', 'time', 'a[data-tracking-control-name="public_post_feed-actor-image"] img', 'p[data-test-id="main-feed-activity-card_commentary"]', 'span[data-test-id="social-actionsreaction-count"]', 'a[data-id="social-actions_comments"]', 'ul[data-test-id="feed-images-content"] img'],
			"attributesArray" => ["data-attributed-urn", "innerText", "src", "innerText", "innerText", "innerText", "src"],
			"namesArray" => ["URN", "age", "profilePicture", "post_text", "reactions", "comments", "images"]
			// ... rest of the data ...
		);

		// Make POST request
		$response = wp_remote_post($request_url, array('body' => json_encode($request_data), 'headers' => array('Content-Type' => 'application/json')));

		if (is_wp_error($response)) {
			// Handle error
			error_log($response->get_error_message());
			continue;  // Skip to next iteration
		}

		$response_data = json_decode(wp_remote_retrieve_body($response), true);
		$post_data = $response_data['results'][0];

		// Sanitize and prepare updated post data
		$updated_data = array(
			'author' => 'Alpine Laser',
			'username' => 'alpine-laser',
			'age' => sanitize_text_field($post_data['age'][0]) + " •",
			'profilePicture' => esc_url_raw($post_data['profilePicture'][0]),
			'post_text' => sanitize_textarea_field($post_data['post_text'][0]),
			'images' => array_filter($post_data['images'], 'strlen'),  // Remove any empty strings
			'reactions' => absint($post_data['reactions'][0]),  // Convert to absolute integer
			'comments' => sanitize_text_field($post_data['comments'][0]),
			'synced' => true,
			'published' => false,
			'post_order' => $unsynced_post->id,
		);

		// Update post in database
		$wpdb->update($table_name, $updated_data, array('id' => $unsynced_post->id));
	}

	$wpdb->update(
		$settings_table,
		['setting_value' => current_time('mysql')],  // Data to update
		['setting_name' => 'linkedin_scrapper_last_update']  // Where condition
	);
}



// Update scrape_data function to call process_scraped_data
// Function to extract company name from URL
function extract_company_name($url)
{
	$parsed_url = parse_url($url);
	$path_parts = explode('/', $parsed_url['path']);
	if (in_array('company', $path_parts)) {
		$company_index = array_search('company', $path_parts) + 1;
		if ($company_index < count($path_parts)) {
			return $path_parts[$company_index];
		}
	}
	return null;
}

// Function to scrape data
function scrape_data()
{
	global $wpdb;
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings'; // Your custom table name

	// Function to get setting value from the custom table
	function get_custom_setting($setting_name, $default_value)
	{
		global $wpdb, $settings_table;
		$value = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_name = %s", $setting_name));
		return ($value !== null) ? $value : $default_value;
	}

	$url = "http://localhost:3001/scrape";
	$data = array(
		"secret_key" => "test",
		"url" => get_custom_setting('linkedin_company_url', 'https://www.linkedin.com/company/alpine-laser/'),
		"postSelector" => 'li[class="mb-1"]',
		"selectorsArray" => array(
			'li[class="mb-1"] article',
			'time',
			'span[data-test-id="social-actions_reaction-count"]',
			'a[data-id="social-actions_comments"]',
			'a[data-tracking-control-name="organization_guest_main-feed-card_feed-actor-name"]'
		),
		"attributesArray" => array(
			"data-activity-urn",
			"innerText",
			"innerText",
			"innerText",
			"innerText"
		),
		"namesArray" => array(
			"URN",
			"age",
			"reactions",
			"comments",
			"company-name"
		)
	);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json'
	));
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_TIMEOUT, 360);  // Set timeout to 6 minutes
	$response = curl_exec($ch);

	if ($response === false) {
		$error = curl_error($ch);
		curl_close($ch);
		echo "An error occurred during the API call: $error";
		return null;
	}

	curl_close($ch);
	$decoded_response = json_decode($response, true);

	if (isset($decoded_response["results"]) && is_array($decoded_response["results"])) {
		process_scraped_data($decoded_response["results"]);  // Call process_scraped_data with response array
		return $decoded_response["results"];
	} else {
		echo "No results found or an error occurred.";
		return null;
	}
}

// Schedule scrape_data function to run hourly
if (!wp_next_scheduled('scrape_data_cron_job')) {
	wp_schedule_event(time(), 'daily', 'scrape_data_cron_job');
}
add_action('scrape_data_cron_job', 'scrape_data');



/*
function update_linkedin_settings()
{
	//Verify nonce for security
	if (!isset($_POST['linkedin_settings_nonce']) || !wp_verify_nonce($_POST['linkedin_settings_nonce'], 'update_linkedin_settings')) {
		wp_send_json_error('Invalid nonce');
		wp_die();
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_slider_settings'; // Replace with your table name

	// Sanitize and update the settings
	$settings_to_update = [
		'linkedin_company_url' => sanitize_text_field($_POST['linkedin_company_url']),
		'linkedin_slider_open_link' => intval($_POST['linkedin_slider_open_link']),
		'linkedin_update_frequency' => intval($_POST['linkedin_update_frequency']),
		'linkedin_scrapper_endpoint' => sanitize_text_field($_POST['linkedin_scrapper_endpoint'])
	];

	foreach ($settings_to_update as $setting_name => $new_value) {

		$wpdb->update(
			$table_name,
			['setting_value' => $new_value], // Data to update
			['setting_name' => $setting_name] // Where condition
		);
		if ($wpdb->last_error) {
			wp_send_json_error($wpdb->last_error);
			wp_die();
		}
	}

	wp_send_json_success('Settings updated successfully');
	wp_die();
}
add_action('wp_ajax_update_linkedin_settings', 'update_linkedin_settings');
*/