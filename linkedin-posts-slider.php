<?php

/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

// Define plugin paths and URLs

define('LPS_PLUGIN_PATH', plugin_dir_path(__FILE__));

define('LPS_PLUGIN_URL', plugin_dir_url(__FILE__));

define('LPS_PLUGIN_BASENAME', plugin_basename(__FILE__));



// Include necessary files

require_once LPS_PLUGIN_PATH .  'src/db-table-creation.php';

require_once LPS_PLUGIN_PATH .  'src/options-page.php';

require_once LPS_PLUGIN_PATH .  'src/scrapper-options-page.php';

// Include the slider widget class at the right time
add_action('elementor/widgets/widgets_registered', function () {
  require_once LPS_PLUGIN_PATH .  'src/slider-widget.php';
});

require_once LPS_PLUGIN_PATH .  'src/posts-table-page.php'; // Make sure this path is correct.



// Activation hook for creating custom tables and setting up default values

register_activation_hook(__FILE__, 'linkedin_posts_slider_activation');



/**

 * Activation callback function.

 */

function  linkedin_posts_slider_activation()

{

  linkedin_posts_slider_create_table();

  linkedin_posts_slider_populate_defaults();
}

  

// Code for creating tables and populating defaults is located in 'src/db-table-creation.php'