<?php 
/**
 * Retrieves the post meta data for a given post ID.
 *
 * @param int $id The ID of the post.
 * @param string $return_type The format in which to return the data (default: 'json').
 *                           Valid values are 'array' and 'json'.
 * @throws None
 * @return mixed The post meta data, either as an array or as a JSON string.
 *               If successful, the return value will include the 'result', 'status', and 'message' keys.
 *               If unsuccessful, the return value will include the 'status' and 'message' keys.
 */
function wpt_get_postmeta_by_id($id, $return_type = 'json') {
    // Get post meta
    if(empty($id) || $id == ''){
        die('<div class="error">Please provide id</div>');
    }
    $postmeta = get_post_meta($id);
    $postmeta_arr = [];
    // Check if get_post_meta failed
    if (empty($postmeta)) {
        $status = 500; // Internal Server Error
        $message = 'Error retrieving post meta';
    } else {
        
        foreach ($postmeta as $key => $value) {
            // Unserialize the value if it is a serialized array
            $unserialized_value = maybe_unserialize($value[0]);

            // Check if maybe_unserialize failed
            if ($unserialized_value === false) {
                $status = 500; // Internal Server Error
                $message = 'Error unserializing post meta';
                break;
            }

            if (is_array($unserialized_value)) {
                // If the unserialized value is an array, add it as a key-value pair in the main array
                $postmeta_arr[$key] = $unserialized_value;
            } else {
                // If the unserialized value is not an array, add it as a regular key-value pair in the main array
                $postmeta_arr[$key] = $value[0];
            }
        }

        // If there were no errors during processing
        if (!isset($status)) {
            $status = 200;
            $message = 'success';
        }
    }

    if ($return_type === 'array') {
        return array(
            'result'  => $postmeta_arr,
            'status'  => $status,
            'message' => $message
        );
    } elseif ($return_type === 'json') {
        return json_encode(array(
            'result'  => $postmeta_arr,
            'status'  => $status,
            'message' => $message
        ));
    }
}

add_action('wp_ajax_wpt_get_postmeta_by_id_endpoint', 'wpt_get_postmeta_by_id_endpoint');
add_action('wp_ajax_nopriv_wpt_get_postmeta_by_id_endpoint', 'wpt_get_postmeta_by_id_endpoint');
function wpt_get_postmeta_by_id_endpoint()
{
    echo  wpt_get_posts_by_meta([
        'id'    => $_REQUEST['id'],
        'return_type' => $_REQUEST['return_type']
    ]);
    die();
}