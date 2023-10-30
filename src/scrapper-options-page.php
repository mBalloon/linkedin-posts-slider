<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/*
function enqueue_preview_styles()
{
	//wp_enqueue_style('preview-styles', plugin_dir_url(dirname(__FILE__)) . 'preview.css');
	//wp_enqueue_style('swiper-style');
}
//add_action('admin_enqueue_scripts', 'enqueue_preview_styles');

function enqueue_preview_scripts()
{
	wp_enqueue_script('preview-scripts', plugin_dir_url(dirname(__FILE__)) . 'preview.js', array('jquery'), null, true);
}
//add_action('admin_enqueue_scripts', 'enqueue_preview_scripts');
*/

function linkedin_posts_scrapper_register_settings()
{
	// Default values
	$defaults = [
		'linkedin_company_url' => 'https://www.linkedin.com/company/alpine-laser/',
		'linkedin_slider_open_link' => 1, // true by default
		'linkedin_update_frequency' => 60 * 60 * 24, // 24 hours in seconds
		'linkedin_scrapper_status' => 'OK',
		'linkedin_scrapper_last_update' => 'Not available',
		'linkedin_scrapper_endpoint' => 'https://scrape-js.onrender.com/scrape'
	];

	foreach ($defaults as $key => $value) {
		if (get_option($key) === false) {
			update_option($key, $value);
		}
	}

	// Register settings
	register_setting('linkedin-posts-scrapper-settings-group', 'linkedin_company_url');
	register_setting('linkedin-posts-scrapper-settings-group', 'linkedin_slider_open_link');
	register_setting('linkedin-posts-scrapper-settings-group', 'linkedin_update_frequency');
	register_setting('linkedin-posts-scrapper-settings-group', 'linkedin_scrapper_status');
	register_setting('linkedin-posts-scrapper-settings-group', 'linkedin_scrapper_last_update');
	register_setting('linkedin-posts-scrapper-settings-group', 'linkedin_scrapper_endpoint');
}
add_action('admin_init', 'linkedin_posts_scrapper_register_settings');

// Add an options page for the Linkedin Posts Slider widget in the WordPress admin menu.
function linkedin_posts_scrapper_settings_page()
{
	// Handle form submission
	handle_scrapper_form_submission();
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Fetch statistics
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	$published_posts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE published = true");
	$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE synced = true");
	$last_update = get_option('linkedin_scrapper_last_update', 'Not available');
	$status = get_option('linkedin_scrapper_status', 'OK');
?>

	<div class="wrap">
		<h1>LinkedIn Posts Scrapper Settings</h1>
		<?php settings_errors('linkedin_scrapper_settings'); ?>

		<!-- Stats Section -->
		<div class="stats-wrapper">
			<div class="stat-card">
				<span class="stat-title">Last Successful Update:</span>
				<span class="stat-value"><?php echo $last_update; ?></span>
			</div>
			<div class="stat-card">
				<span class="stat-title">Posts:</span>
				<span class="stat-value"><?php echo $total_posts; ?></span>
			</div>
			<div class="stat-card">
				<span class="stat-title">Published:</span>
				<span class="stat-value"><?php echo $published_posts; ?></span>
			</div>
			<div class="stat-card">
				<span class="stat-title">Synced:</span>
				<span class="stat-value"><?php echo $synced_posts; ?></span>
			</div>
			<div class="stat-card">
				<span class="stat-title">Status:</span>
				<span class="stat-value status-<?php echo strtolower($status); ?>"><?php echo $status; ?></span>
			</div>
		</div>

		<!-- Settings Form Section -->
		<form method="post" action="">
			<?php wp_nonce_field('update_linkedin_scrapper_options', 'linkedin_scrapper_options_nonce'); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">Company Profile URL</th>
					<td><input type="text" name="linkedin_company_url" value="<?php echo esc_attr(get_option('linkedin_company_url', 'https://www.linkedin.com/company/alpine-laser/')); ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Post Links Behavior</th>
					<td><input type="checkbox" name="linkedin_slider_open_link" value="1" <?php checked(1, get_option('linkedin_slider_open_link', true), true); ?> /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Scrapping Frequency (in seconds)</th>
					<td><input type="number" name="linkedin_update_frequency" value="<?php echo esc_attr(get_option('linkedin_update_frequency', 60 * 60 * 24)); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">Scrapper Endpoint</th>
					<td><input type="text" name="linkedin_scrapper_endpoint" value="<?php echo esc_attr(get_option('linkedin_scrapper_endpoint', 'https://scrape-js.onrender.com/scrape')); ?>" /></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>

	<style>
		.stats-wrapper {
			display: flex;
			justify-content: space-around;
			margin-bottom: 20px;
			padding: 10px;
			background-color: #f1f1f1;
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
			font-family: 'Courier New', Courier, monospace;
			font-weight: bold;
		}

		.stat-value.status-error {
			color: red;
			font-family: 'Courier New', Courier, monospace;
			font-weight: bold;
		}
	</style>
<?php
}

function handle_scrapper_form_submission()
{
	// Check if our nonce is set and valid
	if (isset($_POST['linkedin_scrapper_options_nonce']) && wp_verify_nonce($_POST['linkedin_scrapper_options_nonce'], 'update_linkedin_scrapper_options')) {

		// Validate and Update Company URL
		if (isset($_POST['linkedin_company_url'])) {
			$company_url = sanitize_text_field($_POST['linkedin_company_url']);
			if (filter_var($company_url, FILTER_VALIDATE_URL)) {
				update_option('linkedin_company_url', $company_url);
			} else {
				add_settings_error('linkedin_scrapper_settings', 'invalid_url', 'Invalid Company URL.', 'error');
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
				add_settings_error('linkedin_scrapper_settings', 'invalid_frequency', 'Invalid Scrapping Frequency.', 'error');
			}
		}

		// Validate and Update Scrapper Endpoint
		if (isset($_POST['linkedin_scrapper_endpoint'])) {
			$endpoint = sanitize_text_field($_POST['linkedin_scrapper_endpoint']);
			if (filter_var($endpoint, FILTER_VALIDATE_URL)) {
				update_option('linkedin_scrapper_endpoint', $endpoint);
			} else {
				add_settings_error('linkedin_scrapper_settings', 'invalid_endpoint', 'Invalid Scrapper Endpoint URL.', 'error');
			}
		}

		// Optionally, add a message to show that the update was successful
		if (!get_settings_errors('linkedin_scrapper_settings')) {
			add_settings_error('linkedin_scrapper_settings', 'settings_updated', 'Settings saved.', 'updated');
		}
	}
}



add_action('admin_menu', function () {
	add_options_page('Linkedin Posts Scrapper Settings', 'Linkedin Posts Scrapper Settings', 'manage_options', 'linkedin-posts-scrapper', 'linkedin_posts_scrapper_settings_page');
});
