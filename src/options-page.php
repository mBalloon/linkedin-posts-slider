<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function linkedin_posts_slider_register_settings()
{
	// Company info section
	register_setting('linkedin-posts-slider', 'section-company-color');
	register_setting('linkedin-posts-slider', 'section-company-font-size');
	register_setting('linkedin-posts-slider', 'section-company-font-family');
	register_setting('linkedin-posts-slider', 'section-company-line-height');
	register_setting('linkedin-posts-slider', 'section-company-font-weight');

	// Author username and date section
	register_setting('linkedin-posts-slider', 'section-author-date-color');
	register_setting('linkedin-posts-slider', 'section-author-date-font-size');
	register_setting('linkedin-posts-slider', 'section-author-date-font-family');
	register_setting('linkedin-posts-slider', 'section-author-date-font-weight');
	register_setting('linkedin-posts-slider', 'section-author-date-line-height');

	// Post text section
	register_setting('linkedin-posts-slider', 'section-body-color');
	register_setting('linkedin-posts-slider', 'section-body-font-size');
	register_setting('linkedin-posts-slider', 'section-body-font-family');
	register_setting('linkedin-posts-slider', 'section-body-webkit-line-clamp');
	register_setting('linkedin-posts-slider', 'section-body-font-weight');


	// Post interactions section
	register_setting('linkedin-posts-slider', 'section-interactions-color');
	register_setting('linkedin-posts-slider', 'section-interactions-font-size');
	register_setting('linkedin-posts-slider', 'section-interactions-font-family');
	register_setting('linkedin-posts-slider', 'section-interactions-font-weight');
	register_setting('linkedin-posts-slider', 'section-interactions-line-height');
}
add_action('admin_init', 'linkedin_posts_slider_register_settings');

function enqueue_preview_styles()
{
	wp_enqueue_style('preview-styles', plugin_dir_url(dirname(__FILE__)) . 'preview.css');
	wp_enqueue_style('swiper-style');
}
add_action('admin_enqueue_scripts', 'enqueue_preview_styles');

