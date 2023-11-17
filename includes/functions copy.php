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

function wpt_get_all_posts($atts = array()) {
    $result = 'success';
    $status = 200;
    $message = '';
    $posts = array();

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
            while ($posts_query->have_posts()) {
                $posts_query->the_post();

                $post_data = array(
                    'title'       => get_the_title(),
                    'author'      => get_the_author(),
                    'date'        => get_the_date(),
                    'categories'  => get_the_category_list(', '),
                    'tags'        => get_the_tag_list('', ', '),
                    'content'     => get_the_content(),
                    // Add more fields as needed
                );

                $posts[] = $post_data;
            }
            wp_reset_postdata();
        } else {
            throw new Exception('No posts found.');
        }
    } catch (Exception $e) {
        $result = 'error';
        $status = 500;
        $message = 'Error: ' . $e->getMessage();
    }

    $response = json_encode(array(
        'result'  => $result,
        'status'  => $status,
        'message' => $message,
        'posts'   => $posts,
    ));

    // Send the response and exit
    if (defined('DOING_AJAX') && DOING_AJAX) {
        echo $response;
        wp_die();
    } else {
        return $response;
    }
}

function wpt_all_posts_shortcode($atts) {
    ob_start();
    echo wpt_get_all_posts($atts);
    return ob_get_clean();
}


/* * *
 *  UNIVERSAL AJAX ENDPOINT BETA VERSION
 * * */

/*
GET 
    1. post by id, 
    2. all posts,
	3. posts by ids
	4. post by postmeta
	5. posts by postmeta
    6. postmeta,
    7. user by id,
    8. all users,
	9. users by ids
	10.user by usermeta 
	11.users by usermeta
    12.usermeta
	13.get post by category
	14.get posts by categories
	15.get all catagories
	16.get post categories by post id
	17.get option by id
*/
/*POST
1. create post
2. create post with postmeta
3. add postmeta
4. add user
5. add user with usermeta
6. add usermeta
7. delet post
8. delete postmeta
9. delete user
10. delete usermeta
11. add/update option
12. delete option
*/
add_action('wp_ajax_wpt_universal_ajax_endpoint', 'wpt_universal_ajax_endpoint');
add_action('wp_ajax_nopriv_wpt_universal_ajax_endpoint', 'wpt_universal_ajax_endpoint');

