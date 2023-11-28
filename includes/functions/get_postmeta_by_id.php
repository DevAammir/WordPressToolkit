<?php 
/**
 * Retrieves the post meta data for a given post ID.
 *
 * @param int $id The ID of the post.
 * @param string $return_type The format in which to return the data (default: 'json').
 *                           Valid values are 'array', 'json' and 'html'.
 * @throws None
 * @return mixed The post meta data, either as an array or as a JSON string.
 *               If successful, the return value will include the 'result', 'status', and 'message' keys.
 *               If unsuccessful, the return value will include the 'status' and 'message' keys.
 */
function wpt_get_postmeta_by_id($id, $return_type = 'json') {
    // Get post meta
    if (empty($id) || $id == '') {
        die('<div class="error">Please provide id</div>');
    }
    $postmeta = get_post_meta($id);
    $postmeta_arr = [];
    $html_output = ''; // Initialize HTML output

    // Check if get_post_meta failed
    if (empty($postmeta)) {
        $status = 500; // Internal Server Error
        $message = 'Error retrieving post meta';
    } else {
$n = 0;
        foreach ($postmeta as $key => $value) { $n++;
            // Unserialize the value if it is a serialized array
            $unserialized_value = maybe_unserialize($value[0]);

            // Check if maybe_unserialize failed
            if ($unserialized_value === false) {
                $status = 500; // Internal Server Error
                $message = 'Error unserializing post meta';
                break;
            }

            // Add the HTML representation
            $html_output .= '<p class="the-meta meta-' . $key . ' meta-' . $n . '">';
            $html_output .= '<span class="key" data-key="' . $key . '">' . $key . '</span>';

            if (is_array($unserialized_value)) {
                // If the unserialized value is an array, add it as a key-value pair in the main array
                $postmeta_arr[$key] = $unserialized_value;

                // Add list for array values
                $html_output .= '<span class="value" data-value="' . $val[0] . '"><ul>';
                foreach ($unserialized_value as $item) {
                    $html_output .= '<li>' . $item . '</li>';
                }
                $html_output .= '</ul></span>';
            } else {
                // If the unserialized value is not an array, add it as a regular key-value pair in the main array
                $postmeta_arr[$key] = $value[0];
                $html_output .= '<span class="value" data-value="' . $value[0] . '">' . $value[0] . '</span>';
            }

            $html_output .= '</p>';
        }

        // If there were no errors during processing
        if (!isset($status)) {
            $status = 200;
            $message = 'success';
        }
    }

    // Add the HTML representation to the return data
    $return_data = array(
        'result'  => $postmeta_arr,
        'status'  => $status,
        'message' => $message,
    );

    if ($return_type === 'array') {
        return $return_data;
    } elseif ($return_type === 'json') {
        return json_encode($return_data);
    }elseif ($return_type === 'html') {
        return $html_output;
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