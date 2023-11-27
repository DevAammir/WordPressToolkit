<?php
/**
 * Retrieves posts from the WordPress database based on specified categories.
 *
 * @param array $params An array of parameters for retrieving the posts.
 *              - 'size' (int): The size of the posts to retrieve. Default is 0.
 *              - 'post_type' (string): The type of posts to retrieve.
 *              - 'per_page' (int): The number of posts to retrieve per page. Default is -1.
 *              - 'category' (string): The category or categories to filter the posts by.
 *              - 'return_type' (string): The type of response to return. Either 'html' or 'json'.
 * @return mixed The response based on the specified return type.
 * @throws Some_Exception_Class A description of the exception that can be thrown.
 */
function wpt_get_posts_by_categories($params = array())
{
    if(empty($params) || 
    $params['post_type']=='' || 
    $params['size']=='' || 
    empty($params['category']) ||
    $params['category']=='' || 
    empty($params['return_type']) || 
    !isset($params['return_type']) || 
    $params['return_type']==''){
        echo "<div class='error'>Please provide all the required parameters: post_type, size, category and return_type</div>";
        die();
    } 

    $return_type = empty($params['return_type']) || !isset($params['return_type']) ?  'html' : $params['return_type'];
   
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
        }elseif ($return_type === 'array') {
            $response = _wpt_generate_array_response($query, $status, $message);
        }
    }

    wp_reset_postdata();
    return $response;
    }
    /**
     * Retrieves posts by categories using an endpoint.
     *
     * @param array $atts The attributes passed to the shortcode.
     *                    - per_page (int): The number of posts to retrieve per page. Defaults to -1 (all).
     *                    - post_type (string): The post type to retrieve.
     *                    - size (string): The size of the posts.
     *                    - category (string): The category of the posts.
     * @return string The HTML representation of the retrieved posts.
     */
    add_action('wp_ajax_wpt_get_posts_by_categories_endpoint', 'wpt_get_posts_by_categories_endpoint');
    add_action('wp_ajax_nopriv_wpt_get_posts_by_categories_endpoint', 'wpt_get_posts_by_categories_endpoint');

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

    /**
     * Retrieves posts by categories using a shortcode.
     *
     * @param array $atts The attributes passed to the shortcode.
     *                    - per_page (int): The number of posts to retrieve per page. Defaults to -1 (all).
     *                    - post_type (string): The post type to retrieve.
     *                    - size (string): The size of the posts.
     *                    - category (string): The category of the posts.
     * @return string The HTML representation of the retrieved posts.
     */
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
