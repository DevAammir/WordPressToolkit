<?php
function user_register_cb()
{
    ob_start();

    // Add nonce field to the form
    wp_nonce_field('wpt_login_nonce', 'wpt_login_nonce_field');
?>
    <form action="#" method="post" id="wpt-user-register-form" enctype="multipart/form-data">
        <div id="response"></div>
        <?php foreach (WPT_REGISTRATION_FIELDS as $key => $value) : ?>
            <?php FORMBUILDER->field([
                'type'  => $value,
                'label' => str_replace('_', ' ', ucfirst($key)),
                'placeholder' => str_replace('_', ' ', ucfirst($key)),
                'name'  => $key,
                'id'    => $key,
                'class' => 'form-control',
            ]); ?>
        <?php endforeach; ?>


        <?php FORMBUILDER->field([
            'type'  => 'submit',
            'label' => 'Register',
            'name'  => 'wpt_user_register_button',
            'id'    => 'wpt_user_register_button',
            'class' => 'button button-primary',
        ]); ?>
    </form>

    <script>
        jQuery(document).ready(function($) {
            $(document).on('click', '#wpt_user_register_button', function(event) {
                event.preventDefault();

                // Clear previous error messages
                $('#response').html('');

                // Validate each field
                var valid = true;

                // Validation for first_name
                var first_name = $('#first_name').val().trim();
                if (first_name === '') {
                    $('#response').html('Please enter your first name.');
                    valid = false;
                } else                // Validation for last_name
                var last_name = $('#last_name').val().trim();
                if (last_name === '') {
                    $('#response').html('Please enter your last name.');
                    valid = false;
                }

                // Validation for email
                var email = $('#email').val().trim();
                if (email === '') {
                    $('#response').html('Please enter your email address.');
                    valid = false;
                } else if (!isValidEmail(email)) {
                    $('#response').html('Please enter a valid email address.');
                    valid = false;
                }

                // Validation for password
                var password = $('#password').val().trim();
                if (password === '') {
                    $('#response').html('Please enter a password.');
                    valid = false;
                } else if (password.length < 6) {
                    $('#response').html('Password must be at least 6 characters long.');
                    valid = false;
                }

                // Validation for confirm_password
                var confirm_password = $('#confirm_password').val().trim();
                if (confirm_password === '') {
                    $('#response').html('Please confirm your password.');
                    valid = false;
                } else if (confirm_password !== password) {
                    $('#response').html('Passwords do not match.');
                    valid = false;
                }

                // Validation for billing_phone
                var billing_phone = $('#billing_phone').val().trim();
                if (billing_phone === '') {
                    $('#response').html('Please enter your phone number.');
                    valid = false;
                }

                // Validation for billing_city
                var billing_city = $('#billing_city').val().trim();
                if (billing_city === '') {
                    $('#response').html('Please enter your city.');
                    valid = false;
                }

                // Validation for billing_postcode
                var billing_postcode = $('#billing_postcode').val().trim();
                if (billing_postcode === '') {
                    $('#response').html('Please enter your postal code.');
                    valid = false;
                }

                // Validation for billing_state
                var billing_state = $('#billing_state').val().trim();
                if (billing_state === '') {
                    $('#response').html('Please enter your state.');
                    valid = false;
                }

                // Validation for billing_country
                var billing_country = $('#billing_country').val();
                if (billing_country === '') {
                    $('#response').html('Please select your country.');
                    valid = false;
                }

                // Validation for profile_image (assuming it's a file input)
                var profile_image = $('#profile_image').val();
                if (!profile_image) {
                    $('#response').html('Please upload a profile image.');
                    valid = false;
                } else {
                    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                    if (!allowedExtensions.exec(profile_image)) {
                        $('#response').html('Please choose a valid image file (jpg, jpeg, png, gif).');
                        valid = false;
                    }
                }

                // Validation for terms_agreement (assuming it's a checkbox)
                var terms_agreement = $('#terms_agreement').prop('checked');
                if (!terms_agreement) {
                    $('#response').html('You must agree to the terms and conditions.');
                    valid = false;
                }

                // If all validations pass, proceed with AJAX submission
                if (valid != false) {
                    var formData = new FormData($('#wpt-user-register-form')[0]);

                    // Add nonce to the data
                    formData.append('action', 'wpt_register_user');
                    formData.append('wpt_register_nonce', '<?php echo wp_create_nonce("wpt_register_nonce"); ?>');

                    // Your AJAX code
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response); // Log the response to the console

                            // Parse the JSON response
                            var result = $.parseJSON(response);

                            // Display the message
                            $('#response').html(result.message);

                            // Redirect if registration was successful
                            if (result.status === 200) {
                                window.location.href = result.redirect;
                            } else {
                                $('#response').html('Registration failed, please try again later.');
                            }
                        },
                        error: function(xhr, textStatus, error) {
                            $('#response').html(error);
                        }
                    });
                }
            });

            // Helper function to check if the email is valid
            function isValidEmail(email) {
                // You can implement a more robust email validation if needed
                return /\S+@\S+\.\S+/.test(email);
            }
        });
    </script>
