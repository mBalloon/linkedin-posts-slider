/* script.js */
function syncButtonClicked(buttonElement) {
    // Get button and URN
    var button = jQuery(buttonElement);
    var urn = button.data("urn");

    // Update button text
    button.text("...");

    // Make AJAX request
    jQuery('.delete-button').click(function () {
        var postId = jQuery(this).data('id');

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_post',
                id: postId
            },
            success: function (response) {
                // Remove the post from the table
                jQuery('#post-' + postId).remove();
            }
        });
    });

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
        success: function (response) {

            // Update button text and data
            button.text(published ? "Publish" : "Unpublish");
            button.data("published", !published);

        },
        error: function (jqXHR, textStatus, errorThrown) {

            // Log error
            console.log("Error: " + textStatus + ", " + errorThrown);

            // Reset button text
            button.text(published ? "Unpublish" : "Publish");

        }
    });

}

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
