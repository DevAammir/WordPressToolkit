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
function wpt_create_post_beta($args)
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
function wpt_get_user_id_by_username($username)
{
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
function wpt_get_username_by_id($id)
{
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
function wpt_get_user_id_by_email($email)
{
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
function wpt_get_post_thumbnail_by_post_id($id)
{
    $thumbnail_url = get_the_post_thumbnail_url($id);
    if ($thumbnail_url) {
        return $thumbnail_url;
    } else {
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
function wpt_get_post_thumbnail_by_attachment_id($id)
{
    $thumbnail_url = wp_get_attachment_url($id);
    if ($thumbnail_url) {
        return $thumbnail_url;
    } else {
        return 0;
    }
}

/**
 * Retrieves the parent post ID of a given post ID.
 *
 * @param int $id The ID of the post.
 * @return int The ID of the parent post, or 0 if there is no parent post.
 */
function wpt_get_post_parent_by_id($id)
{
    $parent_id = wp_get_post_parent_id($id);
    if ($parent_id) {
        return $parent_id;
    } else {
        return 0;
    }
}


/**
 * Retrieves posts by tags based on the provided parameters.
 *
 * @param array $params An array of parameters:
 *                     - post_type (string): The post type.
 *                     - size (string): The size.
 *                     - tags (array|string): The tags.
 *                     - return_type (string): The return type.
 * @throws None
 * @return mixed The generated response based on the return type.
 */
function wpt_get_posts_by_tags($params = array())
{
    if (
        empty($params) ||
        $params['post_type'] == '' ||
        $params['size'] == '' ||
        empty($params['tags']) ||  // Change from 'category' to 'tags'
        $params['tags'] == '' ||   // Change from 'category' to 'tags'
        empty($params['return_type']) ||
        !isset($params['return_type']) ||
        $params['return_type'] == ''
    ) {
        echo "<div class='error'>Please provide all the required parameters: post_type, size, tags, and return_type</div>";
        die();
    }

    $return_type = empty($params['return_type']) || !isset($params['return_type']) ? 'html' : $params['return_type'];

    $size = empty($params['size']) ? 0 : $params['size'];

    $args = array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => empty($params['per_page']) ? -1 : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'orderby'        => 'post__in', // This may need adjustment based on your requirements
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ? 0 : $params['size']
    );

    if (!empty($params['tags'])) {  // Change from 'category' to 'tags'
        $tags = $params['tags'];    // Change from 'category' to 'tags'

        // Check if it's a comma-separated list
        if (is_array($tags) || (is_string($tags) && strpos($tags, ',') !== false)) {
            $tags_array = array_map('trim', is_array($tags) ? $tags : explode(',', $tags));

            if (count($tags_array) > 1) {
                $args['tax_query'] = array(
                    'relation' => 'OR',
                );

                foreach ($tags_array as $single_tag) {
                    if (is_numeric($single_tag)) {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'post_tag',  // Change from 'category' to 'post_tag'
                            'field' => 'id',
                            'terms' => $single_tag,
                        );
                    } else {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'post_tag',  // Change from 'category' to 'post_tag'
                            'field' => 'slug',
                            'terms' => $single_tag,
                        );
                    }
                }
            } elseif (count($tags_array) === 1) {
                // Use 'tag__in' for a single tag ID
                $args['tag__in'] = array_map('intval', $tags_array);
            }
        } else {
            // Check if it's numeric (ID) or a string (name)
            if (is_numeric($tags)) {
                $args['tag_id'] = $tags;  // Change from 'category' to 'post_tag'
            } else {
                $args['tag'] = $tags;  // Change from 'category' to 'post_tag'
            }
        }
    }

    // Uncomment the following line for debugging purposes
    // dd($args, true);

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        $response = _wpt_handle_no_posts($params);
    } else {
        $status  = 200;
        $message = 'success';

        if ($return_type === 'html') {
            $response = _wpt_generate_html_response_for_posts($query, $params);
        } elseif ($return_type === 'json') {
            $response = _wpt_generate_json_response_for_posts($query,  $params, $status, $message);
        } elseif ($return_type === 'array') {
            $response = _wpt_generate_array_response_for_posts($query,  $params, $status, $message);
        }
    }

    wp_reset_postdata();
    return $response;
}



/**
 * Creates a new post in WordPress.
 *
 * @param array $params An associative array containing the parameters for the new post.
 *                     - title: The title of the post. Required.
 *                     - content: The content of the post. Required.
 *                     - post_status: The status of the post. Default is 'publish'.
 *                     - post_type: The type of the post. Default is 'post'.
 *                     - post_meta: An associative array of post meta data. Optional.
 * @throws None
 * @return int Returns the ID of the newly created post on success, or 0 on failure.
 */
function wpt_create_post($params)
{
    // Check if required parameters are provided
    if (empty($params['title']) || empty($params['content'])) {
        return 'Error: Title and content are required.';
    }

    // Set default post status if not provided
    $post_status = isset($params['post_status']) ? $params['post_status'] : 'publish';

    // Prepare post data
    $post_data = array(
        'post_title'   => $params['title'],
        'post_content' => $params['content'],
        'post_status'  => $post_status,
        'post_type'    => isset($params['post_type']) ? $params['post_type'] : 'post',
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    // Check for errors during post creation
    if (is_wp_error($post_id)) {
        // return 'Error: ' . $post_id->get_error_message();
        return 0;
    }

    // Check if post meta is provided
    if (isset($params['post_meta']) && is_array($params['post_meta'])) {
        foreach ($params['post_meta'] as $meta_key => $meta_value) {
            // Add post meta for each key-value pair
            add_post_meta($post_id, $meta_key, $meta_value, true);
        }
    }

    // Success message
    // return 'Post created successfully with ID ' . $post_id;
    return  $post_id;
}





















function wpt_create_user($params)
{
    $username = sanitize_text_field($params['username']);
    $email = sanitize_text_field($params['email']);
    $password = sanitize_text_field($params['password']);
    $default_role = !empty($params['role']) ? $params['role'] : 'subscriber'; // You can change this to the default role you prefer

    $data["username"] = $username;
    $data["email"] = $email;
    $data["password"] = $password;
    $errors = array();

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'EMPTY_FIELDS';
    }

    if (!empty($username)) {
        if (username_exists($username)) {
            $errors[] = 'USERNAME_ALREADY_EXIST';
        }
    }

    if (!empty($email)) {
        if (!is_email($email)) {
            $errors[] = 'NOT_A_VALID_EMAIL_ADDRESS';
        } else {
            if (email_exists($email)) {
                $errors[] = 'EMAIL_ADDRESS_ALREADY_EXIST';
            }
        }
    }

    if (!empty($password) && strlen($password) < 5) {
        $errors[] = 'PASSWORD_LENGTH_IS_TOO_SHORT';
    }

    if (count($errors)) {
        $data["errors"] = $errors;
        $data["result"] = 'FAIL';
    } else {
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        $user_id = wp_create_user($username, $random_password, $email);

        if (!is_wp_error($user_id)) {
            // Set the default role for the new user
            $user = new WP_User($user_id);
            $user->set_role($default_role);

            wp_set_password($password, $user_id);
            $data["result"] = $user_id;
        } else {
            $data["result"] = 'FAIL';
        }
    }

    return $data;
}



/**
 * Uploads a user image to the server.
 *
 * @param array $params An array containing the user identifier and the image data.
 *                     - user_identifier: The identifier of the user (either an ID or a username).
 *                     - image: The image data to be uploaded.
 * @return string|false The URL of the uploaded image on success, or false on failure.
 */
function _wpt_upload_user_image($params)
{
    $user_identifier = $params['user_identifier'];
    $image = $params['image'];

    // Use wp_upload_dir() to get the correct upload directory
    $upload_dir = wp_upload_dir();
    $upload_directory = $upload_dir['basedir'] . '/users/';

    if (is_numeric($user_identifier)) {
        $user = get_user_by('id', $user_identifier);
    } else {
        // If $user_identifier is not numeric, assume it's a username
        $user = get_user_by('login', $user_identifier);
    }

    // Ensure the directory exists or create it
    if (!file_exists($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    $image_extension = pathinfo($image['name'], PATHINFO_EXTENSION);

    $new_image_name = $user->user_login . '_' . uniqid() . '.' . $image_extension;

    // Set the full path for the new image
    $new_image_path = $upload_directory . $new_image_name;

    // Check if the file was successfully moved
    if (move_uploaded_file($image['tmp_name'], $new_image_path)) {
        $full_image_url = $upload_dir['baseurl'] . '/users/' . $new_image_name;
        update_user_meta($user->ID, 'wpt_profile_image', $full_image_url);
        return $full_image_url;
    } else {
        // Handle upload error
        return "Error uploading image.";
    }
}





add_shortcode('image_upload_test', 'image_upload_test_cb');
function image_upload_test_cb()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["user_name"]) && isset($_FILES["user_profile_image"])) {
            $user_name = $_POST["user_name"];
            $image = $_FILES['user_profile_image'];

            // Call the uploadImage function
            $result = _wpt_upload_user_image(['user_identifier' => 'joe', 'image' => $image]);

            echo $result;
        } else {
            echo "Please provide both user name and image.";
        }
    }
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="user_name">User Name:</label>
        <input type="text" name="user_name"><br>

        <label for="image">Choose Image:</label>
        <input type="file" name="user_profile_image" accept="image/*"><br>

        <input type="submit" value="Upload Image">
    </form>
<?php
}
