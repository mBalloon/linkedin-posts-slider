jQuery(document).ready(function ($) {
    // Define a reusable function to update styles
    function updateStyle(selector, cssProperty, value, unit = '') {
        $(selector).css(cssProperty, value + unit);
    }

    // Map of style settings
    const styleSettings = {
        // Company info section
        "section-company-color": ["color"],
        "section-company-font-size": ["fontSize", 'px'],
        "section-company-font-family": ["fontFamily"],
        "section-company-line-height": ["lineHeight", 'px'],

        // Author username and date section
        "section-author-date-color": ["color"],
        "section-author-date-font-size": ["fontSize", 'px'],
        "section-author-date-font-family": ["fontFamily"],
        "section-author-date-font-weight": ["fontWeight"],
        "section-author-date-line-height": ["lineHeight", 'px'],

        // Post text section
        "section-body-color": ["color"],
        "section-body-font-size": ["fontSize", 'px'],
        "section-body-font-family": ["fontFamily"],
        "section-body-webkit-line-clamp": ["webkitLineClamp"],

        // Post interactions section
        "section-interactions-color": ["color"],
        "section-interactions-font-size": ["fontSize", 'px'],
        "section-interactions-font-family": ["fontFamily"],
        "section-interactions-font-weight": ["fontWeight"],
        "section-interactions-line-height": ["lineHeight", 'px'],
    };

    // Loop through each setting and add a change event listener
    for (const [setting, [cssProperty, unit]] of Object.entries(styleSettings)) {
        $("#" + setting).on("change", function () {
            updateStyle("." + setting, cssProperty, $(this).val(), unit);
        });
    }
});