<?php
function user_login_cb()
{
    ob_start();

    // Add nonce field to the form
    wp_nonce_field('wpt_login_nonce', 'wpt_login_nonce_field');
?>
    <form action="#" method="post" id="wpt-user-login-form">
        <div id="response"></div>
        <?php FORMBUILDER->field([
            'type'  => 'text',
            'label' => 'Username',
            'name'  => 'wpt_username',
            'id'    => 'wpt_username',
            'class' => 'form-control',
        ]); ?>

        <?php FORMBUILDER->field([
            'type'  => 'password',
            'label' => 'Password',
            'name'  => 'wpt_password',
            'id'    => 'wpt_password',
            'class' => 'form-control',
        ]); ?>

        <?php FORMBUILDER->field([
            'type'  => 'submit',
            'label' => 'Login',
            'name'  => 'wpt_user_login_button',
            'id'    => 'wpt_user_login_button',
            'class' => 'button button-primary',
        ]); ?>
    </form>

    <script>
        jQuery(document).ready(function($) {
            let WPT_AJAX = '<?php echo WPT_AJAX; ?>';

            // Attach a click event handler to the login button
            $('#wpt_user_login_button').on('click', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Get the username and password values
                var username = $('#wpt_username').val();
                var password = $('#wpt_password').val();

                // Check if username and password are not empty
                if (username === '' || password === '') {
                    $('#response').html('<div class="error">Please enter both username and password.</div>');
                    return;
                }

                // Add nonce to the data
                var data = {
                    action: 'wpt_login_user',
                    username: username,
                    password: password,
                    wpt_login_nonce: '<?php echo wp_create_nonce("wpt_login_nonce"); ?>'
                };

                $.post(WPT_AJAX, data, function(response) {
                    console.log(response); // Log the response to the console

                    // Parse the JSON response
                    var result = $.parseJSON(response);

                    // Display the message
                    $('#response').html(result.message);

                    // Redirect if login was successful
                    if (result.status === 200) {
                        window.location.href = result.redirect;
                    } else {
                        $('.error a').attr('href', '<?php echo home_url(); ?>/lost-password/');
                    }
                });
            });



        });
    </script>
<?php

    $return = ob_get_clean();
    return $return;
}

add_shortcode('wpt_user_login', 'user_login_cb');

function wpt_login_user()
{
    // Verify the nonce
    if (!isset($_POST['wpt_login_nonce']) || !wp_verify_nonce($_POST['wpt_login_nonce'], 'wpt_login_nonce')) {
        $message = '<div class="error">Nonce verification failed.</div>';
        $status = 400;
    } else {
        // Get the username and password from the AJAX request
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $status = 400;
        $message = '';
        $redirect = '';

        // Validate the username and password (you may want to add more robust validation)
        if (empty($username) || empty($password)) {
            $message = '<div class="error">Please enter both username and password.</div>';
            $status = 400;
        } else {
            $user = wp_signon(['user_login' => $username, 'user_password' => $password], false);
            $user_id = $user->ID;
            // Check if the login was successful
            if (is_wp_error($user)) {
                $message = '<div class="error">' . $user->get_error_message() . '</div>';
                $status = 400;
            } else {
                $status = 200;
                $current_time = current_time('mysql');

                update_user_meta($user_id, $meta_key, $current_time);

                $message = 'Successfully logged in. Redirecting...';
                $redirect = home_url();
            }
        }
    }

    // Use the correct variable name here
    echo json_encode(array(
        'status'  => $status,
        'message' => $message,
        'redirect'    => $redirect
    ));
    wp_die();
}

add_action('wp_ajax_wpt_login_user', 'wpt_login_user');
add_action('wp_ajax_nopriv_wpt_login_user', 'wpt_login_user'); // For non-logged-in users