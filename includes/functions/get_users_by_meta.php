<?php 


/**
 * Retrieves users based on meta key and/or meta value.
 *
 * @param array $params The parameters for the function.
 *                     - meta_key: (string) The meta key to search for.
 *                     - meta_value: (string) The meta value to search for.
 *                     - return_type: (string) The type of data to return. Default is "object".
 * @return array The result of the function.
 *               - result: (mixed) The retrieved user data.
 *               - status: (string) The status of the result. Possible values are "success" or "error".
 *               - message: (string) A message providing information about the result.
 *               - total: (int) The total number of unique users retrieved.
 * @throws None
 */
function wpt_get_users_by_meta($params)
{
    if($params == 'help'){wpt_get_users_by_meta_help();die();}
    $meta_key    = isset($params['meta_key']) ? $params['meta_key'] : null;
    $meta_value  = isset($params['meta_value']) ? $params['meta_value'] : null;
    $return_type = isset($params['return_type']) ? $params['return_type'] : 'object';

    if ((empty($meta_key) && empty($meta_value)) || empty($return_type)) {
        die("<div class='error'>Please provide either meta key or meta value, and return type.</div>");
    }

    // Define the query args based on the provided meta key and value
    $query_args = [];
    if (!empty($meta_key)) {
        $query_args['meta_key'] = $meta_key;
    }
    if (!empty($meta_value)) {
        $query_args['meta_value'] = $meta_value;
    }

    // Get users with the specified meta key and/or value
    $users = get_users($query_args);

    return _wpt_user_data_with_metadata($users, $return_type);
}







function wpt_get_users_by_meta_help()
{
?>
  <h3>wpt_get_users_by_meta() help</h3>
  <code>
    $params = [
      'meta_key'    => '',
      'meta_value'  => '',
      'return_type' => 'object'
    ];<br/><br/>
    wpt_get_users_by_meta($params);<br/>
  </code><br/><br/>
  <p>Retrieves users based on meta key and/or meta value.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'meta_key'</code> (string) - The meta key to search for.</li>
    <li><code>'meta_value'</code> (string) - The meta value to search for.</li>
    <li><code>'return_type'</code> (string) - The type of data to return. Default is "object".</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The result of the function.</p>
  <ul>
    <li><code>'result'</code> (mixed) - The retrieved user data.</li>
    <li><code>'status'</code> (string) - The status of the result. Possible values are "success" or "error".</li>
    <li><code>'message'</code> (string) - A message providing information about the result.</li>
    <li><code>'total'</code> (int) - The total number of unique users retrieved.</li>
  </ul>

  <p><strong>Throws:</strong></p>
  <p>None</p>
<?php
}
