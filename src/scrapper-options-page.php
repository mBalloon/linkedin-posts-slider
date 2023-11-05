<?php

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
	exit;
}

// Handle form submission
if (defined('DOING_AJAX') && DOING_AJAX && $_SERVER['REQUEST_METHOD'] === 'POST' && function_exists('check_admin_referer')) {

	// Check nonce for security
	check_admin_referer('update_scrapper_settings');

	global $wpdb;

	// Table names
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';

	// Check if the table exists
	if($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") != $settings_table) {
	    // Table doesn't exist, create it
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

	// Sanitize and validate input
	$settings = [
		'linkedin_company_url' => isset($_POST['linkedin_company_url']) ? esc_url_raw($_POST['linkedin_company_url']) : '',
		'linkedin_slider_open_link' => isset($_POST['linkedin_slider_open_link']) ? (int) $_POST['linkedin_slider_open_link'] : 0,
		'linkedin_update_frequency' => isset($_POST['linkedin_update_frequency']) ? (int) $_POST['linkedin_update_frequency'] : 0,
		'linkedin_scrapper_endpoint' => isset($_POST['linkedin_scrapper_endpoint']) ? esc_url_raw($_POST['linkedin_scrapper_endpoint']) : '',
	];

	// Update settings in database
	foreach ($settings as $name => $value) {
		$wpdb->update(
			$settings_table,
			['setting_value' => $value],
			['setting_name' => $name]
		);
	}

	// Add settings updated message
	add_settings_error('linkedin_scrapper_settings', 'settings_updated', __('Settings updated successfully'), 'success');

	// Redirect back to settings page
	wp_safe_redirect(admin_url('admin.php?page=linkedin_scrapper_settings'));
	exit;
}

// Check if the linkedin_posts table exists
$posts_table = $wpdb->prefix . 'linkedin_posts';
if($wpdb->get_var("SHOW TABLES LIKE '$posts_table'") != $posts_table) {
    // Table doesn't exist
    $total_posts = $published_posts = $synced_posts = 0;
} else {
    // Table exists, fetch stats
    $total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table");
    $published_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE published = 1");
    $synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE synced = 1");
}

global $wpdb;
$settings_table = $wpdb->prefix . 'linkedin_slider_settings';
$results = $wpdb->get_results("SELECT * FROM $settings_table", ARRAY_A);

$settings = array(
    'linkedin_company_url' => '',
    'linkedin_slider_open_link' => 0,
    'linkedin_update_frequency' => 0,
    'linkedin_scrapper_endpoint' => '',
);

foreach ($results as $row) {
    if (array_key_exists($row['setting_name'], $settings)) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
}
$last_update = is_array($settings) && isset($settings['linkedin_scrapper_last_update']) ? $settings['linkedin_scrapper_last_update'] : '';
$status = is_array($settings) && isset($settings['linkedin_scrapper_status']) ? $settings['linkedin_scrapper_status'] : '';

?>

<div class="wrap">

	<h1><?php echo esc_html('Scrapper Options'); ?></h1>

	<!-- Show status messages -->
	<?php
	if (function_exists('settings_errors')) {
		settings_errors('linkedin_scrapper_settings');
	}
	?>

	<!-- Stats -->
	<div class="stats-wrapper">

		<div class="stat-card">
			<span class="stat-title"><?php _e('Last Update:', 'linkedin-posts-slider'); ?></span>
			<span class="stat-value"><?php echo $last_update; ?></span>
		</div>

		<div class="stat-card">
			<span class="stat-title"><?php _e('Total Posts:', 'linkedin-posts-slider'); ?></span>
			<span class="stat-value"><?php echo $total_posts; ?></span>
		</div>

		<div class="stat-card">
			<span class="stat-title"><?php _e('Published:', 'linkedin-posts-slider'); ?></span>
			<span class="stat-value"><?php echo $published_posts; ?></span>
		</div>

		<div class="stat-card">
			<span class="stat-title"><?php _e('Synced:', 'linkedin-posts-slider'); ?></span>
			<span class="stat-value"><?php echo $synced_posts; ?></span>
		</div>

		<div class="stat-card">
			<span class="stat-title"><?php _e('Status:', 'linkedin-posts-slider'); ?></span>
			<span class="stat-value status-<?php echo esc_attr($status); ?>"><?php echo $status; ?></span>
		</div>

	</div>

	<!-- Settings Form -->
	<form method="post" action="<?php echo admin_url('admin.php?page=linkedin_scrapper_settings'); ?>">

		<?php //wp_nonce_field('update_scrapper_settings'); 
		?>

		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('Company Profile URL:', 'linkedin-posts-slider'); ?></th>
				<td>
					<input type="text" name="linkedin_company_url" class="regular-text" value="<?php echo esc_attr($settings['linkedin_company_url']); ?>">
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Post Links Behavior:', 'linkedin-posts-slider'); ?></th>
				<td>
					<input type="checkbox" name="linkedin_slider_open_link" value="1" <?php checked(1, $settings['linkedin_slider_open_link']); ?>>
					<?php _e('Open post links in new tab', 'linkedin-posts-slider'); ?>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Update Frequency:', 'linkedin-posts-slider'); ?></th>
				<td>
					<input type="number" name="linkedin_update_frequency" class="regular-text" value="<?php echo esc_attr($settings['linkedin_update_frequency']); ?>">
					<?php _e('seconds', 'linkedin-posts-slider'); ?>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Scraper Endpoint:', 'linkedin-posts-slider'); ?></th>
				<td>
					<input type="text" name="linkedin_scrapper_endpoint" class="regular-text" value="<?php echo esc_attr($settings['linkedin_scrapper_endpoint']); ?>">
				</td>
			</tr>

		</table>

		<button type="submit">Update Settings</button>

	</form>

</div>

<?php

// Styles
echo <<<CSS

<style>

  .stats-wrapper {
    display: flex; 
    justify-content: space-around;
    margin-bottom: 20px;
    padding: 10px;
    background: #f1f1f1; 
    border: 1px solid #ccc;
    border-radius: 10px;
  }
  
  .stat-card {
    text-align: center;
  }

  .stat-title {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }

  .stat-value.status-ok {
    color: green;
    font-family: monospace; 
    font-weight: bold;
  }

  .stat-value.status-error {
    color: red;
    font-family: monospace;
    font-weight: bold;
  }

</style>
CSS;

?>

<?php

// Scripts
echo <<<JS

<script>

</script>  

JS;

?>
