<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles specifically for this page
function linkedin_posts_table_enqueue_scripts($hook)
{
    // Check if we are on the posts table page
    if ('toplevel_page_linkedin-posts-table' !== $hook) {
        return;
    }

    wp_enqueue_style('linkedin-posts-table-style', plugins_url('../public/styles.css', __FILE__));
    wp_enqueue_script('linkedin-posts-table-script', plugins_url('../public/script.js', __FILE__), array('jquery'), null, true);

    // Localize the script with new data
    wp_localize_script('linkedin-posts-table-script', 'linkedinPostsTable', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('linkedin_posts_table_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'linkedin_posts_table_enqueue_scripts');

// Add menu item for the posts table page
function linkedin_posts_table_menu()
{
    add_menu_page('LinkedIn Posts Table', 'LinkedIn Posts', 'manage_options', 'linkedin-posts-table', 'linkedin_posts_table_page_display', 'dashicons-list-view');
}
add_action('admin_menu', 'linkedin_posts_table_menu');

// Display function for the posts table page
function linkedin_posts_table_page_display()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'linkedin_posts';

    // Fetch all rows
    $rows = $wpdb->get_results("SELECT * FROM $table_name ORDER BY post_order ASC");

    include plugin_dir_path(__FILE__) . 'partials/posts-table-page-display.php';
}

// Include partials for cleaner code
require_once plugin_dir_path(__FILE__) . 'partials/ajax-actions-handler.php';
