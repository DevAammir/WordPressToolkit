<?php 


/**
 * Creates a new user with the given parameters.
 *
 * @param array $params An array containing the user parameters:
 *                      - username (string): The username of the new user.
 *                      - email (string): The email address of the new user.
 *                      - password (string): The password of the new user.
 *                      - role (string, optional): The role of the new user. If not provided, the default role is 'subscriber'.
 * @throws None
 * @return array An array containing the result of the user creation:
 *               - username (string): The username of the created user.
 *               - email (string): The email address of the created user.
 *               - password (string): The password of the created user.
 *               - errors (array): An array of error codes, if any.
 *               - result (string|int): The result of the user creation. If successful, the user ID is returned.
 * 
 */
function wpt_create_user($params)
{
    if($params == 'help'){wpt_create_user_help();die();}
    $username = sanitize_text_field($params['username']);
    $email = sanitize_text_field($params['email']);
    $password = sanitize_text_field($params['password']);
    $default_role = !empty($params['role']) ? $params['role'] : 'subscriber'; // You can change this to the default role you prefer

    $data["username"] = $username;
    $data["email"] = $email;
    $data["password"] = $password;
    $errors = array();

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'EMPTY_FIELDS';
    }

    if (!empty($username)) {
        if (username_exists($username)) {
            $errors[] = 'USERNAME_ALREADY_EXIST';
        }
    }

    if (!empty($email)) {
        if (!is_email($email)) {
            $errors[] = 'NOT_A_VALID_EMAIL_ADDRESS';
        } else {
            if (email_exists($email)) {
                $errors[] = 'EMAIL_ADDRESS_ALREADY_EXIST';
            }
        }
    }

    if (!empty($password) && strlen($password) < 5) {
        $errors[] = 'PASSWORD_LENGTH_IS_TOO_SHORT';
    }

    if (count($errors)) {
        $data["errors"] = $errors;
        $data["result"] = 'FAIL';
    } else {
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        $user_id = wp_create_user($username, $random_password, $email);

        if (!is_wp_error($user_id)) {
            // Set the default role for the new user
            $user = new WP_User($user_id);
            $user->set_role($default_role);

            wp_set_password($password, $user_id);
            $data = $user_id;
        } else {
            $data["result"] = 'FAIL';
        }
    }

    return $data;
}







function wpt_create_user_help()
{
?>
  <h3>wpt_create_user() help</h3>
  <code>
    $params = [
      'username' => '',
      'email'    => '',
      'password' => '',
      'role'     => 'subscriber'
    ];<br/><br/>
    wpt_create_user($params);<br/>
  </code><br/><br/>
  <p>Creates a new user with the given parameters.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'username'</code> (string) - The username of the new user.</li>
    <li><code>'email'</code> (string) - The email address of the new user.</li>
    <li><code>'password'</code> (string) - The password of the new user.</li>
    <li><code>'role'</code> (string, optional) - The role of the new user. If not provided, the default role is 'subscriber'.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>An array containing the result of the user creation:</p>
  <ul>
    <li><code>'username'</code> (string) - The username of the created user.</li>
    <li><code>'email'</code> (string) - The email address of the created user.</li>
    <li><code>'password'</code> (string) - The password of the created user.</li>
    <li><code>'errors'</code> (array) - An array of error codes, if any.</li>
    <li><code>'result'</code> (string|int) - The result of the user creation. If successful, the user ID is returned.</li>
  </ul>

  <p><strong>Additional:</strong></p>
  <p>Usage:</p>
  <code><pre>
    $user_data = array(
      'user_login'    => 'new_user',        // User's login username (required)
      'user_pass'     => 'secure_password', // User's password (required)
      'user_email'    => 'user@example.com', // User's email address (required)
      'user_url'      => 'http://example.com', // User's website URL
      'first_name'    => 'John',            // User's first name
      'last_name'     => 'Doe',             // User's last name
      'nickname'      => 'john_doe',        // User's nickname
      'description'   => 'A brief description of the user', // User's description
      'rich_editing'  => 'true',            // Allow the user to access the rich editor for writing
      'user_registered' => '2023-01-01 12:00:00', // Date and time when the user registered
      'role'          => 'subscriber',      // User's role (default is 'subscriber')
    );

    </pre>
  </code>
<?php
}
