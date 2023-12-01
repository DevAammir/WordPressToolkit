<?php
/**
 * Retrieves a post by its ID and returns it in the specified format.
 *
 * @param array $args {
 *     Optional. An array of arguments for retrieving the post.
 *
 *     @type int    $id           The ID of the post to retrieve. Default is empty.
 *     @type string $return_type  The format in which to return the post. Default is empty.
 * }
 * @throws None
 * @return string The retrieved post in the specified format.
 * @additional it has a Corresponding ajax function and a shortcode 
 *      - [wpt_get_post_by_id id='']
 *      - wpt_get_post_by_id(postId, target, wpt_ajax_url);
 */
function wpt_get_post_by_id($args = array())
{
    if($args == 'help'){wpt_get_post_by_id_help(); die();}
    // Check for required parameters
    if (empty($args['id']) || empty($args['return_type'])) {
        echo "<div class='error'>Please provide required parameters: id and return_type</div>";
        die();
    }

    $post = get_post($args['id']);

    if (!$post) {
        return _wpt_handle_no_posts($args['return_type']);
    }

    // Process the post based on return type
    switch ($args['return_type']) {
        case 'html':
            return _wpt_generate_html_response_for_posts_for_single_post($post);
        case 'json':
            return _wpt_generate_json_response_for_posts_for_single_post($post);
        case 'array':
            return _wpt_generate_array_response_for_posts_for_single_post($post);
        default:
            return _wpt_generate_json_response_for_posts_for_single_post($args);
    }
}

/**
 * Retrieves a post by its ID from the WordPress database and returns it.
 *
 * @param int $id The ID of the post to retrieve.
 * @param string $return_type The format in which the post should be returned. Defaults to 'html'.
 * @throws None
 * @return string The post content in the specified format.
 */
add_action('wp_ajax_wpt_get_post_by_id_endpoint', 'wpt_get_post_by_id_endpoint');
add_action('wp_ajax_nopriv_wpt_get_post_by_id_endpoint', 'wpt_get_post_by_id_endpoint');
function wpt_get_post_by_id_endpoint()
{
    $id = $_REQUEST['id'];
    $return_type = 'html';

    $args = array(
        'id'    => $_REQUEST['id'], //  
        'return_type'   => 'html'
    );
    echo  wpt_get_post_by_id($args);
    die();
}

/**
 * Retrieves a post by its ID using a shortcode.
 *
 * @param array $atts The attributes passed to the shortcode.
 *                    - 'id': The ID of the post to retrieve.
 * @throws Exception If the post cannot be found.
 * @return string The HTML content of the retrieved post.
 */
add_shortcode('wpt_get_post_by_id', 'get_post_by_id_shortcode');

function get_post_by_id_shortcode($atts)
{//[wpt_get_post_by_id id='']
    $args = array(
        'id'    => $atts['id'], //  
        'return_type'   => 'html'
    );
    return wpt_get_post_by_id($args);
}



function wpt_get_post_by_id_help()
{
?>
  <h3>wpt_get_post_by_id() help</h3>
  <code>
    $args = [
      'id'          => '',
      'return_type' => ''
    ];<br/><br/>
    wpt_get_post_by_id($args);<br/>
  </code><br/><br/>
  <p>Retrieves a post by its ID and returns it in the specified format.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>id</code> (int) - The ID of the post to retrieve. Default is empty.</li>
    <li><code>return_type</code> (string) - The format in which to return the post.  'html', 'array' or 'json'. Default is empty.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The retrieved post in the specified format.</p>

  <p><strong>Additional:</strong></p>
  <p>It has a Corresponding ajax function and a shortcode:</p>
  <ul>
    <li><code>[wpt_get_post_by_id id='']</code></li>
    <li><code>wpt_get_post_by_id(postId, target, wpt_ajax_url);</code></li>
  </ul>
<?php
}
