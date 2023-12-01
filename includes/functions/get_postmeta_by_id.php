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
 * @additional it has a corresponding ajax function and a shortcode
 *      -  [wpt_get_postmeta_by_id id="1"]
 *      -  wpt_get_postmeta_by_id(id, return_type, target, wpt_ajax_url)
 */


function wpt_get_postmeta_by_id($id, $return_type=null)
{
    if ($id === 'help' && (!isset($return_type) || empty($return_type) ||$return_type === null || $return_type === '')) {
        wpt_get_postmeta_by_id_help();
        die();
    }
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
    } elseif ($return_type == 'array' || $return_type == '' ) {
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


add_shortcode('wpt_get_postmeta_by_id', 'wpt_get_postmeta_by_id_shortcode');

function wpt_get_postmeta_by_id_shortcode($atts)
{//[wpt_get_postmeta_by_id id="1"]
    $atts = array(
        'id'    => $atts['id'], 
        'return_type'   => 'html'
    );
    return wpt_get_postmeta_by_id($atts);
}



function wpt_get_postmeta_by_id_help()
{
?>
  <h3>wpt_get_postmeta_by_id() help</h3>
  <code>
    $id = 1;<br/>
    $return_type = 'json';<br/><br/>
    wpt_get_postmeta_by_id($id, $return_type);<br/>
  </code><br/><br/>
  <p>Retrieves the post meta data for a given post ID.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>$id</code> (int) - The ID of the post.</li>
    <li><code>$return_type</code> (string) - The format in which to return the data (default: 'json').
      Valid values are 'array', 'json', and 'html'.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The post meta data, either as an array or as a JSON string.</p>
  <p>If successful, the return value will include the 'result', 'status', and 'message' keys.</p>
  <p>If unsuccessful, the return value will include the 'status' and 'message' keys.</p>

  <p><strong>Additional:</strong></p>
  <p>It has a corresponding ajax function and a shortcode:</p>
  <ul>
    <li><code>[wpt_get_postmeta_by_id id="1"]</code></li>
    <li><code>wpt_get_postmeta_by_id(id, return_type, target, wpt_ajax_url);</code></li>
  </ul>
<?php
}
