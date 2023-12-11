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

        <input type="hidden" name="wpt_user_activation_link" value="<?php echo WPT_CONFIG['wpt_user_activation_link']; ?>">

        <?php FORMBUILDER->field([
            'type'  => 'submit',
            'label' => 'Register',
            'name'  => 'wpt_user_register_button',
            'id'    => 'wpt_user_register_button',
            'class' => 'button button-primary',
        ]); ?>
    </form>
    <div id="wpt-user-register-success" style="display:none;">
        <?php $activation_link = "<a href='" . WPT_CONFIG['wpt_user_activation_link'] . "'>Here.</a>"; ?>
        <h3>Registration Successful</h3>
        <p>Thank you for registering with us.</p>
        <p>Please check your email for activation code.</p>
        <p>You can activate your account by clicking on the link in the email. <?php echo $activation_link; ?></p>
        <p>You can now log in with your new account.</p>
        <p><br/><h4>Email</h4><br/></p>
        <div id="test_email"></div>
    </div>
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
                    displayErrorMessage('Please enter your first name.');
                    valid = false;
                }

                // Validation for last_name
                var last_name = $('#last_name').val().trim();
                if (last_name === '') {
                    displayErrorMessage('Please enter your last name.');
                    valid = false;
                }

                // Other field validations...

                // If all validations pass, proceed with AJAX submission
                if (valid) {
                    var formData = new FormData($('#wpt-user-register-form')[0]);
                    let WPT_AJAX = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
                    // Add nonce to the data
                    formData.append('action', 'wpt_register_user');
                    formData.append('wpt_register_nonce', '<?php echo wp_create_nonce("wpt_register_nonce"); ?>');

                    // Your AJAX code
                    $.ajax({
                        url: WPT_AJAX,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
    console.log('AJAX success:', response);

    // Parse the JSON response
    var jsonResponse = JSON.parse(response);

    // Check the response and display messages accordingly
    if (jsonResponse && jsonResponse.status == 200) {
        console.log('Registration successful');
        $('#response').html('<div class="success">' + jsonResponse.message + '</div>');
        $('#wpt-user-register-form').remove();
        $('#wpt-user-register-success').show();
        $('#test_email').html(jsonResponse.email_body);
    } else {
        console.log('Registration failed:', jsonResponse.message);
        displayErrorMessage(jsonResponse.message || 'Registration failed, please try again later.');
    }
},

                        error: function(xhr, textstatus, error) {
                            console.error('AJAX error:', error);
                            displayErrorMessage('Registration failed, please try again later.');
                        },
                    });
                }
            });

            // Helper function to display error messages
            function displayErrorMessage(message) {
                $('#response').html('<div class="error">' + message + '</div>');
            }

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
        $username = $first_name; // . '-' . $last_name;
        $wpt_user_activation_link = $_POST['wpt_user_activation_link'];
        $status = 400;
        $message = '';
        $redirect = '';

        // Validate the form data (you may want to add more robust validation)
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($billing_phone) || empty($billing_city) || empty($billing_postcode) || empty($billing_state) || empty($billing_country)) {
            $message = '<div class="error">Please fill in all required fields.</div>';
            $status = 400;
        } else {

            if (email_exists($email)) {
                $status = 422;
                $message = 'You already have registerd with us. Try loggin in with your email or username.';
            }

            if (username_exists($username)) {
                $status = 422;
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

            if ($IMG_UPL == 1) {

                // $confirmation_token = md5(uniqid(wp_rand(), true));

                $wpt_activation_code = _wpt_generate_random_string(5);
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
                    // 'confirmation_token' => $confirmation_token,
                    'wpt_activation_code' => $wpt_activation_code,
                ];

                foreach ($user_metadata as $key => $value) {
                    update_user_meta($user_id, $key, $value);
                }
                $website = get_bloginfo('name');
                // $activation_link = "<a href='".site_url('/activate')."'>This link</a>";
                $wpt_config = get_option('WPT_CONFIG'); 
                $email_body =  "Hello ".$first_name." ".$last_name. " and Welcome to  $website  <br /><br />
                You have successfully registered with us.<br />
                 $wpt_activation_code is your code for activation.<br />
                You can activate your account <a href='$wpt_user_activation_link'>Here</a> by entering this code.<br />
                <br />
                Regards,
                $website team.";
                // wp_mail($email, 'Welcome to ' . $website, $email_body, 'Content-type: text/html');
                $status = 200;
                $message = 'Registration successful.';
            } else {
                // Handle image upload error
                $message = '<div class="error">Failed to upload profile image.</div>';
                $status = 422;
            }
        }
    }

    echo json_encode(array(
        'status'   => $status,
        'message'  => $message,
        '$activation_link' => $activation_link,
         'email_body' => $email_body
    ));
    wp_die();
}

add_action('wp_ajax_wpt_register_user', 'wpt_register_user');
add_action('wp_ajax_nopriv_wpt_register_user', 'wpt_register_user');


// FOR ADMIN PANEL

// DISPLAY
function custom_user_profile_columns($columns)
{
    $columns['wpt_profile_image'] = 'Profile Image';
    return $columns;
}
add_filter('manage_users_columns', 'custom_user_profile_columns');

/*
// Add custom column data
function custom_user_profile_data($value, $column_name, $user_id) {
    if ($column_name === 'wpt_profile_image') {
        $profile_image = get_user_meta($user_id, 'wpt_profile_image', true);
        
        // Display the profile image
        if (!empty($profile_image)) {
            $image_html = '<img src="' . esc_url($profile_image) . '" alt="Profile Image" style="max-width: 50px; height: auto;" />';
            return $image_html;
        } else {
            return 'No Image';
        }
    }

    return $value;
}
add_filter('manage_users_custom_column', 'custom_user_profile_data', 10, 3);


//ADD
// Custom form in user profile page
function custom_user_profile_fields($user) {
    ?>
<h3><?php _e('Profile Image', 'your-textdomain'); ?></h3>
<table class="form-table">
    <tr>
        <th><label for="wpt_profile_image"><?php _e('Upload Image', 'your-textdomain'); ?></label></th>
        <td>
            <input type="file" name="wpt_profile_image" id="wpt_profile_image" />
            <span class="description"><?php _e('Upload your profile image.', 'your-textdomain'); ?></span>
        </td>
    </tr>
</table>
<?php
}
add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');

// Save custom field
function save_custom_user_profile_fields($user_id) {
    if (isset($_FILES['wpt_profile_image'])) {
        $attachment_id = media_handle_upload('wpt_profile_image', $user_id);
        if (!is_wp_error($attachment_id)) {
            update_user_meta($user_id, 'wpt_profile_image', wp_get_attachment_url($attachment_id));
        }
    }
}
add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');
*/
 

