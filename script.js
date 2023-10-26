/* script.js */


function syncButtonClicked(buttonElement) {
    // Get button and URN
    var button = jQuery(buttonElement);
    var urn = button.data("urn");

    // Update button text
    button.text("...");

    // Make AJAX request

    jQuery('#posts-table tbody').sortable({
        update: function (event, ui) {
            var postOrder = jQuery(this).sortable('toArray');

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'update_post_order',
                    post_order: postOrder
                }
            });
        }
    });
    jQuery.ajax({
        url: "https://scrape-js.onrender.com/",
        type: "POST",
        data: JSON.stringify({
            urn: urn,
            secretKey: "PzoiJcU2ocfOeWj6AQQdkQ"
        }),
        contentType: "application/json",
        success: function (response) {

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
                        urn: data.urn,
                        author: data.author,
                        username: data.username,
                        age: data.age,
                        profilePicture: data.profilePicture,
                        post_text: data.post_text,
                        images: data.images,
                        reactions: data.reactions,
                        comments: data.comments,
                        synced: true
                    })
                },
                success: function (response) {

                    // Update button text
                    button.text("Sync");

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    // Update button text
                    button.text("Sync");

                }
            });

        },
        error: function (jqXHR, textStatus, errorThrown) {

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
    button.text('...').addClass('loading');

    // Make publish/unpublish AJAX request
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
            action: "publish_unpublish",
            id: id,
            published: published
        },
        success: function (response) {
            console.log('Publish/Unpublish action response:', response);  // Debugging line
            button.text(published ? 'Publish' : 'Unpublish').removeClass('loading');
            button.data("published", !published);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error: " + textStatus + ", " + errorThrown);  // Debugging line
            button.text(published ? 'Unpublish' : 'Publish').removeClass('loading');
        }
    });
}

function move_row() {
    global $wpdb;
    $table_name = $wpdb -> prefix. 'linkedin_posts';

    $id = intval($_POST['id']);
    $action = $_POST['action'];

    // Step 1: Fetch current post_order and IDs
    $rows = $wpdb -> get_results("SELECT id, post_order FROM $table_name ORDER BY post_order ASC");

    // Step 2: Find the index of the row to be moved
    $index = array_search($id, array_column($rows, 'id'));

    if ($index !== false && $index >= 0 && $index < count($rows)) {
        if ($action === 'move_up' && $index > 0) {
            // Step 3: Swap post_order values for moving up
            $swap_index = $index - 1;
        } elseif($action === 'move_down' && $index < count($rows) - 1) {
            // Step 3: Swap post_order values for moving down
            $swap_index = $index + 1;
        } else {
            wp_send_json_error('Invalid move');
            return;
        }

        // Step 4: Update the database
        $wpdb -> query("START TRANSACTION");

        $wpdb -> update(
            $table_name,
            array('post_order' => $rows[$swap_index] -> post_order),
            array('id' => $id)
        );

        $wpdb -> update(
            $table_name,
            array('post_order' => $rows[$index] -> post_order),
            array('id' => $rows[$swap_index] -> id)
        );

        $wpdb -> query("COMMIT");

        wp_send_json_success('Row moved successfully');
    } else {
        wp_send_json_error('Invalid ID or index');
    }
}
add_action('wp_ajax_move_up', 'move_row');
add_action('wp_ajax_move_down', 'move_row');


jQuery(document).ready(function ($) {

    $('.delete-button').on('click', function (e) {
        e.preventDefault();  // Prevent the form from submitting the traditional way
        var button = $(this);
        var postId = button.data('id');
        console.log('Delete button clicked for post ID:', postId);  // Debugging line

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_post',
                id: postId
            },
            success: function (response) {
                console.log('Delete action response:', response);  // Debugging line
                $('#post-' + postId).remove();
            }
        });
    });

    $('form input[type=submit]').on('click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const button = $(this);
        button.val('...').addClass('loading');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                console.log('Form submission response:', response);  // Debugging line
                button.val('Delete').removeClass('loading');
                form.closest('tr').remove();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);  // Debugging line
                button.val('Delete').removeClass('loading');
            }
        });
    });

    jQuery('.publish-button').on('click', function () {
        publishButtonClicked(this);
    });

    jQuery('.publish-button').hover(function () {
        var button = jQuery(this);
        var published = button.data("published");
        if (published == 1) {
            button.text('Unpublish');
        } else {
            button.text('Publish');
        }
    }, function () {
        var button = jQuery(this);
        var published = button.data("published");
        if (published == 1) {
            button.text('Published');
        } else {
            button.text('Unpublished');
        }
    });

    // Assuming there's a button with class sync-button for syncing
    jQuery('.sync-button').on('click', function () {
        syncButtonClicked(this);
    });

    jQuery(document).ready(function ($) {
        $('#posts-table tbody').sortable({
            update: function (event, ui) {
                var postOrder = jQuery(this).sortable('toArray');

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'update_post_order',
                        post_order: postOrder
                    },
                    success: function (response) {
                        // Handle success
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Handle error
                    }
                });
            }
        });
    });

    jQuery('.up-button, .down-button').on('click', function () {
        var button = jQuery(this);
        var id = button.closest('tr').find('.row-id').text();
        var action = button.hasClass('up-button') ? 'move_up' : 'move_down';

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: action,
                id: id,
            },
            success: function (response) {
                console.log('Move row response:', response);
                // Update the table based on the response or refresh the page to reflect the changes
                location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    });


});


