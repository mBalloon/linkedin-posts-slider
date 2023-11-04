<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Enqueue any required styles or scripts
function enqueue_scrapper_settings_assets()
{
	wp_enqueue_style('scrapper-settings-styles', plugin_dir_url(__FILE__) . 'style.css');
	// If you have a JS file for this page you can enqueue it here
	// wp_enqueue_script('scrapper-settings-script', plugin_dir_url(__FILE__) . 'script.js');
}
add_action('admin_enqueue_scripts', 'enqueue_scrapper_settings_assets');

function update_scrapper_settings($setting_name, $setting_value)
{
	global $wpdb;
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';

	$wpdb->replace(
		$settings_table,
		array(
			'setting_name' => $setting_name,
			'setting_value' => $setting_value,
		),
		array(
			'%s',
			'%s',
		)
	);
}

// Handle the POST request here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['linkedin_scrapper_settings_form'])) {
	check_admin_referer('linkedin_scrapper_settings_action', 'linkedin_scrapper_settings_nonce');

	update_scrapper_settings('linkedin_company_url', sanitize_text_field($_POST['linkedin_company_url']));
	update_scrapper_settings('linkedin_slider_open_link', isset($_POST['linkedin_slider_open_link']) ? 1 : 0);
	update_scrapper_settings('linkedin_update_frequency', intval($_POST['linkedin_update_frequency']));
	update_scrapper_settings('linkedin_scrapper_endpoint', sanitize_text_field($_POST['linkedin_scrapper_endpoint']));

	// Redirect back to settings page with a message
	wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
	exit;
}

function get_scrapper_setting($setting_name, $default = '')
{
	global $wpdb;
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';
	$value = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_name = %s", $setting_name));
	return (is_null($value)) ? $default : $value;
}

function linkedin_posts_scrapper_settings_page()
{
	global $wpdb;
	$last_update = get_scrapper_setting('linkedin_scrapper_last_update', 'Not yet updated');
	$status = get_scrapper_setting('linkedin_scrapper_status', 'Unknown');
	$company_url = get_scrapper_setting('linkedin_company_url', '');
	$open_link = get_scrapper_setting('linkedin_slider_open_link', 0);
	$update_frequency = get_scrapper_setting('linkedin_update_frequency', 3600);
	$endpoint = get_scrapper_setting('linkedin_scrapper_endpoint', '');
	// Fetch total number of posts
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}linkedin_posts");

	// Fetch number of published posts
	$published_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}linkedin_posts WHERE published = 1");

	// Fetch number of synced posts
	$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}linkedin_posts WHERE synced = 1");

?>
	<div class="wrap">
		<h1>LinkedIn Posts Scrapper Settings</h1>
		<?php if (isset($_GET['settings-updated'])) : ?>
			<div id="message" class="updated notice is-dismissible">
				<p><strong>Settings saved.</strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">Dismiss this notice.</span>
				</button>
			</div>
		<?php endif; ?>

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
			<?php wp_nonce_field('linkedin_scrapper_settings_action', 'linkedin_scrapper_settings_nonce'); ?>
			<table class="form-table">
				<!-- Company Profile URL -->
				<tr valign="top">
					<th scope="row">Company Profile URL</th>
					<td>
						<input type="text" name="linkedin_company_url" value="<?php echo esc_attr($company_url); ?>" />
					</td>
				</tr>
				<!-- Post Links Behavior -->
				<tr valign="top">
					<th scope="row">Post Links Behavior</th>
					<td>
						<input type="checkbox" name="linkedin_slider_open_link" value="1" <?php checked(1, $open_link, true); ?> />
					</td>
				</tr>
				<!-- Scrapping Frequency -->
				<tr valign="top">
					<th scope="row">Scrapping Frequency (in seconds)</th>
					<td>
						<input type="number" name="linkedin_update_frequency" value="<?php echo esc_attr($update_frequency); ?>" />
					</td>
				</tr>
				<!-- Scrapper Endpoint -->
				<tr valign="top">
					<th scope="row">Scrapper Endpoint</th>
					<td>
						<input type="text" name="linkedin_scrapper_endpoint" value="<?php echo esc_attr($endpoint); ?>" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<button type="submit" class="button button-primary">Update Settings</button>
			</p>
		</form>
	</div>
<?php
}

// Register the settings page
function linkedin_register_scrapper_settings_page()
{
	add_options_page(
		'LinkedIn Posts Scrapper Settings',
		'LinkedIn Scrapper Settings',
		'manage_options',
		'linkedin-scrapper-settings',
		'linkedin_posts_scrapper_settings_page'
	);
}
add_action('admin_menu', 'linkedin_register_scrapper_settings_page');
?>