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

/**
 * Adds WooCommerce support to the theme.
 *
 * @throws Some_Exception_Class description of exception
 * @return void
 */
function wpt_woocommerce_support()
{
    function add_woo_support()
    {
        add_theme_support('woocommerce');
    }
    add_action('after_setup_theme', 'add_woo_support');
}

/**
 * Generates the CSS classes to be added to the body element.
 *
 * @param array $classes The array of existing CSS classes for the body element.
 * @return array The modified array of CSS classes with the added post slug.
 */
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

/**
 * Enable error reporting and display in PHP.
 *
 * @throws Exception If there is an error enabling error reporting.
 */
function E_ON()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


/**
 * Dump and die function for debugging purposes.
 *
 * @param mixed $data The data to be dumped.
 * @param mixed|null $exit Optional. If provided, the script execution will be terminated after dumping the data.
 * @throws None
 * @return None
 */
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

// include_once 'functions/custom-pagination-beta.php';
// include_once 'functions/wp_ajax_wpt_get_all_posts-beta.php';

include_once 'functions/_common_functions.php';
include_once 'functions/get_post_by_id.php';
include_once 'functions/get_post_by_name.php';
include_once 'functions/get_all_posts.php';
include_once 'functions/get_posts_by_ids.php';
include_once 'functions/get_posts_by_meta.php';
include_once 'functions/get_posts_by_categories.php';
include_once 'functions/get_posts_by_author.php';
include_once 'functions/get_postmeta_by_id.php';


/*
// to test
add_action('wp_footer', function () {


    //echo wpt_get_post_by_id(78, 'html');
    // echo wpt_get_post_by_name('lorem','html', 'work'); 

    // Example: Retrieve a custom post type named 'my_custom_post_type' as HTML
    // $content = wpt_get_post_by_name('shrieker', 'html', 'test');
    // echo $content;
    ?><div id="test-321"></div>
<script>
(function($) {
    $(document).ready(function() {

        // Trigger the AJAX request on document click
        // $(document).on('click', function() {
        // wpt_get_post_by_id(78,  '#test', '<?php //echo WPT_AJAX;?>');
        // wpt_get_post_by_name('home', 'page', '#test', '<?php //echo WPT_AJAX;?>');
        // wpt_get_posts(per_page, post_type, size, target,wpt_ajax_url);
        wpt_get_posts('-1', 'post', '0', '#test-321', '<?php echo WPT_AJAX;?>');

        // });

    });
})(jQuery);
</script>
<?php
});
*/










/**
 * A description of the entire PHP function.
 *
 * @param array $params The array of parameters.
 * @throws Some_Exception_Class description of exception
 * @return void
 */

/**
 * Updates the user meta data with the provided arguments.
 *
 * @param array $args An array containing the following keys:
 *   - id (int) The user ID.
 *   - meta_key (string) The meta key.
 *   - meta_value (mixed) The meta value to set.
 * @return string JSON-encoded string containing the following keys:
 *   - result (string) The result of the operation (success or failed).
 *   - status (int) The HTTP status code.
 *   - message (string) A message describing the result of the operation.
 */
function wpt_update_usermeta($args)
{
    $id = $args['id'];
    $meta_key = $args['meta_key'];
    $meta_value = $args['meta_value'];

    // Check if the usermeta already exists
    $existing_meta = get_user_meta($id, $meta_key, true);

    // Convert both existing and new values to strings for comparison
    $existing_meta_str = is_array($existing_meta) ? json_encode($existing_meta) : (string) $existing_meta;
    $meta_value_str = is_array($meta_value) ? json_encode($meta_value) : (string) $meta_value;

    if ($existing_meta_str === $meta_value_str) {
        // Values are the same, consider it a success
        $result = 'success';
        $status = 200;
        $message = 'Usermeta is already up to date';
    } else {
        // Update or add usermeta
        $update_result = update_user_meta($id, $meta_key, $meta_value);

        if ($update_result) {
            $result = 'success';
            $status = 200;
            $message = 'Usermeta updated successfully';
        } else {
            // Try to add usermeta if updating fails
            $add_result = add_user_meta($id, $meta_key, $meta_value, true);

            if ($add_result) {
                $result = 'success';
                $status = 201;
                $message = 'Usermeta added successfully';
            } else {
                $result = 'failed';
                $status = 500;
                $message = 'Error updating or adding usermeta';
            }
        }
    }

    return json_encode(array(
        'result'  => $result,
        'status'  => $status,
        'message' => $message
    ));
}