<?php

    $return = ob_get_clean();
    return $return;
}

add_shortcode('wpt_user_register', 'user_register_cb');




function wpt_register_user()
{
    // Verify the nonce
    if (!isset($_POST['wpt_register_nonce']) || !wp_verify_nonce($_POST['wpt_register_nonce'], 'wpt_register_nonce')) {
        $message = '<div class="error">Nonce verification failed.</div>';
        $status = 400;
    } else {
        // Get the form data from the AJAX request
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $billing_phone = sanitize_text_field($_POST['billing_phone']);
        $billing_city = sanitize_text_field($_POST['billing_city']);
        $billing_postcode = sanitize_text_field($_POST['billing_postcode']);
        $billing_state = sanitize_text_field($_POST['billing_state']);
        $billing_country = sanitize_text_field($_POST['billing_country']);
        $profile_image = $_FILES['profile_image'];
        $username = $first_name . '-' . $last_name;
        $status = 400;
        $message = '';
        $redirect = '';

        // Validate the form data (you may want to add more robust validation)
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($billing_phone) || empty($billing_city) || empty($billing_postcode) || empty($billing_state) || empty($billing_country)) {
            $message = '<div class="error">Please fill in all required fields.</div>';
            $status = 400;
        } else {

            if (email_exists($email)) {
                $status = 420;
                $message = 'You already have registerd with us. Try loggin in with your email or username.';
            }

            if (username_exists($username)) {
                $status = 420;
                $message = 'You already have registerd with us. Try loggin in with your email or username.';
            }
            $user_data = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => 'subscriber',
            ];
            $user_id = wpt_create_user($user_data);

            // Handle profile image upload
            $IMG_UPL = _wpt_upload_user_image([
                'user_identifier' => $user_id,
                'image'           => $profile_image
              ]);

            if ($IMG_UPL==1) {
 
                $confirmation_token = md5(uniqid(wp_rand(), true));


                $user_metadata = [
                    'wpt_user_status' => 'disabled', 
                    'first_name' => $first_name,
                    'billing_first_name' => $first_name,
                    'last_name' => $last_name,
                    'billing_last_name' => $last_name,
                    'billing_phone' => $billing_phone,
                    'billing_city' => $billing_city,
                    'billing_postcode' => $billing_postcode,
                    'billing_state' => $billing_state,
                    'billing_country' => $billing_country,
                    'confirmation_token' => $confirmation_token,
                ];

                foreach ($user_metadata as $key => $value) {
                    update_user_meta($user_id, $key, $value);
                }
            } else {
                // Handle image upload error
                $message = '<div class="error">Failed to upload profile image.</div>';
                $status = 400;
            }

            // For demonstration purposes, assume registration is successful
            $status = 200;
            $message = 'Registration successful. Redirecting...';
            $redirect = home_url() . '/my-account';
        }
    }

    echo json_encode(array(
        'status'   => $status,
        'message'  => $message,
        'redirect' => $redirect
    ));
    wp_die();
}

add_action('wp_ajax_wpt_register_user', 'wpt_register_user');
add_action('wp_ajax_nopriv_wpt_register_user', 'wpt_register_user');
