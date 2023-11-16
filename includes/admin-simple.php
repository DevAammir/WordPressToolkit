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
add_action('admin_menu', 'wpt_admin_menu', 0);

// Render the custom admin page
function wpt_admin_page()
{
    ob_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wpt_save_settings'])) {
        // Form submitted, process the data
        wpt_save_settings();
    }

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
        <div class="clearfix" id="target">&nbsp;</div>

        <form method="post" action="">
            <?php
            $fb = new FormBuilder();
            

            foreach (AVAILABLE_OPTIONS as $key => $value) {
                $fb->field([
                    'type' => 'checkbox',
                    'label' => $value,
                    'name' => $key,
                    'id' => $key,
                    'dbval' => !empty(WPT_SETTINGS[$key]) ? WPT_SETTINGS[$key] : '',
                ]);
            }

            echo '<hr>';
            $fb->field([
                'type' => 'submit', // Change the button type to "submit"
                'label' => 'Save Settings',
                'name' => 'wpt_save_settings',
                'id' => 'wpt_save_settings',
                'class' => 'button button-primary',
            ]);
            ?>
        </form>
    </div>
<?php
    ob_end_flush();
}

/***
 * SET VALUES
 * ***/
function wpt_save_settings()
{
    $wpt_settings = [];
    foreach ($_POST as $key => $value) {
        $wpt_settings[$key] = $value;
    }

    // update the options in one go
    update_option('wpt_settings', $wpt_settings);

    // Redirect back to the settings page or display a success message
    // wp_redirect(admin_url('admin.php?page=wpt-admin'));
    header('Location: '.admin_url('admin.php?page=wpt-admin'));
    exit;
}
