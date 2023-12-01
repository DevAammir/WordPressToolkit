<?php 


/**
 * Retrieves user data by ID.
 *
 * @param int $id The ID of the user.
 * @param string $return_type (optional) The return type. Defaults to 'object'. Possible values are 'object', 'array', and 'json'.
 * @return array The user data along with the status and message.
 *     - result (mixed) The user data.
 *     - status (string) The status of the operation ('success' or 'error').
 *     - message (string) The message associated with the status.
 */
function wpt_get_user_by_id($id, $return_type =null)
{
    if($id == 'help'){wpt_get_user_by_id_help();die();}
    if (empty($id) || empty($return_type)) {
        die("<div class='error'>Please provide a user ID and return type.</div>");
    }
    $user = get_user_by('ID', $id);
    if ($user) {
        $user_meta = get_user_meta($id);
        $user_data = (object) array_merge((array) $user->data, $user_meta);
        if ($return_type === 'array') {
            $user_data = (array) $user_data;
        } elseif ($return_type === 'json') {
            $user_data = json_encode($user_data);
        }
        return [
            'result' => $user_data,
            'status' => 'success',
            'message' => 'User data retrieved successfully'
        ];
    }
    return [
        'result' => 'User not found',
        'status' => 'error',
        'message' => 'User not found'
    ];
}









function wpt_get_user_by_id_help()
{
?>
  <h3>wpt_get_user_by_id() help</h3>
  <code>
    $id = 1;<br/>
    $return_type = 'object'; // Optional, defaults to 'object'. Possible values are 'object', 'array', and 'json'.<br/><br/>
    wpt_get_user_by_id($id, $return_type);<br/>
  </code><br/><br/>
  <p>Retrieves user data by ID.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>$id</code> (int) - The ID of the user.</li>
    <li><code>$return_type</code> (string) - Optional. The return type. Defaults to 'object'. Possible values are 'object', 'array', and 'json'.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The user data along with the status and message.</p>
  <ul>
    <li><code>'result'</code> (mixed) - The user data.</li>
    <li><code>'status'</code> (string) - The status of the operation ('success' or 'error').</li>
    <li><code>'message'</code> (string) - The message associated with the status.</li>
  </ul>
<?php
}
