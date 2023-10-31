<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function enqueue_scrapper_scripts_and_styles()
{
	wp_enqueue_script('scrapper-script', plugin_dir_url(dirname(__FILE__)) . 'scrapper-settings-form.js', array('jquery'), null, true);
	wp_localize_script('scrapper-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_scrapper_scripts_and_styles');



// Add an options page for the Linkedin Posts Slider widget in the WordPress admin menu.
function linkedin_posts_scrapper_settings_page()
{

	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$settings_table = $wpdb->prefix . 'linkedin_slider_settings';
	$settings = $wpdb->get_results("SELECT * FROM $settings_table");

	$settings_map = [];
	foreach ($settings as $setting) {
		$settings_map[$setting->setting_name] = $setting->setting_value;
	}

	// Fetch statistics
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	$published_posts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE published = true");
	$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE synced = true");
	$last_update = $settings_map['linkedin_scrapper_last_update'];
	$status = $settings_map['linkedin_scrapper_status'];
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
		<form id="my-ajax-form">
			<?php wp_nonce_field('update_linkedin_settings', 'linkedin_settings_nonce'); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">Company Profile URL</th>
					<td><input type="text" name="linkedin_company_url" value="<?php echo esc_attr($settings_map['linkedin_company_url']); ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Post Links Behavior</th>
					<td><input type="checkbox" name="linkedin_slider_open_link" value="1" <?php checked(1, $settings_map['linkedin_slider_open_link'], true); ?> /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Scrapping Frequency (in seconds)</th>
					<td><input type="number" name="linkedin_update_frequency" value="<?php echo esc_attr($settings_map['linkedin_update_frequency']); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">Scrapper Endpoint</th>
					<td><input type="text" name="linkedin_scrapper_endpoint" value="<?php echo esc_attr($settings_map['linkedin_scrapper_endpoint']); ?>" /></td>
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
	<script>

	</script>
<?php
}
