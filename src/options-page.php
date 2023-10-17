<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add an options page for the Linkedin Posts Slider widget in the WordPress admin menu.
function linkedin_posts_slider_options_page()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$message = '';

	$url = '';
	// Check if form is submitted
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$url = sanitize_text_field($_POST['linkedin_url']);
		$question_mark_position = strpos($url, '?');
		if ($question_mark_position !== false) {
			$url = substr($url, 0, $question_mark_position);
		}

		// Validate URL
		if (filter_var($url, FILTER_VALIDATE_URL) && strpos($url, 'linkedin.com') !== false) {
			// Insert into table
			$result = $wpdb->insert(
				$table_name,
				array(
					'url' => $url,
					'synced' => false,
					'published' => false
				),
				array('%s', '%d', '%d')
			);

			if ($result !== false) {
				$message = 'URL added successfully.';
				$url = '';
			} else {
				$message = 'Failed to add URL.';
			}
		} else {
			$message = 'Invalid LinkedIn URL.';
		}
	}

	echo '<style>
	    .wrap {
	        font-family: Arial, sans-serif;
	        max-width: 600px;
	        margin: 0 auto;
	        padding: 20px;
	        background-color: #f9f9f9;
	        border: 1px solid #ddd;
	        border-radius: 5px;
	    }
	    label {
	        display: block;
	        margin: 10px 0;
	    }
	    input[type="text"] {
	        width: 100%;
	        padding: 10px;
	        margin: 10px 0;
	        box-sizing: border-box;
	        border: 1px solid #ddd;
	        border-radius: 5px;
	    }
	    input[type="submit"] {
	        background-color: #4CAF50;
	        color: white;
	        padding: 10px 20px;
	        margin: 10px 0;
	        border: none;
	        cursor: pointer;
	        border-radius: 5px;
	    }
	    input[type="submit"]:hover {
	        background-color: #45a049;
	    }
	</style>';
	echo '<div class="wrap">';
	echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
	echo '<form action="" method="post">';
	echo '<label for="linkedin_url">LinkedIn URL:</label>';
	echo '<input type="text" id="linkedin_url" name="linkedin_url" value="' . esc_attr($url) . '">';
	submit_button('Add URL');
	echo '</form>';
	if ($message != '') {
		echo '<p>' . esc_html($message) . '</p>';
	}
	echo '</div>';
}