<?php
/**
 * Retrieves a post by its name.
 *
 * @param array $args An array of arguments:
 *     - post_name (string): The name of the post.
 *     - post_type (string): The type of the post.
 *     - return_type (string): The type of the response (html or json).
 * @return string The response containing the post information.
 */
function wpt_get_post_by_name($args = array())
{
    if(empty($args) || !isset($args['post_name'])  || $args['post_name']=='' || !isset($args['post_type'])  || $args['post_type']=='' ||empty($args['return_type']) || !isset($args['return_type']) || $args['return_type']==''){
        echo "<div class='error'>Please provide required parameters that are: post_name, post_type and return_type</div>";
        die();
    } 

    $return_type = empty($args['return_type']) || !isset($args['return_type']) ?  'html' : $args['return_type'];
   
    $args = array(
        'post_name'     => $args['post_name'],
        'post_type'     => $args['post_type'],
        'return_type'   => $args['return_type']
    );
    $post = get_page_by_path($args['post_name'], OBJECT, $args['post_type']);

    if (!$post) {
        return _wpt_handle_no_posts($args['return_type']);
    }

    // Process the post based on return type
    switch ($args['return_type']) {
        case 'html':
            return _wpt_generate_html_response_for_single_post($post);
        case 'json':
            return _wpt_generate_json_response_for_single_post($post);
        case 'array':
            return _wpt_generate_array_response_for_single_post($post);
        default:
            return _wpt_generate_json_response_for_single_post($args);
    }
}

/**
 * Retrieves a post by its name using the WordPress REST API.
 *
 * @param array $args The arguments for retrieving the post.
 *                    - post_name (string): The name of the post.
 *                    - post_type (string): The type of the post.
 *                    - return_type (string): The type of the return value.
 * @throws None
 * @return string The retrieved post in HTML format.
 */
add_action('wp_ajax_wpt_get_post_by_name_endpoint', 'wpt_get_post_by_name_endpoint');
add_action('wp_ajax_nopriv_wpt_get_post_by_name_endpoint', 'wpt_get_post_by_name_endpoint');
function wpt_get_post_by_name_endpoint()
{
    $args = array(
        'post_name'    => $_REQUEST['post_name'],
        'post_type'   => $_REQUEST['post_type'],
        'return_type'   => 'html'
    );
    echo  wpt_get_post_by_name($args);
    die();
}

/**
 * Get a post by name using a shortcode.
 *
 * @param array $atts An associative array of attributes.
 *                    - post_name (string): The name of the post.
 *                    - post_type (string): The type of the post.
 * @throws Some_Exception_Class Description of exception (if any).
 * @return string The HTML representation of the post.
 */
add_shortcode('get_post_by_name', 'get_post_by_name_shortcode');

function get_post_by_name_shortcode($atts)
{
    $args = array(
        'post_name'    => $atts['post_name'], // -1 to display all posts
        'post_type'   => $atts['post_type'],
        'return_type'   => 'html'
    );
    return wpt_get_post_by_name($args);
}