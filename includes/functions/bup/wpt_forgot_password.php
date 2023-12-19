<?php
// Add this shortcode to your theme's functions.php file or a custom plugin
add_shortcode('custom_forgot_password_form', 'custom_forgot_password_shortcode');

function custom_forgot_password_shortcode() {
    ob_start(); ?>

    <form method="post">
        <?php wp_nonce_field('forgot_password_nonce', 'forgot_password_nonce'); ?>
        <label for="username_or_email">Enter your email or username:</label>
        <input type="text" name="username_or_email" required>
        <input type="hidden" name="wpt_reset_password_link" value="<?php echo WPT_CONFIG['wpt_reset_password_link']; ?>">
        <input type="submit" value="Reset Password">
    </form>

    <?php
    handle_forgot_password_form(); // Handle form submission
    return ob_get_clean();
}

// Function to handle forgot password form submission and send reset link
function handle_forgot_password_form() {
    if (isset($_POST['forgot_password_nonce']) && wp_verify_nonce($_POST['forgot_password_nonce'], 'forgot_password_nonce')) {
        // Get username or email from the form
        $username_or_email = sanitize_text_field($_POST['username_or_email']);

        // Find user by username or email
        $user = get_user_by('login', $username_or_email) ?: get_user_by('email', $username_or_email);

        // Check if a user was found
        if ($user) {
            // Generate a unique token (you can use a library or generate your own)
            $token = bin2hex(random_bytes(32));

            // Store the token in user meta
            update_user_meta($user->ID, 'reset_password_token', $token);

            // Send an email to the user with a link to reset their password
            $link = $_POST['wpt_reset_password_link'];
            $reset_link =  $link."?token=$token&user=" . $user->ID;
            $to = $user->user_email;
            $subject = "Password Reset";
           echo  $message = "Click the following link to reset your password: $reset_link";
            $admin_email = get_option('admin_email');;
            $headers = "From: ". $admin_email; // Change this to your email address

            if (wp_mail($to, $subject, $message, $headers)) {
                echo 'Reset link sent successfully. Check your email.';
            } else {
                echo 'Error sending reset link.';
            }
        } else {
            echo 'User not found. Please check your email or username and try again.';
        }
    }
}








// Add this shortcode to your theme's functions.php file or a custom plugin
add_shortcode('custom_reset_password_form', 'custom_reset_password_shortcode');

function custom_reset_password_shortcode() {
    ob_start();

    // Get token and user ID from the query parameters
    $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
    $user_id = isset($_GET['user']) ? absint($_GET['user']) : 0;

    // Validate the token and user ID
    if ($token && $user_id) {
        // Display the reset password form
        ?>
        <form method="post">
            <?php wp_nonce_field('reset_password_nonce', 'reset_password_nonce'); ?>
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">
            <input type="hidden" name="reset_token" value="<?php echo esc_attr($token); ?>">
            <label for="new_password">Enter your new password:</label>
            <input type="password" name="new_password" required>
            <input type="submit" value="Reset Password">
        </form>
        <?php

        // Handle form submission
        handle_reset_password_form();
    } else {
        echo 'Invalid reset link. Please check your link and try again.';
    }

    return ob_get_clean();
}

// Function to handle reset password form submission
function handle_reset_password_form() {
    if (
        isset($_POST['reset_password_nonce'])
        && wp_verify_nonce($_POST['reset_password_nonce'], 'reset_password_nonce')
        && isset($_POST['user_id'])
        && isset($_POST['reset_token'])
    ) {
        $user_id = absint($_POST['user_id']);
        $reset_token = sanitize_text_field($_POST['reset_token']);
        $new_password = sanitize_text_field($_POST['new_password']);

        // Validate and sanitize the new password
        // Additional validation can be added as needed

        // Verify the reset token stored in user meta
        $stored_token = get_user_meta($user_id, 'reset_password_token', true);

        if ($reset_token === $stored_token) {
            // Update user password and remove the reset token
            wp_set_password($new_password, $user_id);
            delete_user_meta($user_id, 'reset_password_token');

            echo 'Password reset successfully. You can now log in with your new password.';
        } else {
            echo 'Invalid reset token. Please try again.';
        }
    }
}
?>
