jQuery(document).ready(function ($) {
    $('#my-ajax-form').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: ajaxurl, // 'ajaxurl' is automatically defined by WordPress and points to 'admin-ajax.php'
            type: 'POST',
            data: {
                action: 'handle_scrapper_form_submission',
                form_data: formData
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message);
                }
            }
        });
    });
});

