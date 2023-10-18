<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Add an options page for the Linkedin Posts Slider widget in the WordPress admin menu.
function linkedin_posts_slider_options_page()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

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
    });
	</script>
    ';
	EOT;
	// Add the CSS code to style the slider
	echo '
	<style>
    .section-company {
        color: ' . get_option('section-company-color', '#454545') . ';
        font-size: ' . get_option('section-company-font-size', '16px') . ';
        font-family: ' . get_option('section-company-font-family', '"Titillium Web"') . ';
        line-height: ' . get_option('section-company-line-height', '21px') . ';
    }
    .section-author-date {
        color: ' . get_option('section-author-date-color', '#454545') . ';
        font-size: ' . get_option('section-author-date-font-size', '14px') . ';
        font-family: ' . get_option('section-author-date-font-family', '"Titillium Web"') . ';
        font-weight: ' . get_option('section-author-date-font-weight', '300') . ';
        line-height: ' . get_option('section-author-date-line-height', '18px') . ';
    }
    .section-body {
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
    }
	</style>
	';

	// Add the HTML code for the form fields
	echo '
    <form method="post" action="options.php">
        <label for="section-company-color">Company info section color:</label>
        <input type="color" id="section-company-color" name="section-company-color" value="' . esc_attr(get_option('section-company-color', '#454545')) . '">
        
        <label for="section-company-font-size">Font size:</label>
        <input type="number" id="section-company-font-size" name="section-company-font-size" value="' . esc_attr(get_option('section-company-font-size', '16')) . '">
        
        <label for="section-company-font-family">Font family:</label>
        <select id="section-company-font-family" name="section-company-font-family">
            <option value="' . esc_attr(get_option('section-company-font-family', 'Titillium Web')) . '">Titillium Web</option>
            <!-- Add other font options here -->
        </select>
        
        <label for="section-company-line-height">Line height:</label>
        <input type="number" id="section-company-line-height" name="section-company-line-height" value="' . esc_attr(get_option('section-company-line-height', '21')) . '">
        
		<label for="section-author-date-color">Author username and date section color:</label>
		<input type="color" id="section-author-date-color" name="section-author-date-color" value="' . esc_attr(get_option('section-author-date-color', '#454545')) . '">

		<label for="section-author-date-font-size">Font size:</label>
		<input type="number" id="section-author-date-font-size" name="section-author-date-font-size" value="' . esc_attr(get_option('section-author-date-font-size', '14')) . '">

		<label for="section-author-date-font-family">Font family:</label>
		<select id="section-author-date-font-family" name="section-author-date-font-family">
			<option value="Titillium Web" ' . (get_option('section-author-date-font-family') == 'Titillium Web' ? 'selected' : '') . '>Titillium Web</option>
			<!-- Add other font options here -->
		</select>

		<label for="section-author-date-font-weight">Font weight:</label>
		<input type="number" id="section-author-date-font-weight" name="section-author-date-font-weight" value="' . esc_attr(get_option('section-author-date-font-weight', '300')) . '">

		<label for="section-author-date-line-height">Line height:</label>
		<input type="text" id="section-author-date-line-height" name="section-author-date-line-height" value="' . esc_attr(get_option('section-author-date-line-height', '18px')) . '">
        
    	<form method="post" action="options.php">
        <label for="section-company-color">Company info section color:</label>
        <input type="color" id="section-company-color" name="section-company-color" value="' . esc_attr(get_option('section-company-color', '#454545')) . '">

        <label for="section-body-color">Post text section color:</label>
        <input type="color" id="section-body-color" name="section-body-color" value="' . esc_attr(get_option('section-body-color', '#adb5bd')) . '">

        <label for="section-body-font-size">Post text section font size:</label>
        <input type="number" id="section-body-font-size" name="section-body-font-size" value="' . esc_attr(get_option('section-body-font-size', '16')) . '">

        <label for="section-body-font-family">Post text section font family:</label>
        <input type="text" id="section-body-font-family" name="section-body-font-family" value="' . esc_attr(get_option('section-body-font-family', 'Titillium Web')) . '">

        <label for="section-body-line-clamp">Post text section line clamp:</label>
        <input type="number" id="section-body-line-clamp" name="section-body-line-clamp" value="' . esc_attr(get_option('section-body-line-clamp', '5')) . '">

		<label for="section-interactions-color">Post interactions section color:</label>
		<input type="color" id="section-interactions-color" name="section-interactions-color" value="' . esc_attr(get_option('section-interactions-color', '#454545')) . '">

		<label for="section-interactions-font-size">Font size:</label>
		<input type="number" id="section-interactions-font-size" name="section-interactions-font-size" value="' . esc_attr(get_option('section-interactions-font-size', '14')) . '">

		<label for="section-interactions-font-family">Font family:</label>
		<select id="section-interactions-font-family" name="section-interactions-font-family">
			<option value="Titillium Web" ' . (get_option('section-interactions-font-family') == 'Titillium Web' ? 'selected' : '') . '>Titillium Web</option>
			// Add other font options here
		</select>

		<label for="section-interactions-font-weight">Font weight:</label>
		<input type="number" id="section-interactions-font-weight" name="section-interactions-font-weight" value="' . esc_attr(get_option('section-interactions-font-weight', '300')) . '">

		<label for="section-interactions-line-height">Line height:</label>
		<input type="text" id="section-interactions-line-height" name="section-interactions-line-height" value="' . esc_attr(get_option('section-interactions-line-height', '18px')) . '">
        ' . submit_button() . '
    </form>';
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
