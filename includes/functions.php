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


///////


// Enqueue scripts and styles
function enqueue_custom_scripts()
{
    // Enqueue custom script
    wp_enqueue_script('JS-functions', WPT_URL . 'js/functions.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');




////////////////////////////////////////////////

// Custom pagination function in functions.php
function custom_pagination()
{
    $paged = $_POST['page'];
    $per_page = $_POST['per_page'];
    $post_type = $_POST['post_type'];

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $per_page,
        'paged' => $paged
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            // Output your post data here
            echo '<h2>' . get_the_title() . '</h2>';
            echo '<div>' . get_the_content() . '</div>';
        // Add more fields as needed
        endwhile;

        echo '<div id="load-more-btn-container"><button id="load-more-btn">Load More</button></div>';
    endif;

    wp_die();
}

add_action('wp_ajax_custom_pagination', 'custom_pagination');
add_action('wp_ajax_nopriv_custom_pagination', 'custom_pagination');

// Inline script in wp_footer
add_action('wp_footer', function () {
    ?>
    <script>
        jQuery(function($) {
            var page = 1;
            var loading = false;
            var perPage = 2; // Set your default value
            var postType = 'post'; // Set your default value

            function load_posts(paged) {
                if (loading) return;
                loading = true;

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: {
                        action: 'custom_pagination',
                        page: paged,
                        per_page: perPage,
                        post_type: postType,
                    },
                    beforeSend: function() {
                        // $('#load-more-btn').hide();
                        $('#ajax-posts').append('<div class="loader">Please wait...</div>');
                    },
                    success: function(response) {
                        $('.loader').hide();
                        $('#ajax-posts').append(response);
                        loading = false;
                    }
                });
            }

            function load_more_posts() {
                page++;
                load_posts(page);
            }

            // Load posts initially
            load_posts(page);

            // Load more button click event
            $(document).on('click', '#load-more-btn', function(e) {
                e.preventDefault();
                $(this).hide();
                load_more_posts();
            });
        });
    </script>
<?php
});

include_once 'functions/get_post_by_id.php';
// include_once 'functions/get_post_by_name.php';

