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
 */
function wpt_get_post_by_id($args = array())
{
    $args = array(
        'id'    => $args['id'], 
        'return_type'   => $args['return_type']
    );
    $post = get_post($args['id']);

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
add_shortcode('get_post_by_id', 'get_post_by_id_shortcode');

function get_post_by_id_shortcode($atts)
{
    $args = array(
        'id'    => $atts['id'], //  
        'return_type'   => 'html'
    );
    return wpt_get_post_by_id($args);
}
