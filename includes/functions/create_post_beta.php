<?php 



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
