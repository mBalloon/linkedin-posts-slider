<?php

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
	exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Check nonce for security
	check_admin_referer('update_scrapper_settings');

	global $wpdb;

	// Table names
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';

	// Sanitize and validate input
	$settings = [
		'linkedin_company_url' => esc_url_raw($_POST['linkedin_company_url']),
		'linkedin_slider_open_link' => (int) $_POST['linkedin_slider_open_link'],
		'linkedin_update_frequency' => (int) $_POST['linkedin_update_frequency'],
		'linkedin_scrapper_endpoint' => esc_url_raw($_POST['linkedin_scrapper_endpoint']),
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

// Fetch stats
$posts_table = $wpdb->prefix . 'linkedin_posts';
$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table");

$published_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE published = 1");

$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM $posts_table WHERE synced = 1");

$settings = get_option('linkedin_slider_settings');
$last_update = $settings['linkedin_scrapper_last_update'];
$status = $settings['linkedin_scrapper_status'];

?>

<div class="wrap">

	<h1><?php echo esc_html(get_the_title()); ?></h1>

	<!-- Show status messages -->
	<?php settings_errors('linkedin_scrapper_settings'); ?>

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

		<?php wp_nonce_field('update_scrapper_settings'); ?>

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

		<?php submit_button(); ?>

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
