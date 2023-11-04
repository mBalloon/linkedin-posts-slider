<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Handler for deleting a post
function linkedin_delete_post_function()
{
    check_ajax_referer('linkedin_posts_table_nonce', 'nonce');

    global $wpdb;
    $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($post_id) {
        $table_name = $wpdb->prefix . 'linkedin_posts';
        $result = $wpdb->delete($table_name, array('id' => $post_id), array('%d'));

        if ($result) {
            wp_send_json_success(__('Post deleted successfully.', 'linkedin-posts-slider'));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete the post.', 'linkedin-posts-slider')));
        }
    } else {
        wp_send_json_error(array('message' => __('Invalid post ID.', 'linkedin-posts-slider')));
    }
}
add_action('wp_ajax_linkedin_delete_post', 'linkedin_delete_post_function');

// Handler for publishing/unpublishing a post
function linkedin_publish_unpublish_post_function()
{
    check_ajax_referer('linkedin_posts_table_nonce', 'nonce');

    global $wpdb;
    $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $published = isset($_POST['published']) ? intval($_POST['published']) : 0;

    if ($post_id) {
        $table_name = $wpdb->prefix . 'linkedin_posts';
        $result = $wpdb->update($table_name, array('published' => !$published), array('id' => $post_id), array('%d'), array('%d'));

        if ($result !== false) {
            wp_send_json_success(array('published' => !$published, 'message' => __('Post status updated successfully.', 'linkedin-posts-slider')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update the post status.', 'linkedin-posts-slider')));
        }
    } else {
        wp_send_json_error(array('message' => __('Invalid post ID.', 'linkedin-posts-slider')));
    }
}
add_action('wp_ajax_linkedin_publish_unpublish_post', 'linkedin_publish_unpublish_post_function');

// Handler for moving a post up or down
function linkedin_move_post_function()
{
    check_ajax_referer('linkedin_posts_table_nonce', 'nonce');

    global $wpdb;
    $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $direction = isset($_POST['direction']) ? $_POST['direction'] : '';

    if ($post_id && in_array($direction, array('up', 'down'))) {
        $table_name = $wpdb->prefix . 'linkedin_posts';

        // Get the current post order
        $current_order = $wpdb->get_var($wpdb->prepare("SELECT post_order FROM $table_name WHERE id = %d", $post_id));
        if (null === $current_order) {
            wp_send_json_error(array('message' => __('Post not found.', 'linkedin-posts-slider')));
        }

        // Determine new order based on direction
        $new_order = ('up' === $direction) ? $current_order - 1 : $current_order + 1;

        // Swap order values
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET post_order = %d WHERE post_order = %d", $current_order, $new_order));
        $wpdb->update($table_name, array('post_order' => $new_order), array('id' => $post_id), array('%d'), array('%d'));

        wp_send_json_success(__('Post order updated successfully.', 'linkedin-posts-slider'));
    } else {
        wp_send_json_error(array('message' => __('Invalid action or post ID.', 'linkedin-posts-slider')));
    }
}
add_action('wp_ajax_linkedin_move_post', 'linkedin_move_post_function');


// Function to process scraped data and update the posts
function process_scraped_data($data_array)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'linkedin_posts';
    $settings_table = $wpdb->prefix . 'linkedin_slider_settings';
    $new_posts_added = false;

    // Fetch the endpoint from the settings table
    $endpoint = $wpdb->get_var(
        "SELECT setting_value FROM $settings_table WHERE setting_name = 'linkedin_scrapper_endpoint'"
    );

    // Make a request to the endpoint
    $response = wp_remote_get($endpoint);

    // Exit if there is an error in the response
    if (is_wp_error($response)) {
        error_log('Error fetching data: ' . $response->get_error_message());
        return;
    }

    // Assume the response is an array of posts
    $posts = json_decode(wp_remote_retrieve_body($response), true);

    // Update each existing post or add new ones
    foreach ($posts as $post_data) {
        $urn = $post_data['urn'];
        $reactions = intval($post_data['likes']); // Assuming 'likes' corresponds to 'reactions'
        $comments = intval($post_data['comments']);
        $age = sanitize_text_field($post_data['age']);

        // Check for existing post by urn
        $existing_post = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE urn = %s", $urn)
        );

        // Update or insert post
        if ($existing_post) {
            // Update existing post with new data
            $wpdb->update(
                $table_name,
                ['reactions' => $reactions, 'comments' => $comments, 'age' => $age],
                ['urn' => $urn]
            );
        } else {
            // Insert new post with 'synced' as false
            $wpdb->insert(
                $table_name,
                ['urn' => $urn, 'reactions' => $reactions, 'comments' => $comments, 'age' => $age, 'synced' => false]
            );
            $new_posts_added = true;
        }
    }

    // If new posts were added, sync them
    if ($new_posts_added) {
        sync_unsynced_posts();
    }
}

