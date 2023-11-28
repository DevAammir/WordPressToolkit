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
    if(empty($params) || $params['post_name']=='' ||empty($params['return_type']) || !isset($params['return_type']) || $params['return_type']==''){
        echo "<div class='error'>Please provide required parameters that are: post_name and return_type</div>";
        die();
    } 

    $return_type = empty($params['return_type']) || !isset($params['return_type']) ?  'html' : $params['return_type'];
   
    $args = array(
        'post_name'     => $args['post_name'],
        'post_type'     => $args['post_type'],
        'return_type'   => $args['return_type']
    );
    $post = get_page_by_path($args['post_name'], OBJECT, $args['post_type']);

    if (!$post) {
        if ($args['return_type'] === 'html') {
            $response = '<div class="error">Post not found</div>';
        } else {
            $response = json_encode(array(
                'result'  => 'Post not found',
                'status'  => 404,
                'message' => 'Post not found'
            ));
        }
    } else {
        // global $post;
        $slug = $post->post_name;
        $status = 200;
        $message = 'success';

        if ($args['return_type'] === 'html') {
            $category = get_the_category($post->ID);
            $category_class = $category ? ($category[0]->slug) : '';
            ob_start();
?>
<div class="post-wrapper <?php echo $category_class . ' post-' . $post->ID; ?> <?php echo $slug; ?>">
    <h3><?php echo $post->post_title; ?></h3>
    <div class="featuerd-image-wrapper">
        <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'full'); ?>" class="featuerd-image" />
    </div>
    <div class="meta-info">
        <p class="the-date">Date: <?php echo get_the_date('F j, Y', $post->ID); ?></p>
        <p class="the-author">Author: <?php echo get_the_author_meta('display_name', $post->post_author); ?></p>
        <p class="the-categories">Categories: <?php echo get_the_category_list(', ', '', $post->ID); ?> </p>
        <p class="the-tags">Tags: <?php echo get_the_tag_list('', ', ', '', $post->ID); ?></p>
    </div>
    <div class="post-content">
        <?php echo $post->post_content; ?>
    </div>
    <div class="wpt-post-meta">
        <?php
                    $postmeta = get_post_meta($post->ID);
                    if (!empty($postmeta)) : $n = 0;
                        foreach ($postmeta as $key => $val) : $n++;
                    ?>
        <p class="the-meta meta-<?php echo $key; ?> meta-<?php echo $n; ?>">
            <span class="key" data-key="<?php echo $key; ?>"><?php echo $key; ?></span>
            <span class="value" data-value="<?php echo $val[0]; ?>"><?php echo $val[0]; ?></span>
        </p>
        <?php
                        endforeach;
                    endif; ?>
    </div>
</div>
<?php

            $content = ob_get_clean();
            $response = $content;
        } elseif ($args['return_type'] === 'json') {
            $response = json_encode(array(
                'result'  => $post,
                'status'  => $status,
                'message' => $message
            ));
        }
    }
    return $response;
    // wp_die();
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