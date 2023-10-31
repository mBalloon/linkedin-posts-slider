

jQuery(document).ready(function ($) {
    $('#my-ajax-form').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();
        formData += '&action=update_linkedin_settings';

        $.post(my_ajax_object.ajax_url, formData, function (response) {
            console.log(response)
        });
    });
});