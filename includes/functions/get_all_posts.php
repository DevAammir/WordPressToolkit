<?php

/**
 * Retrieves posts based on the provided parameters.
 *
 * @param array $params An associative array of parameters (default: empty array)
 *
 *     - 'per_page'    (int)    The number of posts to retrieve per page (default: -1)
 *     - 'post_type'   (string) The post type to retrieve (default: 'post')
 *     - 'return_type' (string) The type of data to return ('html' or 'json') (default: 'html')
 *     - 'size'        (mixed)  The size of the excerpt or 0 for full content (default: '')
 *
 * @throws WP_Error If an error occurs while querying the posts
 *
 * @return string The retrieved posts in the specified format
 * 
 * @additional it has a shortcode and a callback function as well 
 * - [wpt_get_posts_shortcode per_page="10" post_type="post" size="10"]
 * - wpt_get_post_by_id(postId, target,wpt_ajax_url) 
 */

function wpt_get_posts($params = array())
{
    if($params=='help'){wpt_get_posts_help(); die();}
    
    $default_params = array(
        'per_page'    => -1,
        'post_type'   => 'post',
        'return_type' => 'html',
        'size'        => '',
    );

    $params = wp_parse_args($params, $default_params);
    $size = empty($params['size']) ? 0 : $params['size'];

    $args = array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => empty($params['per_page']) ? -1 : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ? 0 : $params['size'],
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        $response = _wpt_handle_no_posts($params);
    } else {
        $status = 200;
        $message = 'success';

        if ($params['return_type'] === 'html') {
            $response = _wpt_generate_html_response_for_posts($query, $params);
        } elseif ($params['return_type'] === 'json') {
            $response = _wpt_generate_json_response_for_posts($query, $status, $message);
        }elseif ($return_type === 'array') {
            $response = _wpt_generate_array_response_for_posts($query, $status, $message);
        }
    }

    wp_reset_postdata();
    return $response;
}

/**
 * Retrieves the posts endpoint and returns the HTML content.
 *
 * @throws Some_Exception_Class An exception that may occur during the execution of the function.
 * @return Some_Return_Value The HTML content of the posts endpoint.
 */
add_action('wp_ajax_wpt_get_posts_endpoint', 'wpt_get_posts_endpoint');
add_action('wp_ajax_nopriv_wpt_get_posts_endpoint', 'wpt_get_posts_endpoint');
function wpt_get_posts_endpoint()
{
    echo  wpt_get_posts([
        'per_page'    => -1,
        'post_type'   => $_REQUEST['post_type'],
        'return_type' => 'html',
        'size' =>   $_REQUEST['size'],
    ]);
    die();
}


/**
 * Retrieves a list of posts using the WordPress get_posts function and returns them as HTML.
 *
 * @param array $atts An array of attributes passed to the shortcode function.
 *   - per_page (int) The number of posts to retrieve per page. Default is -1 (retrieve all posts).
 *   - post_type (string) The type of posts to retrieve. Required.
 *   - return_type (string) The type of data to return. Default is 'html'.
 *   - size (string) The size of the posts to retrieve. Required.
 * @return string The HTML representation of the retrieved posts.
 */
add_shortcode('wpt_get_posts', 'wpt_get_posts_shortcode');

function wpt_get_posts_shortcode($atts)
{ //[wpt_get_posts_shortcode per_page="10" post_type="post" size="10"]
    return wpt_get_posts([
        'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
        'post_type'   => $atts['post_type'],
        'return_type' => 'html',
        'size' =>   $atts['size'],
    ]);
}




//HELP
function wpt_get_posts_help()
{
?>
  <h3>wpt_get_posts() help</h3>
  <code>
    $params = [
      'per_page'    => -1,
      'post_type'   => 'post',
      'return_type' => 'html',
      'size'        => ''
    ];<br/><br/>
    wpt_get_posts($params);<br/>
  </code><br/><br/>
  <p>Retrieves posts based on the provided parameters.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'per_page'</code> (int) - The number of posts to retrieve per page (default: -1)</li>
    <li><code>'post_type'</code> (string) - The post type to retrieve (default: 'post')</li>
    <li><code>'return_type'</code> (string) - The type of data to return ('html' , 'array' or 'json') (default: 'html')</li>
    <li><code>'size'</code> (mixed) - The size of the excerpt or 0 for full content (default: '')</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The retrieved posts in the specified format</p>

  <p><strong>Additional:</strong></p>
  <p>It has a shortcode and a callback function as well:</p>
  <ul>
    <li><code>[wpt_get_posts_shortcode per_page="10" post_type="post" size="10"]</code></li>
    <li><code>wpt_get_post_by_id(postId, target, wpt_ajax_url);</code></li>
  </ul>
<?php
}
