<?php

function user_login_cb()
{
    ob_start();

    ?>
    <form id="wpt_login_form" action="#" method="post" id="user_login">
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
            'name'  => 'wpt_user_login',
            'id'    => 'wpt_user_login',
            'class' => 'button button-primary',
        ]); ?>

    </form>
 <script>
    jQuery(document).ready(function ($) {
    // Attach a click event handler to the login button
    $('#wpt_user_login').on('click', function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Get the username and password values
        var username = $('#wpt_username').val();
        var password = $('#wpt_password').val();

        // Check if username and password are not empty
        if (username === '' || password === '') {
            $('#response').html('<div class="error">Please enter both username and password.</div>');
            return;
        }

        // Perform an AJAX request to the WordPress endpoint
        $.ajax({
            type: 'POST',
            url: <?php echo WPT_AJAX;?>, // WordPress AJAX endpoint URL
            data: {
                action: 'wpt_login_user', // Action to be performed on the server
                username: username,
                password: password,
            },
            success: function (response) {
                // Handle the response from the server
                if(response == 1){
                    window.location.reload();
                }
                $('#response').html(response);
            },
            error: function (error) {
                // Handle errors
                $('#response').html('<div class="error">Error occurred: ' + error.statusText + '</div>');
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

function wpt_login_user_callback() {
    // Get the username and password from the AJAX request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the username and password (you may want to add more robust validation)
    if (empty($username) || empty($password)) {
        echo '<div class="error">Please enter both username and password.</div>';
        wp_die();
    }

    // Perform login logic here (you may use wp_signon() or custom logic)
    $user = wp_signon(['user_login' => $username, 'user_password' => $password], false);

    // Check if the login was successful
    if (is_wp_error($user)) {
        echo '<div class="error">' . esc_html($user->get_error_message()) . '</div>';
    } else {
        echo 1;
        // $status = 1;
    }

    wp_die(); // Always call wp_die() after processing AJAX requests
}

add_action('wp_ajax_wpt_login_user', 'wpt_login_user_callback');
add_action('wp_ajax_nopriv_wpt_login_user', 'wpt_login_user_callback'); // For non-logged-in users
