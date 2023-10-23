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

	// Register and enqueue the stylesheet
	//wp_register_style('slider-style', plugins_url('../public/styles.css', __FILE__));
	//wp_enqueue_style('slider-style');
	wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));
	wp_enqueue_style('swiper-style');

	// Add the jQuery code to handle the changes
	echo <<<'EOT'
	<script>
	jQuery(document).ready(function($) {
		// Company info section
		$("#section-company-color").on("change", function() {
			$(".section-company").css("color", $(this).val());
		});
		$("#section-company-font-size").on("change", function() {
			$(".section-company").css("font-size", $(this).val() + 'px');
		});
		$("#section-company-font-family").on("change", function() {
			$(".section-company").css("font-family", $(this).val());
		});
		$("#section-company-line-height").on("change", function() {
			$(".section-company").css("line-height", $(this).val() + 'px');
		});

		// Author username and date section
		$("#section-author-date-color").on("change", function() {
			$(".section-author-date").css("color", $(this).val());
		});
		$("#section-author-date-font-size").on("change", function() {
			$(".section-author-date").css("font-size", $(this).val() + 'px');
		});
		$("#section-author-date-font-family").on("change", function() {
			$(".section-author-date").css("font-family", $(this).val());
		});
		$("#section-author-date-font-weight").on("change", function() {
			$(".section-author-date").css("font-weight", $(this).val());
		});
		$("#section-author-date-line-height").on("change", function() {
			$(".section-author-date").css("line-height", $(this).val() + 'px');
		});

		// Post text section
		$("#section-body-color").on("change", function() {
			$(".section-body").css("color", $(this).val());
		});
		$("#section-body-font-size").on("change", function() {
			$(".section-body").css("font-size", $(this).val() + 'px');
		});
		$("#section-body-font-family").on("change", function() {
			$(".section-body").css("font-family", $(this).val());
		});
		$("#section-body-webkit-line-clamp").on("change", function() {
			$(".section-body").css("-webkit-line-clamp", $(this).val());
		});

		// Post interactions section
		$("#section-interactions-color").on("change", function() {
			$(".section-interactions").css("color", $(this).val());
		});
		$("#section-interactions-font-size").on("change", function() {
			$(".section-interactions").css("font-size", $(this).val() + 'px');
		});
		$("#section-interactions-font-family").on("change", function() {
			$(".section-interactions").css("font-family", $(this).val());
		});
		$("#section-interactions-font-weight").on("change", function() {
			$(".section-interactions").css("font-weight", $(this).val());
		});
		$("#section-interactions-line-height").on("change", function() {
		$(".section-interactions").css("line-height", $(this).val() + 'px');
		}); 

		jQuery(document).ready(function($) {
			$("form").on("submit", function(event) {
				event.preventDefault();
		
				var formData = $(this).serialize();
		
				$.ajax({
					url: ajaxurl, // This is a variable automatically defined by WordPress that contains the URL to wp-admin/admin-ajax.php
					type: 'POST',
					data: {
						//TODO: Add the options saving logic and steps here
						action: 'save_form_data', // This should match the action hook in your PHP code
						nonce: your_nonce, // Replace this with the actual nonce
						form_data: $(this).serialize() // This serializes the form data
					},
					success: function(data) {
						// Handle the server response here
					}
				});
			});
		});

		// Handle form field changes
		$("form input, form select").on("change", function() {
			// Get the form data
			var formData = $("form").serialize();

			// Send an AJAX request to the server
			$.ajax({
				url: "path/to/your/server/script.php",
				type: "POST",
				data: formData,
				success: function(data) {
					// Update the post preview
					$("#post-preview").html(data);
				}
			});
		});
	});
	</script>
	EOT;
	// Add the CSS code to style the slider
	echo '
	<style>
	body {
		font-family: Arial, sans-serif;
	}
	  
	.swiper-slide{
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		text-align: center;
		background-color: #ffffff;
		box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.16);
		height: 100%;
		margin-bottom: 4px;
	}
	  
	.section-company {
		color: ' . get_option('section-company-color', '#454545') . ';
		font-size: ' . get_option('section-company-font-size', '16px') . ';
		font-family: ' . get_option('section-company-font-family', '"Titillium Web"') . ';
		line-height: ' . get_option('section-company-line-height', '21px') . ';
		font-weight: ' . get_option('section-company-font-weight', '400') . ';
		padding-top: 10px;
	}
	.section-author-date {
		color: ' . get_option('section-author-date-color', '#454545') . ';
		font-size: ' . get_option('section-author-date-font-size', '14px') . ';
		font-family: ' . get_option('section-author-date-font-family', '"Titillium Web"') . ';
		font-weight: ' . get_option('section-author-date-font-weight', '300') . ';
		line-height: ' . get_option('section-author-date-line-height', '18px') . ';

		padding-top: 10px;
		gap: 3px;
  		margin-bottom: 10px;
	}
	.section-body {

		text-align: center;
  		overflow: hidden;
  		-webkit-box-orient: vertical;
		display: -webkit-box;
		margin-right: 10px;
		margin-left: 10px;
		margin-bottom: 60px;
		color: ' . get_option('section-body-color', '#adb5bd') . ';
		font-size: ' . get_option('section-body-font-size', '16px') . ';
		font-family: ' . get_option('section-body-font-family', '"Titillium Web"') . ';
		-webkit-line-clamp: ' . get_option('section-body-webkit-line-clamp', '5') . ';
	}
	.section-interactions {
		color: ' . get_option('section-interactions-color', '#454545') . ';
		font-size: ' . get_option('section-interactions-font-size', '14px') . ';
		font-family: ' . get_option('section-interactions-font-family', '"Titillium Web"') . ';
		font-weight: ' . get_option('section-interactions-font-weight', '300') . ';
		line-height: ' . get_option('section-interactions-line-height', '18px') . ';

		padding-right: 7px;
  		width: 100%;
  		position: absolute;
  		bottom: 7px;
		display: flex;
		align-items: center;
		justify-content: center;
		text-align: center;
		gap: 5px;
	}
	.li-icon-white {

		width: 100%;
		position: absolute;
		top: 5px;
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: center;
		text-align: center;
	}
	.li-post-card {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		text-align: center;
		background-color: #ffffff;
		box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.16);
	}
	  
	  .img-container {
		z-index: -1;
		min-height: 350px;
		display: flex;
		flex-direction: column;
	}
	  
	.info-container {
		z-index: -1;
		height: 40%;
		max-height: 40%;
		display: flex;
		flex-direction: column;
	}
	.li-single-img {
		flex-grow: 1;
		height: 100%;
		width: 100%;
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
	}
	.li-img-two {
		flex-grow: 1;
		height: 50%;
		width: 100%;
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
	}
	.li-img-three-main {
		flex-grow: 1;
		height: 70%;
		width: 100%;
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
	}
	.li-img-three-sec-container {
		flex-grow: 1;
		width: 100%;
		display: grid;
		grid-auto-flow: column;
	}
	  .li-img-three-sec {
		height: 100%;
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
	}
	.li-author-img {
		margin-top: -15px;
		margin-right: auto;
		margin-left: auto;
		width: 60px;
		height: 60px;
		border-radius: 10000px;
		box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.16);
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
	}
	.options-form {
		float: left;
		width: 50%;
	}
	.form-field{
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: left;
		margin-top: 20px;
	}
	.form-section-title{
		font-size: 18px;
		font-weight: 500;
		margin-bottom: 10px;
	}
	.form-subsection-title{
		font-size: 15px;
		font-weight: 200;
		margin-bottom: 10px;
	}
	.form-subsection-inputs-row{
		display: grid;
		grid-template-columns: 20px 20px 20px;
  		grid-template-rows: auto;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 10px;
	}

	
	.right-section {
		float: right;
		width: 50%;
		height: 100%;
		display: flex;
		align-items: center;
  		justify-content: center;
		flex-direction: column;

	}
	.post-preview-class{
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		max-width: 400px;
		margin-top: 20vh;
	}
	</style>
	';



	echo <<<'EOT'
	<div class="right-section">
		<div id="post-preview" class="post-preview-class">
			<div class="swiper-slide">
			<div class="li-icon-white">
			<svg
				style="
				width: 30px;
				height: 30px;
				overflow: visible;
				fill: rgb(255, 255, 255);
				"
				viewBox="0 0 448 512"
			>
				<path
				d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"
				></path>
			</svg>
			</div>
			<div class="img-container">
			<div
				class="li-single-img"
				style="
				background-image: url('https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4');
				"
			></div>
			</div>
		
			<div class="info-container">
			<div
				class="li-author-img"
				style="
				background-image: url('https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1705536000&v=beta&t=9PVwxirZIj7Pgh68ihS7bA_UscLia5XiJy9llH9Q_PA');
				"
			></div>
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
				<span
				><svg
					style="
					width: 24px;
					height: 24px;
					overflow: visible;
					fill: rgb(0, 122, 255);
					"
					viewBox="0 0 24 24"
				>
					<path fill="none" d="M0 0h24v24H0z"></path>
					<path
					d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z"
					opacity=".3"
					></path>
					<path
					d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"
					></path>
					<path
					d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"
					></path></svg
				></span>
				<span class="li-post-reactions">74 . </span>
				<span class="li-post-comments">84 comments</span>
			</div>
			</div>
		</div>
	  
		</div>
	</div>
	EOT;

	/**
	 * TODO: Style the form fields using CSS.
	 * TODO[]: Add gaps between the form fields.
	 * TODO[]: edit labels and add tooltips.
	 * TODO[]: add loader when submitting the form.
	 * TODO[]: rearrange the small form fields 2 in each row.
	 * TODO[]: add a reset button.
	 */

	// Add the HTML code for the form fields
	echo '
	<form method="post" action="options-page.php" class="options-form">

		<div class="form-field">
		
			<div class="form-section-title">
				Company Name: 
			</div>
			<div class="form-subsection-title">
				Color | Size | Weight | Line Height:
			</div>
			<div class="form-subsection-inputs-row">
				<input type="color" id="section-company-color" name="section-company-color" value="' . esc_attr(get_option('section-company-color', '#454545')) . '">
				<input type="number" class="number-input" id="section-company-font-size" name="section-company-font-size" value="' . esc_attr(get_option('section-company-font-size', '16')) . '">
				<input type="number" class="number-input" id="section-company-font-weight" name="section-company-font-weight" value="' . esc_attr(get_option('section-company-font-weight', '400')) . '">
				<input type="number" class="number-input" id="section-company-line-height" name="section-company-line-height" value="' . esc_attr(get_option('section-company-line-height', '18px')) . '">
			</div>
			<div class="form-subsection-title">
				Font Family:
			</div>
			<div class="form-subsection-inputs-row">
				<select id="section-company-font-family" name="section-company-font-family">
					<option value="' . esc_attr(get_option('section-company-font-family', 'Titillium Web')) . '">Titillium Web</option>
					<!-- //TODO: Add other font options here -->
				</select>
			</div>
			
		</div>

		<div class="form-field">
		
			<div class="form-section-title">
				Username and post date:
			</div>
			<div class="form-subsection-title">
				Color | Size | Weight | Line Height:
			</div>
			<div class="form-subsection-inputs-row">
				<input type="color" id="section-author-date-color" name="section-author-date-color" value="' . esc_attr(get_option('section-author-date-color', '#454545')) . '">
				<input type="number" class="number-input" id="section-author-date-font-size" name="section-author-date-font-size" value="' . esc_attr(get_option('section-author-date-font-size', '14')) . '">
				<input type="number" class="number-input" id="section-author-date-font-weight" name="section-author-date-font-weight" value="' . esc_attr(get_option('section-author-date-font-weight', '300')) . '">
				<input type="number" class="number-input" id="section-author-date-line-height" name="section-author-date-line-height" value="' . esc_attr(get_option('section-author-date-line-height', '18px')) . '">
			</div>
			<div class="form-subsection-title">
				Font Family:
			</div>
			<div class="form-subsection-inputs-row">
				<select id="section-author-date-font-family" name="section-author-date-font-family">
					<option value="Titillium Web" ' . (get_option('section-author-date-font-family') == 'Titillium Web' ? 'selected' : '') . '>Titillium Web</option>
					<!-- //TODO: Add other font options here -->
				</select>
			</div>
			
		</div>

		<div class="form-field">
		
			<div class="form-section-title">
				Post text:
			</div>
			<div class="form-subsection-title">
			Color | Size | Max no. of Lines:
			</div>
			<div class="form-subsection-inputs-row">
				<input type="color" id="section-body-color" name="section-body-color" value="' . esc_attr(get_option('section-body-color', '#adb5bd')) . '">
				<input type="number" class="number-input" id="section-body-font-size" name="section-body-font-size" value="' . esc_attr(get_option('section-body-font-size', '16')) . '">
				<input type="number" class="number-input" id="section-body-line-clamp" name="section-body-line-clamp" value="' . esc_attr(get_option('section-body-line-clamp', '5')) . '">
			</div>
			<div class="form-subsection-title">
				Font Family:
			</div>
			<div class="form-subsection-inputs-row">
				<select id="section-body-font-family" name="section-body-font-family">
					<option value="Titillium Web" ' . esc_attr(get_option('section-body-font-family', 'Titillium Web')) . '>Titillium Web</option>
					<!-- //TODO: Add other font options here -->
				</select>
			</div>
			
			
		</div>

		<div class="form-field">
		
			<div class="form-section-title">
				Post interactions and comments:
			</div>
			<div class="form-subsection-title">
				Color | Size | Weight | Line Height:
			</div>
			<div class="form-subsection-inputs-row">
				<input type="color" id="section-interactions-color" name="section-interactions-color" value="' . esc_attr(get_option('section-interactions-color', '#454545')) . '">
				<input type="number" class="number-input" id="section-interactions-font-size" name="section-interactions-font-size" value="' . esc_attr(get_option('section-interactions-font-size', '14')) . '">
				<input type="number" class="number-input" id="section-interactions-font-weight" name="section-interactions-font-weight" value="' . esc_attr(get_option('section-interactions-font-weight', '300')) . '">
				<input type="number" class="number-input" id="section-interactions-line-height" name="section-interactions-line-height" value="' . esc_attr(get_option('section-interactions-line-height', '18px')) . '">
			</div>
			<div class="form-subsection-title">
				Font Family:
			</div>
			<div class="form-subsection-inputs-row">
				<select id="section-interactions-font-family" name="section-interactions-font-family">
					<option value="Titillium Web" ' . (get_option('section-interactions-font-family') == 'Titillium Web' ? 'selected' : '') . '>Titillium Web</option>
					<!-- //TODO: Add other font options here -->
				</select>
			</div>
			
		</div>';
	echo submit_button() . ' </form>';
}
