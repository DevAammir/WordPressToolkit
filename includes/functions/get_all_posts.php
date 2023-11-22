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
 */

function wpt_get_posts($params = array())
{
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
            $response = _wpt_generate_html_response($query, $params);
        } elseif ($params['return_type'] === 'json') {
            $response = _wpt_generate_json_response($query, $status, $message);
        }
    }

    wp_reset_postdata();
    return $response;
}

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

add_action('wp_ajax_wpt_get_posts_endpoint', 'wpt_get_posts_endpoint');
add_action('wp_ajax_nopriv_wpt_get_posts_endpoint', 'wpt_get_posts_endpoint');

add_shortcode('wpt_get_posts', 'wpt_get_posts_shortcode');

function wpt_get_posts_shortcode($atts)
{
    return wpt_get_posts([
        'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
        'post_type'   => $atts['post_type'],
        'return_type' => 'html',
        'size' =>   $atts['size'],
    ]);
}
