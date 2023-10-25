<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

/**
 * TODO: add an options page to manage the published posts's order and the syncing.
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}


function enqueue_custom_scripts_and_styles()
{
  wp_enqueue_style('custom-style', plugins_url('style.css', __FILE__));
  wp_enqueue_script('custom-script', plugins_url('script.js', __FILE__), array('jquery', 'jquery-ui-sortable'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts_and_styles');


// Include the new files
require_once plugin_dir_path(__FILE__) . 'src/widget-registration.php';
require_once plugin_dir_path(__FILE__) . 'src/options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/db-table-creation.php';
require_once plugin_dir_path(__FILE__) . 'src/cron-event.php';
require_once plugin_dir_path(__FILE__) . 'src/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'src/ajax-actions.php';
require_once plugin_dir_path(__FILE__) . 'src/linkedin-posts-syncing.php';

// Register the activation hook for table creation
register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');


/**
 * Display the admin table page for managing LinkedIn posts.
 *
 * @return void
 */
/**
 * Display the admin table page for managing LinkedIn posts.
 *
 * @return void
 */
function linkedin_posts_slider_admin_table_page()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'linkedin_posts';

  // Fetch all rows
  $rows = $wpdb->get_results("SELECT * FROM $table_name ORDER BY post_order ASC");

  // Start output
  ob_start();
?>
  <div class="wrap">
    <h1 style="text-align:center;"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <table class="widefat fixed" cellspacing="0">
      <thead>
        <tr>
          <th scope="col" class="manage-column">ID</th>
          <th scope="col" class="manage-column">Thumbnail</th>
          <th scope="col" class="manage-column">Age</th>
          <th scope="col" class="manage-column">Post Text</th>
          <th scope="col" class="manage-column">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row) : ?>
          <tr>
            <td><?php echo esc_html($row->id); ?></td>
            <td>
              <?php
              $images = json_decode($row->images);
              if (!empty($images)) {
                echo '<img src="' . esc_url($images[0]) . '" alt="" width="50" height="50" />';
              }
              ?>
            </td>
            <td><?php echo esc_html($row->age); ?></td>
            <td><?php echo wp_trim_words(esc_html($row->post_text), 10, '...'); ?></td>
            <td>
              <form method="post" style="display:inline;">
                <?php wp_nonce_field('linkedin_delete_action', 'linkedin_delete_nonce'); ?>
                <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">
                <input type="submit" value="Delete" class="delete-button" data-id="<?php echo esc_attr($row->id); ?>">
              </form>

              <button class="publish-button" data-id="<?php echo esc_attr($row->id); ?>" data-published="<?php echo esc_attr($row->published); ?>">
                <?php echo $row->published ? 'Unpublish' : 'Publish'; ?>
              </button>

            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php
  // Output buffer
  echo ob_get_clean();
}
