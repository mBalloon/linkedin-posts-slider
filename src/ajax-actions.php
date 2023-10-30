<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

$linkedin_company_url = get_option('linkedin_company_url', 'https://www.linkedin.com/company/alpine-laser/');
$linkedin_slider_open_link = get_option('linkedin_slider_open_link', 'https://scrape-js.onrender.com/scrape');
$linkedin_update_frequency = get_option('linkedin_update_frequency', '86400');
$linkedin_scrapper_status = get_option('linkedin_scrapper_status', 'OK');
$linkedin_scrapper_last_update = get_option('linkedin_scrapper_last_update', '');
$linkedin_scrapper_endpoint = get_option('linkedin_scrapper_endpoint', 'https://scrape-js.onrender.com/scrape');


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

	// Update the 'linkedin_scrapper_last_update' option
	update_option('linkedin_scrapper_last_update', current_time('mysql'));
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
	$url = "http://localhost:3001/scrape";
	$data = array(
		"secret_key" => "test",
		"url" => get_option('linkedin_company_url', 'https://www.linkedin.com/company/alpine-laser/'),
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


// AJAX handler function for LinkedIn Posts Scrapper form
function ajax_handle_scrapper_form_submission()
{
	// Check if nonce is set and valid
	if (isset($_POST['linkedin_scrapper_options_nonce']) && wp_verify_nonce($_POST['linkedin_scrapper_options_nonce'], 'update_linkedin_scrapper_options')) {

		// Validate and Update Company URL
		if (isset($_POST['linkedin_company_url'])) {
			$company_url = sanitize_text_field($_POST['linkedin_company_url']);
			if (filter_var($company_url, FILTER_VALIDATE_URL)) {
				update_option('linkedin_company_url', $company_url);
			} else {
				wp_send_json_error(array('message' => 'Invalid Company URL.'));
				return;
			}
		}

		// Validate and Update Post Links Behavior
		$open_link = isset($_POST['linkedin_slider_open_link']) ? 1 : 0;
		update_option('linkedin_slider_open_link', $open_link);

		// Validate and Update Scrapping Frequency
		if (isset($_POST['linkedin_update_frequency'])) {
			$frequency = intval($_POST['linkedin_update_frequency']);
			if ($frequency > 0) {
				update_option('linkedin_update_frequency', $frequency);
			} else {
				wp_send_json_error(array('message' => 'Invalid Scrapping Frequency.'));
				return;
			}
		}

		// Validate and Update Scrapper Endpoint
		if (isset($_POST['linkedin_scrapper_endpoint'])) {
			$endpoint = sanitize_text_field($_POST['linkedin_scrapper_endpoint']);
			if (filter_var($endpoint, FILTER_VALIDATE_URL)) {
				update_option('linkedin_scrapper_endpoint', $endpoint);
			} else {
				wp_send_json_error(array('message' => 'Invalid Scrapper Endpoint URL.'));
				return;
			}
		}

		// If everything is fine, send a success message
		wp_send_json_success(array('message' => 'Settings saved successfully.'));
	} else {
		wp_send_json_error(array('message' => 'Invalid request.'));
	}
}

// Register the AJAX handler function for logged-in users
add_action('wp_ajax_handle_form_submission', 'ajax_handle_scrapper_form_submission');

// Register the AJAX handler function for guests (if needed)
add_action('wp_ajax_nopriv_handle_form_submission', 'ajax_handle_scrapper_form_submission');
