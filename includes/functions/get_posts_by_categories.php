<?php

function wpt_get_posts_by_categories($params = array())
{

    $size = empty($params['size']) ?  0 : $params['size'];

    $args = array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => empty($params['per_page']) ?  -1 : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        // 'orderby'        => 'date',
        // 'order'          => 'DESC',
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ?  0 : $params['size']
    );


    if (!empty($params['category'])) {
        $category = $params['category'];
    
        // Check if it's a comma-separated list
        if (strpos($category, ',') !== false) {
            
            $categories = array_map('trim', explode(',', $category));
    
            // Initialize arrays for category__in and tax_query
            $args['category__in'] = array();
            $args['tax_query'] = array('relation' => 'OR');
    
            foreach ($categories as $single) {
                if (is_numeric($single)) {
                    
                    $args['category__in'][] = $single; // Use 'category__in' for multiple category IDs
                } else {
                    $args['tax_query'][] = array(
                        'taxonomy' => 'category',
                        'field' => 'name',
                        'terms' => $single,
                    );
                }
            }
    
        } else {
            // Check if it's numeric (ID) or a string (name)
            if (is_numeric($category)) {
                $args['cat'] = $category; // Use 'cat' for a single category ID
            } else {
                $args['category_name'] = $category; // Use 'category_name' for a single category name
            }
        }
    }
        
    // dd($args);
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


    function wpt_get_posts_by_categories_endpoint()
    {
        // dd($_REQUEST);
        echo  wpt_get_posts_by_categories([
            'per_page'    => -1,
            'post_type'   => $_REQUEST['post_type'],
            'return_type' => 'html',
            'size' =>   $_REQUEST['size'],
            'category'  => $_REQUEST['category'],
        ]);
        die();
    }

    add_action('wp_ajax_wpt_get_posts_by_categories_endpoint', 'wpt_get_posts_by_categories_endpoint');
    add_action('wp_ajax_nopriv_wpt_get_posts_by_categories_endpoint', 'wpt_get_posts_by_categories_endpoint');

    add_shortcode('wpt_get_posts_by_categories', 'wpt_get_posts_by_categories_shortcode');

    function wpt_get_posts_by_categories_shortcode($atts)
    {
        // dd($atts);
        return wpt_get_posts_by_categories([
            'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
            'post_type'   => $atts['post_type'],
            'return_type' => 'html',
            'size' =>   $atts['size'],
            'category'  => $atts['category'],
        ]);
    }
