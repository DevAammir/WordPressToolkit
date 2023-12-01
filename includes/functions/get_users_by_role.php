<?php 


/**
 * Retrieves users by their role.
 *
 * @param string $role The role of the users to retrieve.
 * @return array Returns an array containing the user data. based on return type array or json
 */
function wpt_get_users_by_role($role, $return_type=null)
{
    if($role == 'help'){wpt_get_users_by_role_help();die();}
    // Check if the role parameter is provided
    if (empty($role)) {
        return [
            'result'  => 'Role parameter is missing',
            'status'  => 'error',
            'message' => 'Role parameter is required',
            'total'   => 0
        ];
    }

    // Set up query arguments to get users by role
    $query_args = array(
        'role' => $role,
    );

    // Get users with the specified role
    $users = get_users($query_args);

    // Return the result of _wpt_user_data_with_metadata
    return _wpt_user_data_with_metadata($users, $return_type);
}






function wpt_get_users_by_role_help()
{
?>
  <h3>wpt_get_users_by_role() help</h3>
  <code>
    $role = 'subscriber';<br/>
    wpt_get_users_by_role($role);<br/>
  </code><br/><br/>
  <p>Retrieves users by their role.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>$role</code> (string) - The role of the users to retrieve.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>Returns an array containing the user data based on return type array or json.</p>
<?php
}
