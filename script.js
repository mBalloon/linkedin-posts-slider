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


});


