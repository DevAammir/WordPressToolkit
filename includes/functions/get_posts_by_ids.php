<?php


/**
 * Retrieves posts by their IDs.
 *
 * @param array $params An array of parameters.
 *   - size (int): The size of the posts. Default is 0.
 *   - posts_ids (array|string): The IDs of the posts.
 *   - per_page (int): The number of posts to display per page. Default is -1 (all posts).
 *   - return_type (string): The type of response to return. Default is 'html'.
 * @throws Some_Exception_Class Description of exception (if applicable)
 * @return mixed The response generated based on the return type.
 */
function wpt_get_posts_by_ids($params = array())
{


    $size = empty($params['size']) ?  0 : $params['size'];



    $posts_ids  = $params['posts_ids'];
    if (is_array($posts_ids)) {
        $posts_ids_array = $posts_ids;
    } else {
        $posts_ids_array = array_map('trim', explode(',', $posts_ids));
    }


    $args = array(
        'post_type'      => 'any',
        'post__in'       => $posts_ids_array,
        'posts_per_page' => empty($params['per_page']) ?  '-1' : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'orderby'        => 'post__in',
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ?  0 : $params['size'],
    );

    // dd($args,true);

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


    function wpt_get_posts_by_ids_endpoint()
    {
        $args = [
            'per_page'    => -1,
            'posts_ids'   => $_REQUEST['posts_ids'],
            'return_type' => 'html',
            'size' =>   $_REQUEST['size'],
        ];
        echo  wpt_get_posts_by_ids($args);
        die();
    }

    add_action('wp_ajax_wpt_get_posts_by_ids_endpoint', 'wpt_get_posts_by_ids_endpoint');
    add_action('wp_ajax_nopriv_wpt_get_posts_by_ids_endpoint', 'wpt_get_posts_by_ids_endpoint');

    add_shortcode('wpt_get_posts_by_ids', 'wpt_get_posts_by_ids_shortcode');

    function wpt_get_posts_by_ids_shortcode($atts)
    {
        $args = [
            'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
            'posts_ids'   => $atts['posts_ids'],
            'return_type' => 'html',
            'size' =>   $atts['size']
        ];
        return wpt_get_posts_by_ids($args);
    }
