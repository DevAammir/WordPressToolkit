<?php

/**
 * Plugin Name: WordPress Toolkit
 * Description: WordPress Toolkit 
 * Version: 1.6
 * Author: Aammir
 * Author URI: https://127.0.0.1
 * Text Domain: wpt
 */


if (!defined('ABSPATH')) {
    die();
}

define('WPT_URL', plugin_dir_url(__FILE__)); // Get the plugin URL 
define('WPT_DIR', dirname(__FILE__) . '/'); // Get the plugin directory path that is wp-content/plugins/wp-toolkit
define('WPT_AJAX', admin_url('admin-ajax.php'));
$current_theme = get_stylesheet();
define('CURRENT_THEME', $current_theme);

if (CURRENT_THEME !== 'wp-lite') {
    require_once WPT_DIR . 'includes/form-builder.php';

    add_action('wp_footer', 'setting_wpt_ajax');
    add_action('addmin_footer', 'setting_wpt_ajax');
    add_action('admin_head', 'add_custom_script', 10);
    add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
    /**
     * Sets up the WPT AJAX functionality.
     *
     *  adding the WPT_AJAX meta tag.
     */



    function setting_wpt_ajax()
    { ?>
        <script>
            jQuery(document).ready(function($) {
                $('head').append('<meta name="WPT_AJAX" content="<?php echo WPT_AJAX; ?>">');
                console.log('WPT_AJAX added!');
                // const WPT_AJAX = jQuery('meta[name="WPT_AJAX"]').attr('content');//DOESN'T WORK!
                //  console.log(WPT_AJAX);
            });
        </script>
<?php
    }
}
require_once WPT_DIR . 'includes/config.php';

function add_custom_script()
{
    // Check if we are in the admin section
    if (is_admin()) {
        //  echo '<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>';
        echo '<script src="' . esc_url(WPT_URL . 'js/functions.js') . '"></script>';
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@6.4.0/css/all.min.css">';
    }
}


/* * *
 *  Enqueue scripts and styles
 * * */
function enqueue_custom_scripts()
{
    // Enqueue custom script
    wp_enqueue_script('JS-functions', WPT_URL . 'js/functions.js', array('jquery'), '1.0', true);
}



function active()
{
    update_option('wpt_plugin_activated', date('H:i:sA d-m-Y'));
}
register_activation_hook(__FILE__, 'active');

function inactive()
{
    update_option('_wpt_plugin_deactivated', date('H:i:sA d-m-Y'));
}
register_deactivation_hook(__FILE__, 'inactive');

function uninstall_func()
{
    delete_option('_wpt_plugin_activated');
    delete_option('_wpt_plugin_deactivated');
    die('something went wrong!');
}
register_uninstall_hook(__FILE__, 'uninstall_func');

require_once WPT_DIR . 'includes/admin.php';
require_once WPT_DIR . 'includes/functions.php';
require_once WPT_DIR . 'includes/actions.php';