/**
 * Retrieves or updates meta data for a specified object.
 *
 * @param array $args The arguments for retrieving or updating meta data.
 *                    - id: The ID of the object.
 *                    - meta_key: The meta key to retrieve or update.
 *                    - meta_value: The new meta value to update, if applicable.
 *                    - meta_type: The type of meta data (e.g. 'user', 'post', 'term').
 * @return string JSON-encoded response containing the result, status, and message.
 *                - result: The result of the operation ('success' or 'failed').
 *                - status: The HTTP status code.
 *                - message: The result message.
 */
function wpt_set_meta($args)
{
    $id = $args['id'];
    $meta_key = $args['meta_key'];
    $meta_value = $args['meta_value'];
    $meta_type = $args['meta_type']; // 'user', 'post', 'term', etc.

    // Check the meta type and get the existing meta data
    switch ($meta_type) {
        case 'user':
            $existing_meta = get_user_meta($id, $meta_key, true);
            break;
        case 'post':
            $existing_meta = get_post_meta($id, $meta_key, true);
            break;
        case 'term':
            $existing_meta = get_term_meta($id, $meta_key, true);
            break;
            // Add more cases for other meta types as needed
        default:
            $existing_meta = null;
            break;
    }

    // Convert both existing and new values to strings for comparison
    $existing_meta_str = is_array($existing_meta) ? json_encode($existing_meta) : (string) $existing_meta;
    $meta_value_str = is_array($meta_value) ? json_encode($meta_value) : (string) $meta_value;

    if ($existing_meta_str === $meta_value_str) {
        // Values are the same, consider it a success
        $result = 'success';
        $status = 200;
        $message = ucfirst($meta_type) . ' meta is already up to date';
    } else {
        // Update or add meta
        $update_result = update_metadata($meta_type, $id, $meta_key, $meta_value);

        if ($update_result) {
            $result = 'success';
            $status = 200;
            $message = ucfirst($meta_type) . ' meta updated successfully';
        } else {
            // Try to add meta if updating fails
            $add_result = add_metadata($meta_type, $id, $meta_key, $meta_value, true);

            if ($add_result) {
                $result = 'success';
                $status = 201;
                $message = ucfirst($meta_type) . ' meta added successfully';
            } else {
                $result = 'failed';
                $status = 500;
                $message = 'Error updating or adding ' . $meta_type . ' meta';
            }
        }
    }

    return json_encode(array(
        'result'  => $result,
        'status'  => $status,
        'message' => $message
    ));
}

/**
 * Creates a new post in WordPress with the given arguments.
 *
 * @param array $args An associative array of arguments for creating the post.
 *                    The required arguments are 'post_type', 'post_title', and 'post_content'.
 *                    The optional arguments are 'post_excerpt', 'post_categories', 'tags', 'post_author',
 *                    'post_date', 'post_status', 'postmeta', and 'featured_image'.
 * @throws WP_Error Throws a WP_Error if there is an error adding the post or setting the featured image.
 * @return array An associative array with the result, status, message, and post_id of the created post.
 */
