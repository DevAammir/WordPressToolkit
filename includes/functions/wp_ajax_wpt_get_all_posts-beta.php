<?php 

/* * *
 *  AJAX GET ALL POSTS
 * * */
add_action('wp_ajax_wpt_get_all_posts', 'wpt_get_all_posts');
add_action('wp_ajax_nopriv_wpt_get_all_posts', 'wpt_get_all_posts');
add_shortcode('wpt_all_posts', 'wpt_all_posts_shortcode');

function wpt_get_all_posts($atts = array())
{
    $result = '';
    $message = '';

    try {
        if (isset($_REQUEST['post_type'])) {
            $post_type = $_REQUEST['post_type'];
        } else {
            $post_type = isset($atts['post_type']) ? $atts['post_type'] : 'post';
        }

        $per_page = isset($atts['per_page']) ? intval($atts['per_page']) : -1;
        $paged = max(1, get_query_var('paged'));

        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => $per_page,
            'paged'          => $paged,
        );

        $posts_query = new WP_Query($args);

        if ($posts_query->have_posts()) {
            ob_start();
            while ($posts_query->have_posts()) {
                $posts_query->the_post();
                global $post;
                $post_categories = implode('  ', wp_list_pluck(get_the_category(), 'name'));
                $post_tags = implode(' ', wp_list_pluck(get_the_tags(), 'name'));

    ?>
                <div class="post post-<?php echo get_the_ID(); ?>  post-<?php echo $post->post_name; ?> <?php echo $post_categories . ' ' . $post_tags; ?> ">

                    <h3> <?php echo __(get_the_title(), 'wpt'); ?></h3>
                    <div class="post_body">
                        <div class="post_meta">
                            <span class="post_author"><?php _e('Author:', 'wpt'); ?> <?php echo get_the_author(); ?></span>
                            <span class="post_date"><?php _e('Date:', 'wpt'); ?> <?php echo get_the_date(); ?></span>
                            <span class="post_categories"><?php _e('Categories:', 'wpt'); ?><?php echo get_the_category_list(', '); ?></span>
                            <span class="post_tags"><?php _e('Tags:', 'wpt'); ?> <?php echo get_the_tag_list('', ', '); ?></span>
                        </div>
                        <div class="post_content"><?php _e(get_the_content(), 'wpt'); ?> </div>
                    </div>

                </div>
    <?php
            }
            $posts = ob_get_clean();
            wp_reset_postdata();
            $status = 200;
        } else {
            throw new Exception(_e('<div class="error empty no-posts">No posts found.</div>', 'wpt'));
            $status = 500;
        }
    } catch (Exception $e) {
        $result =  _e($e->getMessage(), 'wpt');
        $status = 500;
        $message = __('<div class="error generated">Error: ' . $e->getMessage() . '</div>', 'wpt');
    }

    $response = json_encode(array(
        'result'  =>  $posts,
        'status'  => $status,
        'message' => $message,
        'max_pages' => $posts_query->max_num_pages,
    ));

    // Send the response and exit
    if (defined('DOING_AJAX') && DOING_AJAX) {
        echo $response;
        wp_die();
    } else {
        return $response;
    }
}

/**
 * Generates a shortcode to display all posts.
 *
 * @param array $atts The attributes passed to the shortcode.
 * @throws Exception If there is an error fetching the posts.
 * @return string The HTML representation of the posts.
 */
function wpt_all_posts_shortcode($atts)
{
    ob_start();

    // Get post data as a JSON string
    $post_data_json = wpt_get_all_posts($atts);

    // Decode JSON string to an array
    $post_data = json_decode($post_data_json, true);

    // Check if decoding was successful
    if ($post_data['status'] == 200) {
        // Loop through each post
        echo ($post_data['result']);

        // Display pagination based on user preference
        if ($post_data['max_pages'] > 1) {
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total' => $post_data['max_pages'],
            ));
            echo '</div>';
        }
    } else {
        // decoding failed or posts are not present
        _e('<div class="error">Error: ' . $post_data['message'] . '.</div>', 'wpt');
    }

    return ob_get_clean();
}