// Function to sync posts that haven't been synced yet
function sync_unsynced_posts()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'linkedin_posts';
    $settings_table = $wpdb->prefix . 'linkedin_slider_settings';

    // Find all unsynced posts
    $unsynced_posts = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE synced = false"
    );

    foreach ($unsynced_posts as $unsynced_post) {
        // Call a function to fetch and add the full post content
        // This function needs to be implemented according to your specific requirements
        fetch_and_update_post_content($unsynced_post);
    }

    // Assuming fetch_and_update_post_content function updates 'synced' to true
}

function fetch_and_update_post_content($post)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'linkedin_posts';

    // Extracting the URN from the provided post object
    $urn = $post->urn;
    $request_url = $endpoint;
    $post_url = 'https://www.linkedin.com/feed/update/' . $urn;

    // Prepare request data, similar to the sync_unsynced_posts function
    $request_data = array(
        "secret_key" => "test",
        "url" => $post_url,
        "postSelector" => 'section[class="mb-3"]',
        "selectorsArray" => [
            'section[class="mb-3"] article',
            'time',
            'a[data-tracking-control-name="public_post_feed-actor-image"] img',
            'p[data-test-id="main-feed-activity-card_commentary"]',
            'span[data-test-id="social-actionsreaction-count"]',
            'a[data-id="social-actions_comments"]',
            'ul[data-test-id="feed-images-content"] img'
        ],
        "attributesArray" => [
            "data-attributed-urn",
            "innerText",
            "src",
            "innerText",
            "innerText",
            "innerText",
            "src"
        ],
        "namesArray" => [
            "URN",
            "age",
            "profile_picture",
            "post_text",
            "reactions",
            "comments",
            "images"
        ]
    );

    // Make POST request to the scraping endpoint
    $response = wp_remote_post($endpoint, array(
        'body' => json_encode($request_data),
        'headers' => array('Content-Type' => 'application/json')
    ));

    // Handle error
    if (is_wp_error($response)) {
        error_log($response->get_error_message());
        return; // Exit function if there's an error
    }

    // Parse response data
    $response_data = json_decode(wp_remote_retrieve_body($response), true);
    $post_data = $response_data['results'][0];

    // Sanitize and prepare updated post data
    $updated_data = array(
        'author' => 'Alpine Laser', // This should be dynamic if you have different authors
        'username' => 'alpine-laser', // This should also be dynamic based on the post data
        'age' => sanitize_text_field($post_data['age'][0]) . " •",
        'profile_picture' => esc_url_raw($post_data['profile_picture'][0]),
        'post_text' => sanitize_textarea_field($post_data['post_text'][0]),
        'images' => json_encode(array_filter($post_data['images'], 'strlen')), // Store images as JSON encoded string
        'reactions' => absint($post_data['reactions'][0]), // Convert to absolute integer
        'comments' => sanitize_text_field($post_data['comments'][0]),
        'synced' => true,
        'published' => false,
        'post_order' => $post->post_order, // Keep the existing post order
    );

    // Update post in the database
    $wpdb->update($table_name, $updated_data, array('id' => $post->id));
}


// Schedule the scrape_data function to run daily
if (!wp_next_scheduled('scrape_data_cron_job')) {
    wp_schedule_event(time(), 'daily', 'scrape_data_cron_job');
}
add_action('scrape_data_cron_job', 'scrape_data');
