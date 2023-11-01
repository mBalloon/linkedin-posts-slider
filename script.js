/* script.js */

// Function to update post order
function updatePostOrder(postOrder) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'update_post_order',
            post_order: postOrder
        },
        success: function (response) {
            // Handle success
            console.log('Post order updated:', response);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Handle error
            console.error('Error updating post order:', textStatus, errorThrown);
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
