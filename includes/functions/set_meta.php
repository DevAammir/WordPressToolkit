<?php 
/**
 * Retrieves or updates meta data for a specified object.
 *
 * @param array $args The arguments for retrieving or updating meta data.
 *                    - id: The ID of the object.
 *                    - meta_key: The meta key to retrieve or update.
 *                    - meta_value: The new meta value to update, if applicable.
 *                    - meta_type: The type of meta data (e.g. 'user', 'post', 'term').
 * @return string JSON-encoded response containing the result, status, and message.
 *                - result: The result of the operation ('success' or 'failed').
 *                - status: The HTTP status code.
 *                - message: The result message.
 */
function wpt_set_meta($args)
{
    if($args == 'help'){wpt_set_meta_help();die();}
    $id = $args['id'];
    $meta_key = $args['meta_key'];
    $meta_value = $args['meta_value'];
    $meta_type = $args['meta_type']; // 'user', 'post', 'term', etc.

    // Check the meta type and get the existing meta data
    switch ($meta_type) {
        case 'user':
            $existing_meta = get_user_meta($id, $meta_key, true);
            break;
        case 'post':
            $existing_meta = get_post_meta($id, $meta_key, true);
            break;
        case 'term':
            $existing_meta = get_term_meta($id, $meta_key, true);
            break;
            // Add more cases for other meta types as needed
        default:
            $existing_meta = null;
            break;
    }

    // Convert both existing and new values to strings for comparison
    $existing_meta_str = is_array($existing_meta) ? json_encode($existing_meta) : (string) $existing_meta;
    $meta_value_str = is_array($meta_value) ? json_encode($meta_value) : (string) $meta_value;

    if ($existing_meta_str === $meta_value_str) {
        // Values are the same, consider it a success
        $result = 'success';
        $status = 200;
        $message = ucfirst($meta_type) . ' meta is already up to date';
    } else {
        // Update or add meta
        $update_result = update_metadata($meta_type, $id, $meta_key, $meta_value);

        if ($update_result) {
            $result = 'success';
            $status = 200;
            $message = ucfirst($meta_type) . ' meta updated successfully';
        } else {
            // Try to add meta if updating fails
            $add_result = add_metadata($meta_type, $id, $meta_key, $meta_value, true);

            if ($add_result) {
                $result = 'success';
                $status = 201;
                $message = ucfirst($meta_type) . ' meta added successfully';
            } else {
                $result = 'failed';
                $status = 500;
                $message = 'Error updating or adding ' . $meta_type . ' meta';
            }
        }
    }

    return json_encode(array(
        'result'  => $result,
        'status'  => $status,
        'message' => $message
    ));
}


function wpt_set_meta_help()
{
?>
  <h3>wpt_set_meta() help</h3>
  <code>
    $args = [
      'id'        => '',
      'meta_key'  => '',
      'meta_value'=> '',
      'meta_type' => ''
    ];<br/><br/>
    wpt_set_meta($args);<br/>
  </code><br/><br/>
  <p>Retrieves or updates meta data for a specified object.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'id'</code> - The ID of the object.</li>
    <li><code>'meta_key'</code> - The meta key to retrieve or update.</li>
    <li><code>'meta_value'</code> - The new meta value to update, if applicable.</li>
    <li><code>'meta_type'</code> - The type of meta data (e.g., 'user', 'post', 'term').</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>JSON-encoded response containing the result, status, and message.</p>
  <ul>
    <li><code>'result'</code> - The result of the operation ('success' or 'failed').</li>
    <li><code>'status'</code> - The HTTP status code.</li>
    <li><code>'message'</code> - The result message.</li>
  </ul>
<?php
}
