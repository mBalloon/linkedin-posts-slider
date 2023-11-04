<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function enqueue_preview_assets()
{
	wp_enqueue_style('preview-styles', plugin_dir_url(__FILE__) . 'preview.css');
	wp_enqueue_script('preview-scripts', plugin_dir_url(__FILE__) . 'preview.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_preview_assets');

global $wpdb; // Global WordPress database variable

// Function to get a specific setting value from the custom table or default value
// src/options-page.php
require_once 'utils.php';

// Function to update or insert setting value in custom table
function update_custom_setting($setting_name, $setting_value)
{
	global $wpdb;
	$wpdb->replace(
		"{$wpdb->prefix}linkedin_slider_settings",
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style_settings_form'])) {
	// Save settings
	foreach ($_POST as $key => $value) {
		if (strpos($key, 'section-') === 0) { // Ensures only our settings are updated
			update_custom_setting($key, sanitize_text_field($value));
		}
	}

	// Redirect back to settings page with a message
	wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
	exit;
}

function linkedin_posts_slider_options_page()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

	// Add the nonce field for security
	wp_nonce_field('style_settings_action', 'style_settings_nonce');

?>
	<div style="display: flex;">
		<div style="flex: 1;">
			<h1>Style Live Editor</h1>
			<form method="post" action="">
				<input type="hidden" name="style_settings_form" value="1">
				<?php
				// Add form sections
				$sections = [
					'company' => 'Company Name',
					'author-date' => 'Username and Post Date',
					'body' => 'Post Text',
					'interactions' => 'Post Interactions and Comments'
				];
				foreach ($sections as $section => $title) {
				?>
					<div class="wp-ui-form-section">
						<h2 class="wp-ui-form-section-title"><?php echo $title; ?></h2>
						<div class="wp-ui-form-subsection">
							<?php
							$settings = [
								'color' => 'Color',
								'font-size' => 'Size',
								'font-weight' => 'Weight',
								'line-height' => 'Line Height',
								'font-family' => 'Font Family',
								'webkit-line-clamp' => 'Max Number of Lines' // Only for 'body' section
							];
							foreach ($settings as $setting => $label) {
								// Skip the line clamp setting for non-body sections
								if ($setting === 'webkit-line-clamp' && $section !== 'body') {
									continue;
								}

								$setting_name = "section-{$section}-{$setting}";
								$default_value = ($setting === 'font-family') ? 'Arial' : '#000000';
								if ($setting === 'font-size' || $setting === 'line-height' || $setting === 'webkit-line-clamp') {
									$default_value = '14';
								}
								$value = get_custom_setting($setting_name, $default_value);
							?>
								<div class="wp-ui-form-group">
									<label for="<?php echo $setting_name; ?>" class="wp-ui-form-label"><?php echo $label; ?>:</label>
									<input type="<?php echo ($setting === 'color') ? 'color' : 'text'; ?>" id="<?php echo $setting_name; ?>" name="<?php echo $setting_name; ?>" value="<?php echo esc_attr($value); ?>" class="wp-ui-form-input">
								</div>
							<?php
							}
							?>
						</div>
					</div>
				<?php
				}
				?>
				<button type="submit">Update Settings</button>
			</form>
		</div>
		<div style="flex: 1; display: flex; justify-content: center; align-items: center;">
			<div id="post-preview" class="post-preview-class">
				<h2>Post Preview</h2>
				<div id="post-preview" class="post-preview-class">
					<div class="swiper-slide">
						<div class="li-icon-white">
							<svg style="
			width: 30px;
			height: 30px;
			overflow: visible;
			fill: rgb(255, 255, 255);
			" viewBox="0 0 448 512">
								<path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path>
							</svg>
						</div>
						<div class="img-container">
							<div class="li-single-img" style="
			background-image: url('https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4');
			"></div>
						</div>

						<div class="info-container">
							<div class="li-author-img" style="
			background-image: url('https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1705536000&v=beta&t=9PVwxirZIj7Pgh68ihS7bA_UscLia5XiJy9llH9Q_PA');
			"></div>
							<div class="section-company section-company">Alpine Laser</div>
							<div class="section-author-date">
								<span class="li-author-username">@alpine-laser . </span>
								<span class="li-post-age">3w ago</span>
							</div>
							<p class="section-body">
								Come see a live demo of femtosecond tube cutting today and tomorrow at MDM
								in booth 2803!
								Come see a live demo of femtosecond tube cutting today and tomorrow at MDM
								in booth 2803!
							</p>
							<div class="section-interactions">
								<span><svg style="
				width: 24px;
				height: 24px;
				overflow: visible;
				fill: rgb(0, 122, 255);
				" viewBox="0 0 24 24">
										<path fill="none" d="M0 0h24v24H0z"></path>
										<path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path>
										<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
										<path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path>
									</svg></span>
								<span class="li-post-reactions">74 . </span>
								<span class="li-post-comments">84 comments</span>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
<?php
}
add_action('admin_menu', function () {
	add_options_page('Linkedin Slider Style Settings', 'Linkedin Posts Style Settings', 'manage_options', 'linkedin-posts-slider', 'linkedin_posts_slider_options_page');
});