function enqueue_preview_scripts()
{
	wp_enqueue_script('preview-scripts', plugin_dir_url(dirname(__FILE__)) . 'preview.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_preview_scripts');


// Add an options page for the Linkedin Posts Slider widget in the WordPress admin menu.
function linkedin_posts_slider_options_page()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

?>

	<!-- Main container to hold the form and preview pane -->
	<div style="display: flex;">

		<!-- Left-half: Form -->
		<div style="flex: 1;">
			<h1>Style Live Editor</h1>

			<!-- TODO: Add form fields here -->
			<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
				<input type="hidden" name="action" value="handle_form_submission">
				<!-- Section: Company Name -->
				<div class="wp-ui-form-section">

					<h2 class="wp-ui-form-section-title">Company Name</h2>

					<!-- Subsection: Inputs -->
					<div class="wp-ui-form-subsection">

						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-company-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-company-color" name="section-company-color" value="<?php echo esc_attr(get_option('section-company-color', '#454545')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-company-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-company-font-size" name="section-company-font-size" value="<?php echo esc_attr(get_option('section-company-font-size', '16')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Weight -->
						<div class="wp-ui-form-group">
							<label for="section-company-font-weight" class="wp-ui-form-label">Weight:</label>
							<input type="number" id="section-company-font-weight" name="section-company-font-weight" value="<?php echo esc_attr(get_option('section-company-font-weight', '400')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Line Height -->
						<div class="wp-ui-form-group">
							<label for="section-company-line-height" class="wp-ui-form-label">Line Height:</label>
							<input type="number" id="section-company-line-height" name="section-company-line-height" value="<?php echo esc_attr(get_option('section-company-line-height', '18')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-company-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-company-font-family" name="section-company-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-company-font-family');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								// Define allowed fonts
								foreach ($allowed_fonts as $font) {
									$selected = selected($current_font, $font, false); // Set selected attribute
									echo "<option value='{$font}' {$selected}>{$font}</option>";
								}
								?>
							</select>
						</div>

					</div>
				</div>
				<!-- END: Company Name -->
				<!-- Section: Username and Post Date -->
				<div class="wp-ui-form-section">

					<h2 class="wp-ui-form-section-title">Username and Post Date</h2>

					<!-- Subsection: Inputs -->
					<div class="wp-ui-form-subsection">

						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-author-date-color" name="section-author-date-color" value="<?php echo esc_attr(get_option('section-author-date-color', '#454545')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-author-date-font-size" name="section-author-date-font-size" value="<?php echo esc_attr(get_option('section-author-date-font-size', '14')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Weight -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-font-weight" class="wp-ui-form-label">Weight:</label>
							<input type="number" id="section-author-date-font-weight" name="section-author-date-font-weight" value="<?php echo esc_attr(get_option('section-author-date-font-weight', '300')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Line Height -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-line-height" class="wp-ui-form-label">Line Height:</label>
							<input type="number" id="section-author-date-line-height" name="section-author-date-line-height" value="<?php echo esc_attr(get_option('section-author-date-line-height', '18')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-author-date-font-family" name="section-author-date-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-author-date-font-family');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								// Define allowed fonts
								foreach ($allowed_fonts as $font) {
									$selected = selected($current_font, $font, false); // Set selected attribute
									echo "<option value='{$font}' {$selected}>{$font}</option>";
								}
								?>
							</select>
						</div>

					</div>

				</div>
				<!-- END: Username and Post Date -->
				<!-- Section: Post Text -->
				<div class="wp-ui-form-section">

					<h2 class="wp-ui-form-section-title">Post Text</h2>

					<!-- Subsection: Inputs -->
					<div class="wp-ui-form-subsection">

						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-body-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-body-color" name="section-body-color" value="<?php echo esc_attr(get_option('section-body-color', '#adb5bd')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-body-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-body-font-size" name="section-body-font-size" value="<?php echo esc_attr(get_option('section-body-font-size', '16')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Max Number of Lines -->
						<div class="wp-ui-form-group">
							<label for="section-body-webkit-line-clamp" class="wp-ui-form-label">Max Number of Lines:</label>
							<input type="number" id="section-body-webkit-line-clamp" name="section-body-webkit-line-clamp" value="<?php echo esc_attr(get_option('section-body-webkit-line-clamp', '5')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-body-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-body-font-family" name="section-body-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-body-font-family');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								// Define allowed fonts
								foreach ($allowed_fonts as $font) {
									$selected = selected($current_font, $font, false); // Set selected attribute
									echo "<option value='{$font}' {$selected}>{$font}</option>";
								}
								?>
							</select>
						</div>


					</div>

				</div>
				<!-- END: Post Text -->
				<!-- Section: Post Interactions and Comments -->
				<div class="wp-ui-form-section">

					<h2 class="wp-ui-form-section-title">Post Interactions and Comments</h2>

					<!-- Subsection: Inputs -->
					<div class="wp-ui-form-subsection">

						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-interactions-color" name="section-interactions-color" value="<?php echo esc_attr(get_option('section-interactions-color', '#454545')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-interactions-font-size" name="section-interactions-font-size" value="<?php echo esc_attr(get_option('section-interactions-font-size', '14')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Weight -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-font-weight" class="wp-ui-form-label">Weight:</label>
							<input type="number" id="section-interactions-font-weight" name="section-interactions-font-weight" value="<?php echo esc_attr(get_option('section-interactions-font-weight', '300')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Line Height -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-line-height" class="wp-ui-form-label">Line Height:</label>
							<input type="number" id="section-interactions-line-height" name="section-interactions-line-height" value="<?php echo esc_attr(get_option('section-interactions-line-height', '18')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-interactions-font-family" name="section-interactions-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-interactions-font-family');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								// Define allowed fonts
								foreach ($allowed_fonts as $font) {
									$selected = selected($current_font, $font, false); // Set selected attribute
									echo "<option value='{$font}' {$selected}>{$font}</option>";
								}
								?>
							</select>
						</div>

					</div>

				</div>
				<!-- END: Post Interactions and Comments -->



				<?php wp_nonce_field('handle_form_submission_action', 'handle_form_submission_nonce'); ?>

				<!-- Your submit button -->
				<?php submit_button(); ?>
			</form>

		</div>

		<!-- Right-half: Preview Pane -->
		<div style="flex: 1; display: flex; justify-content: center; align-items: center;">

			<!-- TODO: Add the preview pane here -->
			<div id="post-preview" style="text-align: center;">
				<!-- Your preview post goes here. For example: -->
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

// Function to handle form submission
function handle_form_submission()
{
	// Check if user has the necessary permissions
	if (current_user_can('manage_options')) {
		// Check for nonce security
		check_admin_referer('handle_form_submission_action', 'handle_form_submission_nonce');

		// Predefined list of font families for validation
		$allowed_fonts = array(
			'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
			'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
			'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
		);
		// Update this list based on your application

		// Capture and update form submissions for the "Company Info" section
		if (isset($_POST['section-company-color'])) {
			$color_value = sanitize_text_field($_POST['section-company-color']);
			if (preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $color_value)) {
				update_option('section-company-color', $color_value);
			}
		}

		if (isset($_POST['section-company-font-size'])) {
			$font_size = intval($_POST['section-company-font-size']);
			if ($font_size >= 1 && $font_size <= 100) {
				update_option('section-company-font-size', $font_size);
			}
		}

		if (isset($_POST['section-company-font-weight'])) {
			$font_weight = intval($_POST['section-company-font-weight']);
			if ($font_weight >= 100 && $font_weight <= 900) {
				update_option('section-company-font-weight', $font_weight);
			}
		}

		if (isset($_POST['section-company-line-height'])) {
			$line_height = intval($_POST['section-company-line-height']);
			if ($line_height >= 1 && $line_height <= 100) {
				update_option('section-company-line-height', $line_height);
			}
		}

		if (isset($_POST['section-company-font-family'])) {
			$font_family = sanitize_text_field($_POST['section-company-font-family']);
			if (in_array($font_family, $allowed_fonts)) {
				update_option('section-company-font-family', $font_family);
			}
		}


		// Capture and update form submissions for the "Username and Post Date" section
		if (isset($_POST['section-author-date-color'])) {
			$color_value = sanitize_text_field($_POST['section-author-date-color']);
			if (preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $color_value)) {
				update_option('section-author-date-color', $color_value);
			}
		}

		if (isset($_POST['section-author-date-font-size'])) {
			$font_size = intval($_POST['section-author-date-font-size']);
			if ($font_size >= 1 && $font_size <= 100) {
				update_option('section-author-date-font-size', $font_size);
			}
		}

		if (isset($_POST['section-author-date-font-weight'])) {
			$font_weight = intval($_POST['section-author-date-font-weight']);
			if ($font_weight >= 100 && $font_weight <= 900) {
				update_option('section-author-date-font-weight', $font_weight);
			}
		}

		if (isset($_POST['section-author-date-line-height'])) {
			$line_height = intval($_POST['section-author-date-line-height']);
			if ($line_height >= 1 && $line_height <= 100) {
				update_option('section-author-date-line-height', $line_height);
			}
		}

		if (isset($_POST['section-author-date-font-family'])) {
			$font_family = sanitize_text_field($_POST['section-author-date-font-family']);
			if (in_array($font_family, $allowed_fonts)) {
				update_option('section-author-date-font-family', $font_family);
			}
		}

		// Capture and update form submissions for the "Post Text" section
		if (isset($_POST['section-body-color'])) {
			$color_value = sanitize_text_field($_POST['section-body-color']);
			if (preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $color_value)) {
				update_option('section-body-color', $color_value);
			}
		}

		if (isset($_POST['section-body-font-size'])) {
			$font_size = intval($_POST['section-body-font-size']);
			if ($font_size >= 1 && $font_size <= 100) {
				update_option('section-body-font-size', $font_size);
			}
		}

		if (isset($_POST['section-body-webkit-line-clamp'])) {
			$line_clamp = intval($_POST['section-body-webkit-line-clamp']);
			if ($line_clamp >= 1 && $line_clamp <= 100) {
				update_option('section-body-webkit-line-clamp', $line_clamp);
			}
		}

		if (isset($_POST['section-body-font-family'])) {
			$font_family = sanitize_text_field($_POST['section-body-font-family']);
			if (in_array($font_family, $allowed_fonts)) {
				update_option('section-body-font-family', $font_family);
			}
		}

		// Capture and update form submissions for the "Post Interactions and Comments" section
		if (isset($_POST['section-interactions-color'])) {
			$color_value = sanitize_text_field($_POST['section-interactions-color']);
			if (preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $color_value)) {
				update_option('section-interactions-color', $color_value);
			}
		}

		if (isset($_POST['section-interactions-font-size'])) {
			$font_size = intval($_POST['section-interactions-font-size']);
			if ($font_size >= 1 && $font_size <= 100) {
				update_option('section-interactions-font-size', $font_size);
			}
		}

		if (isset($_POST['section-interactions-font-weight'])) {
			$font_weight = intval($_POST['section-interactions-font-weight']);
			if ($font_weight >= 100 && $font_weight <= 900) {
				update_option('section-interactions-font-weight', $font_weight);
			}
		}

		if (isset($_POST['section-interactions-line-height'])) {
			$line_height = intval($_POST['section-interactions-line-height']);
			if ($line_height >= 1 && $line_height <= 100) {
				update_option('section-interactions-line-height', $line_height);
			}
		}

		if (isset($_POST['section-interactions-font-family'])) {
			$font_family = sanitize_text_field($_POST['section-interactions-font-family']);
			if (in_array($font_family, $allowed_fonts)) {
				update_option('section-interactions-font-family', $font_family);
			}
		}
		// Redirect back to the form page with a success message
		wp_redirect(add_query_arg('status', 'success', wp_get_referer()));
	} else {
		wp_die('Unauthorized', 'You do not have the required permissions to update settings.');
	}
}
// Attach the handle_form_submission() function to an appropriate WordPress hook
add_action('admin_post_handle_form_submission', 'handle_form_submission');
