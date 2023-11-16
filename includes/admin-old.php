<?php


// Add a menu item to the WordPress admin menu
function wpt_admin_menu()
{
    add_menu_page(
        'WordPress Toolkit Admin',  // Page title
        'WordPress Toolkit',       // Menu title
        'manage_options',     // Capability required to access
        'wpt-admin',  // Menu slug
        'wpt_admin_page',  // Callback function to render the page
        'dashicons-admin-settings',  // Icon for the menu item (change as needed)
        102  // Menu position
    );
}
add_action('admin_menu', 'wpt_admin_menu', 1);

// Render the custom admin page
function wpt_admin_page()
{
?>
    <style>
        .form_builder_row {
            margin: 20px 0;
        }

        .form_builder_row>label {
            width: 300px !important;
            display: block;
            float: left;
            font-size: 16px;
            font-weight: 400;
        }

        .wrap {
            padding: 10px 20px;
        }

        .wrap h2 {
            margin-top: 30px;
            margin-bottom: 20px;
        }
    </style>
    <div class="wrap">
        <h2>WordPress Toolkit</h2>
        <!-- <div class="clearfix">&nbsp;</div> -->
        <div class="clearfix" id="target">&nbsp;</div>
        
        <form method="post" action="#" id="wpt_save_settings_form">
            <?php $fb = new FormBuilder();

            $fb->field([
                'type' => 'checkbox',
                'label' => 'Disable Gutenburg?',
                'name' => 'wpt_disable_gutenburg',
                'id' => 'wpt_disable_gutenburg',
                'dbval' => !empty(WPT_SETTINGS['wpt_disable_gutenburg']) ? WPT_SETTINGS['wpt_disable_gutenburg'] : '',
            ]);

            // $fb->field([
            //     'type' => 'checkbox',
            //     'label' => 'Use Helper JS?',
            //     'name' => 'wpt_use_helper_js',
            //     'id' => 'wpt_use_helper_js',
            //     'dbval' => !empty(WPT_SETTINGS['wpt_use_helper_js']) ? WPT_SETTINGS['wpt_use_helper_js'] : '',
            // ]);
            // $fb->field([
            //     'type' => 'checkbox',
            //     'label' => 'Use Helper CSS?',
            //     'name' => 'wpt_use_helper_css',
            //     'id' => 'wpt_use_helper_css',
            //     'dbval' => !empty(WPT_SETTINGS['wpt_use_helper_css']) ? WPT_SETTINGS['wpt_use_helper_css'] : '',
            // ]);
            $fb->field([
                'type' => 'checkbox',
                'label' => 'Enable Social Media?',
                'name' => 'wpt_enable_social_media',
                'id' => 'wpt_enable_social_media',
                'dbval' => !empty(WPT_SETTINGS['wpt_enable_social_media']) ? WPT_SETTINGS['wpt_enable_social_media'] : '',
            ]);
            echo '<hr>';
            $fb->field([
                'type' => 'button',
                'label' => 'Save Settings',
                'name' => 'wpt_save_settings',
                'id' => 'wpt_save_settings',
                'class' => 'button button-primary',
            ]);
            ?>
        </form>
    </div>
    <script>
        (function($) {
            $(document).ready(function() {
                admin_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
                console.log('Welcome to WordPress Toolkit!');

                $(document).on('click', '#wpt_save_settings', function(e) {
                    e.preventDefault();
                    let form_data = $('#wpt_save_settings_form').serialize();
                    console.log(form_data);
                    _AJAX_function_1('#target', admin_ajax_url, 'wpt_save_settings', 'POST', form_data, 'json');
                });

            });
        })(jQuery);
    </script>
<?php
}

/***
 * SET VALUES
 * ***/
add_action("wp_ajax_wpt_save_settings", "wpt_save_settings");
add_action("wp_ajax_nopriv_wpt_save_settings", "wpt_save_settings");
function wpt_save_settings()
{
    $wpt_settings = [];
    foreach ($_POST as $key => $value) {
        $wpt_settings[$key] = $value;
    }

    // update the options in one go
    update_option('wpt_settings', $wpt_settings);


    $result = ('Settings saved successfully!');
    $status = 1;
    $message = "success";
    $return = json_encode(array('result' => $result, 'Status' => $status, 'message' => $message, 'request' => $_REQUEST, 'args' => $args));
    echo $return;
    exit;
}
