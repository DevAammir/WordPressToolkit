<?php
/**
 * Retrieves posts by author based on the given parameters.
 *
 * @param array $params An array of parameters for the query.
 *                      - post_type (string): The post type to filter by.
 *                      - per_page (int): The number of posts per page.
 *                      - size (int): The length of the excerpt.
 *                      - author (string|int): The author's username or ID.
 * @throws Some_Exception_Class If the query fails.
 * @return mixed The response based on the return_type parameter.
 */
function wpt_get_posts_by_author($params = array())
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

    /**
     * Retrieves posts by author using the WordPress API endpoint.
     *
     * @throws Exception Thrown when the 'post_type', 'size', or 'author' parameters are missing.
     * @return string The HTML representation of the retrieved posts.
     */
    add_action('wp_ajax_wpt_get_posts_by_author_endpoint', 'wpt_get_posts_by_author_endpoint');
    add_action('wp_ajax_nopriv_wpt_get_posts_by_author_endpoint', 'wpt_get_posts_by_author_endpoint');
    function wpt_get_posts_by_author_endpoint()
    {
        echo  wpt_get_posts_by_author([
            'per_page'    => -1,
            'post_type'   => $_REQUEST['post_type'],
            'return_type' => 'html',
            'size' =>   $_REQUEST['size'],
            'author' =>   $_REQUEST['author'],
        ]);
        die();
    }


    /**
     * Generates a shortcode that retrieves posts by author.
     *
     * @param array $atts An associative array of shortcode attributes.
     *   - per_page (int) The number of posts to display per page.
     *   - post_type (string) The post type to retrieve.
     *   - return_type (string) The type of output to return.
     *   - size (string) The size of the output.
     *   - author (string) The author to retrieve posts for.
     * @throws Some_Exception_Class A description of the exception that may be thrown.
     * @return Some_Return_Value The generated output.
     */
    add_shortcode('wpt_get_posts_by_author', 'wpt_get_posts_by_author_shortcode');

    function wpt_get_posts_by_author_shortcode($atts)
    {
        return wpt_get_posts_by_author([
            'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
            'post_type'   => $atts['post_type'],
            'return_type' => 'html',
            'size' =>   $atts['size'],
            'author' =>   $atts['author'],
        ]);
    }
