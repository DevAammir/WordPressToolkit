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
    // pd(WPT_SOCIALMEDIA);
?>
    <div id="admin_page" class="wrap">
        <h2>WordPress Toolkit</h2>
        <div class="clearfix" id="target">&nbsp;</div>
        <form method="post" action="#" id="wpt_save_settings_form">
            <ul class="tabs">
                <li class="tab active" onclick="showContent('tab1', this);">Basic Settings</li>

                <li class="tab" id="social_media_tab" onclick="showContent('tab2', this);">Social Media</li>

                <!-- <li class="tab" onclick="showContent('tab3', this);">Tab 3</li> -->
            </ul>

            <div id="tab1" class="content active">
                <?php
                include_once('pages/basic-settings.php');
                ?>
            </div>

            <div id="tab2" id="social_media_tab_content" class="content">
                <?php
                include_once('pages/social-media.php');
                ?>
            </div>


            <!-- <div id="tab3" class="content">
                <h2>Content for Tab 3</h2>
                <p>This is the content for tab 3.</p>
            </div> -->
            <?php
            FORMBUILDER->field([
                'type' => 'button',
                'label' => 'Save Settings',
                'name' => 'wpt_save_settings',
                'id' => 'wpt_save_settings',
                'class' => 'button button-primary',
            ]); ?>
        </form>
    </div>
    <?php include_once 'script.php'; ?>
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
    $wpt_socialmedia = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'sm_') === 0) {
            $wpt_socialmedia[$key] = $value;
        } else {
            $wpt_settings[$key] = $value;
        }
    }




    // update the options in one go
    update_option('wpt_settings', $wpt_settings);
    update_option('wpt_socialmedia', $wpt_socialmedia);


    $result = ('Settings saved successfully!');
    $status = 1;
    $message = "success";
    $return = json_encode(array('result' => $result, 'Status' => $status, 'message' => $message, 'request' => $_REQUEST, 'args' => $args));
    echo $return;
    exit;
}
