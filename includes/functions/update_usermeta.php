<?php

/**
 * Updates the user meta data with the provided arguments.
 *
 * @param array $args An array containing the following keys:
 *   - id (int) The user ID.
 *   - meta_key (string) The meta key.
 *   - meta_value (mixed) The meta value to set.
 * @return string JSON-encoded string containing the following keys:
 *   - result (string) The result of the operation (success or failed).
 *   - status (int) The HTTP status code.
 *   - message (string) A message describing the result of the operation.
 */
function wpt_update_usermeta($args)
{
    if ($args == 'help') {
        wpt_update_usermeta_help();
        die();
    }

    $id = $args['id'];
    $meta_key = $args['meta_key'];
    $meta_value = $args['meta_value'];

    // Check if the usermeta already exists
    $existing_meta = get_user_meta($id, $meta_key, true);

    // Convert both existing and new values to strings for comparison
    $existing_meta_str = is_array($existing_meta) ? json_encode($existing_meta) : (string) $existing_meta;
    $meta_value_str = is_array($meta_value) ? json_encode($meta_value) : (string) $meta_value;

    if ($existing_meta_str === $meta_value_str) {
        // Values are the same, consider it a success
        $result = 'success';
        $status = 200;
        $message = 'Usermeta is already up to date';
    } else {
        // Update or add usermeta
        $update_result = update_user_meta($id, $meta_key, $meta_value);

        if ($update_result) {
            $result = 'success';
            $status = 200;
            $message = 'Usermeta updated successfully';
        } else {
            // Try to add usermeta if updating fails
            $add_result = add_user_meta($id, $meta_key, $meta_value, true);

            if ($add_result) {
                $result = 'success';
                $status = 201;
                $message = 'Usermeta added successfully';
            } else {
                $result = 'failed';
                $status = 500;
                $message = 'Error updating or adding usermeta';
            }
        }
    }

    return json_encode(array(
        'result'  => $result,
        'status'  => $status,
        'message' => $message
    ));
}

function wpt_update_usermeta_help()
{
?>
  <h3>wpt_update_usermeta() help</h3>
  <code>
        $params = ['id'=>2, 'meta_key'=>'key', 'meta_value'=>'value'];<br/><br/>
        wpt_update_usermeta($params);<br/>
    </code><br/><br/>
    <p>Updates the user meta data with the provided arguments.</p>

    <p><strong>Parameters:</strong></p>
    <ol>
        <li><code>id</code> (int) - The user ID.</li>
        <li><code>meta_key</code> (string) - The meta key.</li>
        <li><code>meta_value</code> (mixed) - The meta value to set.</li>
    </ol>

    <p><strong>Returns:</strong></p>
    <p>JSON-encoded string containing the following keys:</p>
    <ol>
        <li><code>result</code> (string) - The result of the operation (success or failed).</li>
        <li><code>status</code> (int) - The HTTP status code.</li>
        <li><code>message</code> (string) - A message describing the result of the operation.</li>
    </ol>
<?php
}
