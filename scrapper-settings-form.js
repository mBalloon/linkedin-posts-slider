jQuery(document).ready(function ($) {
    $('#my-ajax-form').on('submit', function (e) {
        e.preventDefault();

        // Manually collect form values
        const linkedinCompanyUrl = $('input[name="linkedin_company_url"]').val();
        const linkedinSliderOpenLink = $('input[name="linkedin_slider_open_link"]').is(':checked') ? 1 : 0;
        const linkedinUpdateFrequency = $('input[name="linkedin_update_frequency"]').val();
        const linkedinScrapperEndpoint = $('input[name="linkedin_scrapper_endpoint"]').val();

        // Construct the data object
        const formData = {
            'linkedin_company_url': linkedinCompanyUrl,
            'linkedin_slider_open_link': linkedinSliderOpenLink,
            'linkedin_update_frequency': linkedinUpdateFrequency,
            'linkedin_scrapper_endpoint': linkedinScrapperEndpoint,
            'action': 'update_linkedin_settings'
        };

        $.post(my_ajax_object.ajax_url, formData, function (response) {
            console.log(response);
        });
    });
});
