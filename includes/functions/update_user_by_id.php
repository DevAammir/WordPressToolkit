<?php 


/**
 * Update User by ID with Error Handling
 *
 * @param int    $user_id     The ID of the user to update.
 * @param array  $user_data   An associative array of user data to update.
 *
 * @return bool|WP_Error      1 on success, WP_Error on failure.
 */
function wpt_update_user_by_id($user_id, $user_data=null)
{
    if($user_id == 'help'){wpt_update_user_by_id_help();die();}
    // Ensure user ID and data are provided
    if (empty($user_id) || empty($user_data)) {
        return new WP_Error('invalid_params', 'Invalid parameters provided.');
    }

    // Check if password is provided and update it separately
    if (isset($user_data['password'])) {
        wp_set_password($user_data['password'], $user_id);
        unset($user_data['password']); // Remove password from user data array
    }

    // Update user data
    $updated = wp_update_user(array_merge(['ID' => $user_id], $user_data));

    // Check for errors during update
    if (is_wp_error($updated)) {
        return $updated; // Return WP_Error on failure
    }

    // Check if user was successfully updated
    if ($updated > 0) {
        return 1; // Return true on success
    } else {
        return new WP_Error('update_failed', 'User update failed.');
    }
}


function wpt_update_user_by_id_help()
{
?>
  <h3>wpt_update_user_by_id() help</h3>
  <code>
    $user_id = 1;<br/>
    $user_data = [
      'user_login' => 'new_username',
      'user_email' => 'new_email@example.com',
      'user_pass'  => 'new_password'
    ];<br/><br/>
    wpt_update_user_by_id($user_id, $user_data);<br/>
  </code><br/><br/>
  <p>Update User by ID with Error Handling.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>$user_id</code> (int) - The ID of the user to update.</li>
    <li><code>$user_data</code> (array) - An associative array of user data to update.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>1 on success, WP_Error on failure.</p>

  <p><strong>Additional:</strong></p>
  <p>Usage:</p>
  <code><pre>
  $user_data = array(
    'ID'               => 1,                  // ID of the user to update (required) but in this case we have that separately
    'user_login'       => 'updated_user',     // User's login username
    'user_pass'        => 'updated_password', // User's password
    'user_email'       => 'updated_user@example.com', // User's email address
    'user_url'         => 'http://updated-example.com', // User's website URL
    'first_name'       => 'Updated',          // User's first name
    'last_name'        => 'User',             // User's last name
    'nickname'         => 'updated_nickname', // User's nickname
    'description'      => 'Updated description of the user', // User's description
    'rich_editing'     => 'true',             // Allow the user to access the rich editor for writing
    'user_registered'  => '2023-01-01 12:00:00', // Date and time when the user registered
    'role'             => 'subscriber',       // User's role (default is 'subscriber')
    'display_name'     => 'Updated User',     // User's display name
    'spam'             => 'ham',              // Whether the user should be treated as spam ('ham' or 'spam')
    'deleted'          => '0',                // Whether the user has been marked for deletion (0 or 1)
    'locale'           => 'en_US',            // User's locale
    'avatar_default'   => 'gravatar_default', // Default avatar for the user
    'avatar_rating'    => 'G',                // Avatar rating for the user
    'comment_shortcuts' => 'false',           // Enable keyboard shortcuts for comment moderation
    'admin_color'      => 'fresh',            // Admin color scheme for the user
    'use_ssl'          => '0',                // Use SSL for the user's admin area (0 or 1)
);



    </pre>
  </code>
<?php
}
