<?php

/****
 * EXECUTE SETTING
 * **/
function wpt_execute_setting($setting, $callback)
{
    if (!empty(WPT_SETTINGS[$setting])) {
        $callback();
    }
}
/* * *
 * DISABLE GUTENBURG EVERYWHERE
 * * */
function wpt_disable_gutenburg_everywhere()
{
    // Disable Gutenberg editor
    add_filter('use_block_editor_for_post', '__return_false', 10);

    // Disable Gutenberg widgets
    add_filter('use_widgets_block_editor', '__return_false');

    // Disable Gutenberg on post type
    add_filter('gutenberg_can_edit_post_type', '__return_false');

    // Disable Gutenberg for post
    add_filter('gutenberg_can_edit_post', '__return_false');
}

/* * *
 * WOOCOMMERECE SUPPORT 
 * */
function wpt_woocommerce_support()
{
    function add_woo_support()
    {
        add_theme_support('woocommerce');
    }
    add_action('after_setup_theme', 'add_woo_support');
}

/* * *
 * ADD CSS CLASSES TO BODY 
 * */
function wpt_add_css_classes_to_body()
{
    function wpt_body_classes($classes)
    {
        global $post;
        $post_slug = $post->post_name;
        $classes[] = $post_slug;

        return $classes;
    }
    add_filter('body_class', 'wpt_body_classes');
}

/* * *
 * ERROR REPORTING ON 
 * */
function E_ON()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


/* * *
 *  DUMP DATA
 * * */
function dd($data, $exit = null)
{
?>
    <div class="row my-4" style="z-index:99999;">
        <pre>TESTING MODE</pre>
        <pre>
        <?php print_r($data); ?> 
    </pre>
    </div>
    <?php
    if (isset($exit)) {
        exit;
    }
}

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

        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => -1,
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

                    <h3> <?php echo esc_html__(get_the_title(), 'wpt'); ?></h3>
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
        $message = esc_html__('<div class="error generated">Error: ' . $e->getMessage() . '</div>', 'wpt');
    }

    $response = json_encode(array(
        'result'  =>  $posts,
        'status'  => $status,
        'message' => $message,
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

    // dd($post_data);
    // Check if decoding was successful
    if ($post_data['status'] == 200) {
        // Loop through each post
        echo ($post_data['result']);
    } else {
        // decoding failed or posts are not present
        _e('<div class="error">Error: ' . $post_data['message'] . '.</div>', 'wpt');
    }

    return ob_get_clean();
}

///////


// Enqueue scripts and styles
function enqueue_custom_scripts()
{
    // Enqueue custom script
    wp_enqueue_script('JS-functions', WPT_URL . 'js/functions.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
