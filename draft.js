/* script.js */


// code before



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
            published: !published
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

    //other actions

    jQuery('.publish-button').on('click', function () {
        publishButtonClicked(this);
    });

    //other actions


});


