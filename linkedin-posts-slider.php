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

// Include the new files
require_once plugin_dir_path(__FILE__) . 'src/widget-registration.php';
require_once plugin_dir_path(__FILE__) . 'src/options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/db-table-creation.php';
require_once plugin_dir_path(__FILE__) . 'src/cron-event.php';
require_once plugin_dir_path(__FILE__) . 'src/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'src/ajax-actions.php';
require_once plugin_dir_path(__FILE__) . 'src/linkedin-posts-syncing.php';


// Hook into cron event
add_action('linkedin_posts_sync_event', 'linkedin_posts_sync');


/**
 * Display the admin table page for managing LinkedIn posts.
 *
 * @return void
 */
function linkedin_posts_slider_admin_table_page()
{

  global $wpdb;

  $table_name = $wpdb->prefix . 'linkedin_posts';

  // Check for delete action
  if (isset($_POST['delete']) && isset($_POST['id'])) {
    $wpdb->delete(
      $table_name,
      ['id' => $_POST['id']],
      ['%d']
    );
  }

  // Fetch all rows
  $rows = $wpdb->get_results("SELECT * FROM $table_name");

  // Start output
  ob_start();

?>

  <div class="wrap">

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <style>
      table {
        width: 100%;
        border-collapse: collapse;
      }

      th,
      td {
        border: 1px solid #ddd;
        padding: 8px;
      }

      th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
      }
    </style>

    <table>
      <tr>
        <th>ID</th>
        <th>URL</th>
        <th>Author</th>
        <th>Age</th>
        <th>Copy</th>
        <th>Synced</th>
        <th>Published</th>
        <th>Action</th>
        <th>Sync</th>
      </tr>

      <?php foreach ($rows as $row) : ?>

        <tr>
          <td><?php echo esc_html($row->id); ?></td>
          <td><?php echo esc_html($row->url); ?></td>
          <td><?php echo esc_html($row->author); ?></td>
          <td><?php echo esc_html($row->age); ?></td>
          <td><?php echo esc_html($row->copy); ?></td>
          <td><?php echo esc_html($row->synced); ?></td>
          <td><?php echo esc_html($row->published); ?></td>
          <td>

            <button class="publish-button" data-id="<?php echo esc_attr($row->id); ?>" data-published="<?php echo esc_attr($row->published); ?>" onclick="publishButtonClicked(this)">
              <?php echo $row->published ? 'Unpublish' : 'Publish'; ?>
            </button>

          </td>
          <td>

            <form method="post">
              <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">
              <?php submit_button('Delete', 'delete', 'delete', false); ?>
            </form>

          </td>
          <td>

            <button class="sync-button" data-url="<?php echo esc_attr($row->url); ?>" onclick="syncButtonClicked(this)">
              Sync
            </button>

          </td>
        </tr>

      <?php endforeach; ?>

    </table>

  </div>

  <script>
    function syncButtonClicked(buttonElement) {

      // Get button and URL
      var button = jQuery(buttonElement);
      var url = button.data("url");

      // Update button text
      button.text("...");

      // Make AJAX request
      jQuery.ajax({
        url: "https://scrape-js.onrender.com/",
        type: "POST",
        data: JSON.stringify({
          url: url,
          secretKey: "PzoiJcU2ocfOeWj6AQQdkQ"
        }),
        contentType: "application/json",
        success: function(response) {

          // Update button text
          button.text("Sync");

          // Get data
          var data = response;

          // Make update AJAX request
          jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
              action: "update_row",
              data: JSON.stringify({
                url: data.url,
                author: data.author,
                username: data.username,
                age: data.age,
                profilePicture: data.profilePicture,
                copy: data.copy,
                images: data.images,
                reactions: data.reactions,
                comments: data.comments,
                synced: true
              })
            },
            success: function(response) {

              // Update button text
              button.text("Sync");

            },
            error: function(jqXHR, textStatus, errorThrown) {

              // Update button text
              button.text("Sync");

            }
          });

        },
        error: function(jqXHR, textStatus, errorThrown) {

          // Update button text
          button.text("Sync");

        }
      });

    }

    function publishButtonClicked(buttonElement) {

      // Get button and ID
      var button = jQuery(buttonElement);
      var id = button.data("id");
      var published = button.data("published");

      // Update button text  
      button.text("...");

      // Make publish/unpublish AJAX request
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "publish_unpublish",
          id: id,
          published: !published
        },
        success: function(response) {

          // Update button text and data
          button.text(published ? "Publish" : "Unpublish");
          button.data("published", !published);

        },
        error: function(jqXHR, textStatus, errorThrown) {

          // Log error
          console.log("Error: " + textStatus + ", " + errorThrown);

          // Reset button text
          button.text(published ? "Unpublish" : "Publish");

        }
      });

    }

    jQuery('#posts-table tbody').sortable({
      update: function(event, ui) {
        var postOrder = jQuery(this).sortable('toArray');

        jQuery.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'update_post_order',
            order: postOrder
          },
          success: function(response) {
            // Handle success
          },
          error: function(jqXHR, textStatus, errorThrown) {
            // Handle error
          }
        });
      }
    });
  </script>

<?php

  // Output buffer
  echo ob_get_clean();
}
