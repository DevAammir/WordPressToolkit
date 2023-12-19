<?php 

//  
add_shortcode('wpt_user_activation_form', 'activation_form_shortcode');

function activation_form_shortcode() {
    ob_start(); ?>

    <form method="post">
        <?php wp_nonce_field('activate_account_nonce', 'activate_account_nonce'); ?>
        <label for="activation_code">Activation Code:</label>
        <input type="text" name="activation_code" required>
        <input type="submit" value="Activate Account">
    </form>

    <?php
    handle_activation_form(); // Handle form submission
    return ob_get_clean();
}



// Function to handle form submission and activate user
function handle_activation_form() {
    if (isset($_POST['activate_account_nonce']) && wp_verify_nonce($_POST['activate_account_nonce'], 'activate_account_nonce')) {
        // Get activation code from the form
        $activation_code = sanitize_text_field($_POST['activation_code']);

        // Find user with matching activation code in user meta
        $user_query = new WP_User_Query(array(
            'meta_key' => 'wpt_activation_code',
            'meta_value' => $activation_code,
        ));

        $users = $user_query->get_results();

        // Check if a user was found
        if (!empty($users)) {
            foreach ($users as $user) {
                // Update user meta to enable the account
                update_user_meta($user->ID, 'wpt_user_status', 'enabled');
            }

            echo 'User account activated successfully.';
        } else {
            echo 'Invalid activation code. Please check your code and try again.';
        }
    }
}