function wpt_create_post($args)
{
    // Check if required arguments are present
    if (empty($args['post_type']) || empty($args['post_title']) || empty($args['post_content'])) {
        return array('result' => 'failed', 'status' => 400, 'message' => 'Missing required arguments');
    }

    // Set default values for optional arguments
    $defaults = array(
        'post_excerpt' => '',
        'post_categories' => '',
        'tags' => array(),
        'post_author' => get_current_user_id(),
        'post_date' => current_time('mysql'),
        'post_status' => 'publish',
        'postmeta' => array(),
        'featured_image' => '', // Can be an attachment ID or a URL
    );

    // Merge provided arguments with defaults
    $args = wp_parse_args($args, $defaults);

    // Handle categories
    $post_categories = array();

    if (!empty($args['post_categories'])) {
        // Convert comma-separated values to array
        $categories_input = array_map('trim', explode(',', $args['post_categories']));
        $categories_input = array_filter($categories_input, 'strlen'); // Remove empty values

        // Convert category names to IDs
        foreach ($categories_input as $category) {
            $category_id = get_cat_ID($category);

            if ($category_id !== 0) {
                $post_categories[] = $category_id;
            }
        }
    }

    // Create post data
    $post_data = array(
        'post_type' => $args['post_type'],
        'post_title' => $args['post_title'],
        'post_content' => $args['post_content'],
        'post_excerpt' => $args['post_excerpt'],
        'post_category' => $post_categories,
        'tags_input' => $args['tags'],
        'post_author' => $args['post_author'],
        'post_date' => $args['post_date'],
        'post_status' => $args['post_status'],
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data, true);

    // Check if post was added successfully
    if (is_wp_error($post_id)) {
        return array('result' => 'failed', 'status' => 500, 'message' => $post_id->get_error_message());
    }

    // Set postmeta
    foreach ($args['postmeta'] as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    // Set featured image if provided
    if ($args['featured_image']) {
        $attachment_id = _wpt_set_featured_image($post_id, $args['featured_image']);

        // Check if setting featured image was successful
        if (is_wp_error($attachment_id)) {
            // Remove the post if there was an error setting the featured image
            wp_delete_post($post_id, true);

            return array('result' => 'failed', 'status' => 500, 'message' => $attachment_id->get_error_message());
        }
    }

    // Return success
    return array('result' => 'success', 'status' => 201, 'message' => 'Post added successfully', 'post_id' => $post_id);
}




// Helper function to set featured image by URL or attachment ID
function _wpt_set_featured_image($post_id, $image)
{
    // If $image is a URL, try to download and set it as the featured image
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        $file_array = array(
            'name' => basename($image),
            'tmp_name' => download_url($image),
        );

        $attachment_id = media_handle_sideload($file_array, $post_id, '', array('test_form' => false));

        // Check for errors
        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        // Set the post thumbnail
        set_post_thumbnail($post_id, $attachment_id);

        return $attachment_id;
    }

    // If $image is an attachment ID, set it as the featured image
    if (is_numeric($image)) {
        set_post_thumbnail($post_id, $image);
        return $image;
    }

    return new WP_Error('invalid_image', 'Invalid featured image provided');
}









/**
 * Retrieves user data by ID.
 *
 * @param int $id The ID of the user.
 * @param string $return_type (optional) The return type. Defaults to 'object'. Possible values are 'object', 'array', and 'json'.
 * @return array The user data along with the status and message.
 *     - result (mixed) The user data.
 *     - status (string) The status of the operation ('success' or 'error').
 *     - message (string) The message associated with the status.
 */
function wpt_get_user_by_id($id, $return_type = 'object')
{
    if (empty($id) || empty($return_type)) {
        die("<div class='error'>Please provide a user ID and return type.</div>");
    }
    $user = get_user_by('ID', $id);
    if ($user) {
        $user_meta = get_user_meta($id);
        $user_data = (object) array_merge((array) $user->data, $user_meta);
        if ($return_type === 'array') {
            $user_data = (array) $user_data;
        } elseif ($return_type === 'json') {
            $user_data = json_encode($user_data);
        }
        return [
            'result' => $user_data,
            'status' => 'success',
            'message' => 'User data retrieved successfully'
        ];
    }
    return [
        'result' => 'User not found',
        'status' => 'error',
        'message' => 'User not found'
    ];
}

  



/**
 * Retrieves users based on meta key and/or meta value.
 *
 * @param array $params The parameters for the function.
 *                     - meta_key: (string) The meta key to search for.
 *                     - meta_value: (string) The meta value to search for.
 *                     - return_type: (string) The type of data to return. Default is "object".
 * @return array The result of the function.
 *               - result: (mixed) The retrieved user data.
 *               - status: (string) The status of the result. Possible values are "success" or "error".
 *               - message: (string) A message providing information about the result.
 *               - total: (int) The total number of unique users retrieved.
 * @throws None
 */ 
