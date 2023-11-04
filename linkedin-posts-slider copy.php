<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

function enqueue_custom_scripts_and_styles()
{
  wp_enqueue_style('custom-style', plugins_url('style.css', __FILE__));
  wp_enqueue_script('custom-script', plugins_url('script.js', __FILE__), array('jquery', 'jquery-ui-sortable'), null, true);
  wp_localize_script('custom-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts_and_styles');

// Include the new files
require_once plugin_dir_path(__FILE__) . 'src/widget-registration.php';
require_once plugin_dir_path(__FILE__) . 'src/options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/scrapper-options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/db-table-creation.php';
require_once plugin_dir_path(__FILE__) . 'src/cron-event.php';
require_once plugin_dir_path(__FILE__) . 'src/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'src/ajax-actions.php';
require_once plugin_dir_path(__FILE__) . 'src/linkedin-posts-syncing.php';

// Register the activation hook for table creation
//register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
//register_activation_hook(__FILE__, 'linkedin_slider_settings_create_table');

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
  <script>

  </script>
  <div class="wrap">
    <h1 style="text-align:center;"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <table class="widefat fixed custom-table" cellspacing="0">
      <thead>
        <tr>
          <th scope="col" class="manage-column column-id" hidden>ID</th>
          <th scope="col" class="manage-column">ID</th>
          <th scope="col" class="manage-column">Thumbnail</th>
          <th scope="col" class="manage-column">Age</th>
          <th scope="col" class="manage-column">Post Text</th>
          <th scope="col" class="manage-column">Actions</th>
          <th scope="col" class="manage-column">Order</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row) : ?>
          <tr class="table-row-class">
            <td class="column-id" hidden>
              <span class="row-id" hidden><?php echo esc_html($row->id); ?></span>
            </td>
            <td><?php echo esc_html($row->id); ?></td>
            <td class="thumbnail-cell">
              <?php
              $images = json_decode($row->images);
              if (!empty($images)) {
                echo '<img src="' . esc_url($images[0]) . '" alt="" width="100" height="100" />';  /* Adjust width and height as needed */
              }
              ?>
            </td>
            <td><?php echo esc_html($row->age); ?></td>
            <td><?php echo wp_trim_words(esc_html($row->post_text), 10, '...'); ?></td>
            <td>
              <div class="action-buttons">
                <form method="post" style="display:inline;">
                  <?php //wp_nonce_field('linkedin_delete_action', 'linkedin_delete_nonce'); 
                  ?>
                  <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">
                  <input type="submit" value="Delete" class="delete-button" data-id="<?php echo esc_attr($row->id); ?>">
                </form>

                <button class="publish-button" data-id="<?php echo esc_attr($row->id); ?>" data-published="<?php echo esc_attr($row->published); ?>">
                  <?php echo $row->published ? 'Published' : 'Unpublished'; ?>
                </button>
              </div>


            </td>
            </td>
            <td>
              <div class="up-down-wrapper">
                <button class="up-button">
                  <!-- Up arrow SVG -->
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5L5 12H19L12 5Z" fill="white" />
                  </svg>
                </button>
                <button class="down-button">
                  <!-- Down arrow SVG -->
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19L5 12H19L12 19Z" fill="white" />
                  </svg>
                </button>
              </div>

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

function move_row()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'linkedin_posts';

  $id = intval($_POST['id']);
  $action = $_POST['action'];

  // Step 1: Fetch current post_order and IDs
  $rows = $wpdb->get_results("SELECT id, post_order FROM $table_name ORDER BY post_order ASC");

  // Step 2: Find the index of the row to be moved
  $index = array_search($id, array_column($rows, 'id'));

  if ($index !== false && $index >= 0 && $index < count($rows)) {
    if ($action === 'move_up' && $index > 0) {
      // Step 3: Swap post_order values for moving up
      $swap_index = $index - 1;
    } elseif ($action === 'move_down' && $index < count($rows) - 1) {
      // Step 3: Swap post_order values for moving down
      $swap_index = $index + 1;
    } else {
      wp_send_json_error('Invalid move');
      return;
    }

    // Step 4: Update the database
    $wpdb->query("START TRANSACTION");

    $wpdb->update(
      $table_name,
      array('post_order' => $rows[$swap_index]->post_order),
      array('id' => $id)
    );

    $wpdb->update(
      $table_name,
      array('post_order' => $rows[$index]->post_order),
      array('id' => $rows[$swap_index]->id)
    );

    $wpdb->query("COMMIT");

    wp_send_json_success('Row moved successfully');
  } else {
    wp_send_json_error('Invalid ID or index');
  }
}
add_action('wp_ajax_move_up', 'move_row');
add_action('wp_ajax_move_down', 'move_row');