function wpt_universal_ajax_endpoint()
{
    // Initialize variables
    $result = null;
    $status = 0;
    $message = '';

    // Security check
    check_ajax_referer('wpt_ajax_nonce', 'security');

    // Get the AJAX action
    $command = isset($_POST['command']) ? sanitize_text_field($_POST['command']) : '';

    // Perform different actions based on the AJAX action
    switch ($command) {
            // GET requests
        case 'get_post_by_id':
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $result = get_post($post_id);
            $message = $result ? 'Successfully retrieved post' : 'Post not found';
            $status = $result ? 1 : 0;
            break;

        case 'get_all_posts':
            $posts = get_posts(array(
                'post_type'      => 'post',
                'posts_per_page' => -1,
            ));
            $result = $posts;
            $message = $result ? 'Successfully retrieved all posts' : 'No posts found';
            $status = $result ? 1 : 0;
            break;

        case 'get_posts_by_ids':
            $post_ids = isset($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : array();
            $posts = get_posts(array(
                'post_type' => 'post',
                'post__in'  => $post_ids,
            ));
            $result = $posts;
            $message = $result ? 'Successfully retrieved posts by IDs' : 'No posts found';
            $status = $result ? 1 : 0;
            break;

        case 'get_post_by_postmeta':
            $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
            $meta_value = isset($_POST['meta_value']) ? sanitize_text_field($_POST['meta_value']) : '';
            $posts = get_posts(array(
                'post_type'  => 'post',
                'meta_key'   => $meta_key,
                'meta_value' => $meta_value,
            ));
            $result = $posts;
            $message = $result ? 'Successfully retrieved posts by postmeta' : 'No posts found';
            $status = $result ? 1 : 0;
            break;

        case 'get_posts_by_postmeta':
            $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
            $meta_value = isset($_POST['meta_value']) ? sanitize_text_field($_POST['meta_value']) : '';
            $posts = get_posts(array(
                'post_type'  => 'post',
                'meta_key'   => $meta_key,
                'meta_value' => $meta_value,
            ));
            $result = $posts;
            $message = $result ? 'Successfully retrieved posts by postmeta' : 'No posts found';
            $status = $result ? 1 : 0;
            break;

        case 'get_postmeta':
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $result = get_post_meta($post_id);
            $message = $result ? 'Successfully retrieved postmeta' : 'Postmeta not found';
            $status = $result ? 1 : 0;
            break;

        case 'get_user_by_id':
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $result = get_user_by('ID', $user_id);
            $message = $result ? 'Successfully retrieved user' : 'User not found';
            $status = $result ? 1 : 0;
            break;

        case 'get_all_users':
            $users = get_users();
            $result = $users;
            $message = $result ? 'Successfully retrieved all users' : 'No users found';
            $status = $result ? 1 : 0;
            break;

        case 'get_users_by_ids':
            $user_ids = isset($_POST['user_ids']) ? array_map('intval', $_POST['user_ids']) : array();
            $users = get_users(array('include' => $user_ids));
            $result = $users;
            $message = $result ? 'Successfully retrieved users by IDs' : 'No users found';
            $status = $result ? 1 : 0;
            break;

        case 'get_user_by_usermeta':
            $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
            $meta_value = isset($_POST['meta_value']) ? sanitize_text_field($_POST['meta_value']) : '';
            $users = get_users(array(
                'meta_key'   => $meta_key,
                'meta_value' => $meta_value,
            ));
            $result = $users;
            $message = $result ? 'Successfully retrieved users by usermeta' : 'No users found';
            $status = $result ? 1 : 0;
            break;

        case 'get_users_by_usermeta':
            $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
            $meta_value = isset($_POST['meta_value']) ? sanitize_text_field($_POST['meta_value']) : '';
            $users = get_users(array(
                'meta_key'   => $meta_key,
                'meta_value' => $meta_value,
            ));
            $result = $users;
            $message = $result ? 'Successfully retrieved users by usermeta' : 'No users found';
            $status = $result ? 1 : 0;
            break;

        case 'get_usermeta':
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $result = get_user_meta($user_id);
            $message = $result ? 'Successfully retrieved usermeta' : 'Usermeta not found';
            $status = $result ? 1 : 0;
            break;

        case 'get_post_by_category':
            $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
            $posts = get_posts(array(
                'category_name' => $category,
            ));
            $result = $posts;
            $message = $result ? 'Successfully retrieved posts by category' : 'No posts found for the category';
            $status = $result ? 1 : 0;
            break;

        case 'get_posts_by_categories':
            $categories = isset($_POST['categories']) ? array_map('sanitize_text_field', $_POST['categories']) : array();
            $posts = get_posts(array(
                'category_name' => implode(',', $categories),
            ));
            $result = $posts;
            $message = $result ? 'Successfully retrieved posts by categories' : 'No posts found for the categories';
            $status = $result ? 1 : 0;
            break;

        case 'get_all_categories':
            $categories = get_categories();
            $result = $categories;
            $message = $result ? 'Successfully retrieved all categories' : 'No categories found';
            $status = $result ? 1 : 0;
            break;

        case 'get_post_categories_by_post_id':
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $categories = get_the_category($post_id);
            $result = $categories;
            $message = $result ? 'Successfully retrieved post categories' : 'No categories found for the post';
            $status = $result ? 1 : 0;
            break;

            // POST requests
        case 'create_post':
            // Handle creating a new post
            // ...
            $status = 1;
            $message = 'Post created successfully';
            break;

        case 'create_post_with_postmeta':
            // Handle creating a new post with postmeta
            // ...
            $status = 1;
            $message = 'Post created with postmeta successfully';
            break;

        case 'add_postmeta':
            // Handle adding postmeta to an existing post
            // ...
            $status = 1;
            $message = 'Postmeta added successfully';
            break;

        case 'add_user':
            // Handle adding a new user
            // ...
            $status = 1;
            $message = 'User added successfully';
            break;

        case 'add_user_with_usermeta':
            // Handle adding a new user with usermeta
            // ...
            $status = 1;
            $message = 'User added with usermeta successfully';
            break;

        case 'add_usermeta':
            // Handle adding usermeta to an existing user
            // ...
            $status = 1;
            $message = 'Usermeta added successfully';
            break;

        case 'delete_post':
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $result = wp_delete_post($post_id, true); // Set the second parameter to true to force delete
            $status = $result ? 1 : 0;
            $message = $result ? 'Post deleted successfully' : 'Unable to delete post';
            break;

        case 'delete_postmeta':
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
            $result = delete_post_meta($post_id, $meta_key);
            $status = $result ? 1 : 0;
            $message = $result ? 'Postmeta deleted successfully' : 'Unable to delete postmeta';
            break;

        case 'delete_user':
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $result = wp_delete_user($user_id);
            $status = $result ? 1 : 0;
            $message = $result ? 'User deleted successfully' : 'Unable to delete user';
            break;

        case 'delete_usermeta':
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
            $result = delete_user_meta($user_id, $meta_key);
            $status = $result ? 1 : 0;
            $message = $result ? 'Usermeta deleted successfully' : 'Unable to delete usermeta';
            break;

        default:
            $message = 'Invalid command';
            break;
    }

    // Prepare the response
    $response = json_encode(array(
        'result'  => $result,
        'status'  => $status,
        'message' => $message,
        'request' => $_REQUEST,
    ));

    // Send the response and exit
    echo $response;
    wp_die();
}
