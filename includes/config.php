<?php

$available_social_media_platforms = [
    'Facebook' => 'facebook',
    'Twitter' => 'twitter',
    'LinkedIn' => 'linkedin',
    // 'Pinterest' => 'pinterest',
    'Reddit' => 'reddit',
    'Tumblr' => 'tumblr',
    'Instagram' => 'instagram',
    // 'Snapchat' => 'snapchat',
    'YouTube' => 'youtube',
    'Vimeo' => 'vimeo',
    // 'Telegram' => 'telegram',
    'WhatsApp' => 'whatsapp',
    'Email' => 'email', // Not a social media platform, but often included in sharing options.
    // Add more as needed.
];

define('AVAILABLE_SOCIAL_MEDIA_PLATFORMS', $available_social_media_platforms);


$available_options_array = [
    'wpt_disable_gutenburg' => 'Disable Gutenburg',
    'wpt_enable_social_media' => 'Enable Social Media',
    'wpt_add_woocommerce_support' => 'Add Woocommerce Support',
    'wpt_add_classes_body' => 'Add CSS Classes to body',
    'wpt_error_reporting' => 'Turn error reporting on',
];

define('AVAILABLE_OPTIONS', $available_options_array);

$fb = new FormBuilder();
define('FORMBUILDER', $fb);

$wpt_settings = get_option('wpt_settings');
$wpt_socialmedia = get_option('wpt_socialmedia');
define('WPT_SETTINGS', $wpt_settings);
define('WPT_SOCIALMEDIA', $wpt_socialmedia);
