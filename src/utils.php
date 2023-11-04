<?php

function get_custom_setting($setting_name, $default = '')
{
    global $wpdb;
    $value = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM {$wpdb->prefix}linkedin_slider_settings WHERE setting_name = %s", $setting_name));
    if (is_null($value)) {
        return $default;
    }
    return $value;
}
