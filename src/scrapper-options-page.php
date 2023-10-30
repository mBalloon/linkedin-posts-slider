<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

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
		<form id="my-ajax-form">


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
	<script>
		jQuery(document).ready(function($) {
			$('#my-ajax-form').on('submit', function(e) {
				e.preventDefault();

				var formData = $(this).serialize();

				$.ajax({
					url: ajaxurl, // 'ajaxurl' is automatically defined by WordPress and points to 'admin-ajax.php'
					type: 'POST',
					data: {
						action: 'wp_ajax_handle_form_submission',
						form_data: formData
					},
					success: function(response) {
						if (response.success) {
							alert(response.data.message);
						}
					}
				});
			});
		});
	</script>
<?php
}
