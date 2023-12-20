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
    'wpt_error_reporting' => 'Turn error reporting on',
];

if (CURRENT_THEME != 'wp-lite') {
    $available_options_array['wpt_enable_social_media'] = 'Enable Social Media';
    $available_options_array['wpt_add_woocommerce_support'] = 'Add Woocommerce Support';
    $available_options_array['wpt_add_classes_body'] = 'Add CSS Classes to body';
    $available_options_array['wpt_disable_gutenburg'] = 'Disable Gutenburg';
}

define('WPT_AVAILABLE_OPTIONS', $available_options_array);
if (CURRENT_THEME == 'wp-lite') {
}else{
    
    $fb = new FormBuilder();
    define('FORMBUILDER', $fb);
}
$wpt_settings = get_option('wpt_settings');
$wpt_socialmedia = get_option('wpt_socialmedia');
define('WPT_SETTINGS', $wpt_settings);
define('WPT_SOCIALMEDIA', $wpt_socialmedia);



$registration_fields = [
    'first_name' => 'text',
    'last_name' => 'text',
    'email' => 'email',
    'password' => 'password',
    'confirm_password' => 'password',
    'billing_phone' => 'text',
    'billing_city' => 'text',
    'billing_postcode' => 'text',
    'billing_state' => 'text',
    'billing_country' => 'countries',
    'profile_image' => 'image',
    'terms_agreement' => 'checkbox',
];
define('WPT_REGISTRATION_FIELDS', $registration_fields);


$wpt_config = [
    'wpt_user_activation_link' => site_url('/actiavate-account'),
    'wpt_reset_password_link' => site_url('/reset-password'),
];

define('WPT_CONFIG', $wpt_config);
update_option('WPT_CONFIG', WPT_CONFIG);