function wpt_get_users_by_meta($params)
{
    $meta_key    = isset($params['meta_key']) ? $params['meta_key'] : null;
    $meta_value  = isset($params['meta_value']) ? $params['meta_value'] : null;
    $return_type = isset($params['return_type']) ? $params['return_type'] : 'object';

    if ((empty($meta_key) && empty($meta_value)) || empty($return_type)) {
        die("<div class='error'>Please provide either meta key or meta value, and return type.</div>");
    }

    // Define the query args based on the provided meta key and value
    $query_args = [];
    if (!empty($meta_key)) {
        $query_args['meta_key'] = $meta_key;
    }
    if (!empty($meta_value)) {
        $query_args['meta_value'] = $meta_value;
    }

    // Get users with the specified meta key and/or value
    $users = get_users($query_args);

    return _wpt_user_data_with_metadata($users, $return_type);
}


  


/**
 * Retrieves the user ID associated with a given username.
 *
 * @param string $username The username to search for.
 * @return int The user ID if found, or 0 if the user is not found.
 */
function wpt_get_user_id_by_username($username) {
    $user = get_user_by('login', $username);

    if ($user) {
        return $user->ID;
    } else {
        return 0; // User not found
    }
}



/**
 * Retrieves the username associated with the given user ID.
 *
 * @param int $id The ID of the user.
 * @return string The username of the user if found, or 'User not found!' if the user is not found.
 */
function wpt_get_username_by_id($id) {
    $user = get_user_by('id', $id);

    if ($user) {
        return $user->user_login;
    } else {
        return 'User not found!'; // User not found
    }
}

/**
 * Retrieves the user ID associated with a given email address.
 *
 * @param string $email The email address to search for.
 * @throws None
 * @return int|string The user ID if found, or a string indicating that the user was not found.
 */
function wpt_get_user_id_by_email($email) {
    $user = get_user_by('email', $email);

    if ($user) {
        return $user->ID;
    } else {
        return 'User not found!'; // User not found
    }
}

/**
 * Retrieves users by their role.
 *
 * @param string $role The role of the users to retrieve.
 * @return array Returns an array containing the user data.
 */
function wpt_get_users_by_role($role, $return_type)
{
    // Check if the role parameter is provided
    if (empty($role)) {
        return [
            'result'  => 'Role parameter is missing',
            'status'  => 'error',
            'message' => 'Role parameter is required',
            'total'   => 0
        ];
    }

    // Set up query arguments to get users by role
    $query_args = array(
        'role' => $role,
    );

    // Get users with the specified role
    $users = get_users($query_args);

    // Return the result of _wpt_user_data_with_metadata
    return _wpt_user_data_with_metadata($users, $return_type);
}


/**
 * Retrieves the URL of the post thumbnail for a given post ID.
 *
 * @param int $id The ID of the post.
 * @return string|int The URL of the post thumbnail if found, 0 otherwise.
 */
function wpt_get_post_thumbnail_by_post_id($id){
    $thumbnail_url = get_the_post_thumbnail_url($id);
    if($thumbnail_url){
        return $thumbnail_url;
    }else{
        return 0;
    }
}



/**
 * Retrieves the URL of the post thumbnail for a given attachment ID.
 *
 * @param int $id The ID of the attachment.
 * @throws None
 * @return string|false The URL of the post thumbnail, or false if it doesn't exist.
 */
function wpt_get_post_thumbnail_by_attachment_id($id){
    $thumbnail_url = wp_get_attachment_url($id);
    if($thumbnail_url){
        return $thumbnail_url;
    }else{
        return 0;
    }
}

/**
 * Retrieves the parent post ID of a given post ID.
 *
 * @param int $id The ID of the post.
 * @return int The ID of the parent post, or 0 if there is no parent post.
 */
function wpt_get_post_parent_by_id($id){
    $parent_id = wp_get_post_parent_id($id);
    if($parent_id){
        return $parent_id;
    }else{
        return 0;
    }
}


