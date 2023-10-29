<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}


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


function linkedin_posts_slider_register_settings()
{
	// Company info section
	register_setting('linkedin-posts-slider', 'section-company-color');
	register_setting('linkedin-posts-slider', 'section-company-font-size');
	register_setting('linkedin-posts-slider', 'section-company-font-family');
	register_setting('linkedin-posts-slider', 'section-company-line-height');

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

	// Post interactions section
	register_setting('linkedin-posts-slider', 'section-interactions-color');
	register_setting('linkedin-posts-slider', 'section-interactions-font-size');
	register_setting('linkedin-posts-slider', 'section-interactions-font-family');
	register_setting('linkedin-posts-slider', 'section-interactions-font-weight');
	register_setting('linkedin-posts-slider', 'section-interactions-line-height');
}
add_action('admin_init', 'linkedin_posts_slider_register_settings');

// Add an options page for the Linkedin Posts Slider widget in the WordPress admin menu.
function linkedin_posts_slider_options_page()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}


	// Add the HTML code for the form fields

?>
	<div class="wrap">
		<h1>Style Live Editor</h1>
		<div class="right-section">
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
		<form method="post" action="options.php" class="options-form">
			<?php settings_fields('linkedin-posts-slider'); ?>
			<?php do_settings_sections('linkedin-posts-slider'); ?>

			<div class="form-field">

				<div class="form-section-title">
					Company Name:
				</div>

				<div class="form-subsection-inputs-row">
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Color:
						</div>
						<div class="vertical-form-field">
							<input type="color" id="section-company-color" name="section-company-color" value="<?php echo esc_attr(get_option('section-company-color', '#454545')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Size:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-company-font-size" name="section-company-font-size" value="<?php echo esc_attr(get_option('section-company-font-size', '16')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Weight:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-company-font-weight" name="section-company-font-weight" value="<?php echo esc_attr(get_option('section-company-font-weight', '400')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Line Height:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-company-line-height" name="section-company-line-height" value="<?php echo esc_attr(get_option('section-company-line-height', '18px')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Font Family:
						</div>
						<div class="vertical-form-field">
							<select id="section-company-font-family" name="section-company-font-family">
								<option value="<?php echo esc_attr(get_option('section-company-font-family', 'Titillium Web')); ?>">Titillium Web</option>
								<!-- //TODO: Add other font options here -->
							</select>
						</div>
					</div>
				</div>


			</div>
			<div class="form-field">

				<div class="form-section-title">
					Username and post date:
				</div>
				<div class="form-subsection-inputs-row">
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Color:
						</div>
						<div class="vertical-form-field">
							<input type="color" id="section-author-date-color" name="section-author-date-color" value="<?php echo esc_attr(get_option('section-author-date-color', '#454545')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Size:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-author-date-font-size" name="section-author-date-font-size" value="<?php echo esc_attr(get_option('section-author-date-font-size', '14')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Weight:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-author-date-font-weight" name="section-author-date-font-weight" value="<?php echo esc_attr(get_option('section-author-date-font-weight', '300')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Line Height:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-author-date-line-height" name="section-author-date-line-height" value="<?php echo esc_attr(get_option('section-author-date-line-height', '18px')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Font Family:
						</div>
						<div class="vertical-form-field">
							<select id="section-author-date-font-family" name="section-author-date-font-family">
								<option value="Titillium Web" <?php echo esc_attr(get_option('section-author-date-font-family') == 'Titillium Web' ? 'selected' : ''); ?>>Titillium Web</option>
								<!-- //TODO: Add other font options here -->
							</select>
						</div>
					</div>
				</div>


			</div>

			<div class="form-field">

				<div class="form-section-title">
					Post text:
				</div>

				<div class="form-subsection-inputs-row">
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Color:
						</div>
						<div class="vertical-form-field">
							<input type="color" id="section-body-color" name="section-body-color" value="<?php echo esc_attr(get_option('section-body-color', '#adb5bd')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Size:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-body-font-size" name="section-body-font-size" value="<?php echo esc_attr(get_option('section-body-font-size', '16')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Max no. of Lines:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-body-webkit-line-clamp" name="section-body-webkit-line-clamp" value="<?php echo esc_attr(get_option('section-body-webkit-line-clamp', '5')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Font Family:
						</div>
						<div class="vertical-form-field">
							<select id="section-body-font-family" name="section-body-font-family">
								<option value="Titillium Web" <?php echo esc_attr(get_option('section-body-font-family', 'Titillium Web')); ?>>Titillium Web</option>
								<!-- //TODO: Add other font options here -->
							</select>
						</div>
					</div>
				</div>



			</div>

			<div class="form-field">

				<div class="form-section-title">
					Post interactions and comments:
				</div>
				<div class="form-subsection-inputs-row">
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Color:
						</div>
						<div class="vertical-form-field">
							<input type="color" id="section-interactions-color" name="section-interactions-color" value="<?php echo esc_attr(get_option('section-interactions-color', '#454545')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Size:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-interactions-font-size" name="section-interactions-font-size" value="<?php echo esc_attr(get_option('section-interactions-font-size', '14')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Weight:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-interactions-font-weight" name="section-interactions-font-weight" value="<?php echo esc_attr(get_option('section-interactions-font-weight', '300')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Line Height:
						</div>
						<div class="vertical-form-field">
							<input type="number" class="number-input" id="section-interactions-line-height" name="section-interactions-line-height" value="<?php echo esc_attr(get_option('section-interactions-line-height', '18px')); ?>">
						</div>
					</div>
					<div class="vertical-form-group">
						<div class="vertical-form-title">
							Font Family:
						</div>
						<div class="vertical-form-field">
							<select id="section-interactions-font-family" name="section-interactions-font-family">
								<option value="Titillium Web" <?php echo esc_attr(get_option('section-interactions-font-family') == 'Titillium Web' ? 'selected' : ''); ?>>Titillium Web</option>
								<!-- //TODO: Add other font options here -->
							</select>
						</div>
					</div>
				</div>


			</div>

	</div>


	<!-- For example: 
	<input type="text" name="section-company-color" value="php echo esc_attr(get_option('section-company-color', '#default-color')); ?>">
	-->
	<?php submit_button(); ?>
	</form>
	</div>
<?php
}
add_action('admin_menu', function () {
	add_options_page('Linkedin Slider Style Settings', 'Linkedin Posts Style Settings', 'manage_options', 'linkedin-posts-slider', 'linkedin_posts_slider_options_page');
});
