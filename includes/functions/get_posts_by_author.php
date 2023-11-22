<?php

function get_posts_by_author($params = array())
{
    $size = empty($params['size']) ?  0 : $params['size'];

    $args = array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => empty($params['per_page']) ?  -1 : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ?  0 : $params['size'],
    );
    if (!empty($params['author'])) {
        $author_data = get_user_by('login', $params['author']);
        if ($author_data) {
            $args['author'] = $author_data->ID;
        } elseif (is_numeric($params['author'])) {
            $args['author'] = $params['author'];
        }
    }
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


    function get_posts_by_author_endpoint()
    {
        echo  get_posts_by_author([
            'per_page'    => -1,
            'post_type'   => $_REQUEST['post_type'],
            'return_type' => 'html',
            'size' =>   $_REQUEST['size'],
            'author' =>   $_REQUEST['author'],
        ]);
        die();
    }

    add_action('wp_ajax_get_posts_by_author_endpoint', 'get_posts_by_author_endpoint');
    add_action('wp_ajax_nopriv_get_posts_by_author_endpoint', 'get_posts_by_author_endpoint');

    add_shortcode('get_posts_by_author', 'get_posts_by_author_shortcode');

    function get_posts_by_author_shortcode($atts)
    {
        return get_posts_by_author([
            'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
            'post_type'   => $atts['post_type'],
            'return_type' => 'html',
            'size' =>   $atts['size'],
            'author' =>   $atts['author'],
        ]);
    }
