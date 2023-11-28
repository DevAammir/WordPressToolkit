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


function wpt_get_postmeta_by_id($id, $return_type)
{
    if (empty($id) || $id == '') {
        die('<div class="error">Please provide id</div>');
    }
    $postmeta = get_post_meta($id);
    $postmeta_arr = [];
    $html_output = ''; // Initialize HTML output

    foreach ($postmeta as $key => $value) {
        $postmeta_arr[$key] = $value[0];
    }
    if ($return_type == 'json') {
        $response = json_encode([
            'result'  => $postmeta_arr,
            'status'  => 200,
            'message' => 'success'
        ]);
        return $response;
    } elseif ($return_type == 'array') {
        return _wpt_convert_to_nested_array($postmeta_arr);
    } 
    elseif ($return_type == 'html') {
        return _wpt_generate_nested_table($postmeta_arr);    
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
