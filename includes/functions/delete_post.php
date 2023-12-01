<?php 

/**
 * Deletes a post from the WordPress database.
 *
 * @param mixed $post_identifier The identifier of the post to be deleted. It can be either the post ID or the post slug.
 * @throws Exception If the post deletion fails.
 * @return int|false The number 1 if the post is deleted successfully, false otherwise.
 */
function wpt_delete_post($post_identifier)
{
    // require_once ABSPATH . 'wp-admin/includes/user.php';
    // require_once ABSPATH . 'wp-includes/pluggable.php';

    if (!is_numeric($post_identifier)) {
        $post = get_page_by_path($post_identifier, OBJECT, 'post');
        if ($post) {
            $id = $post->ID;
        } else {
            // Handle the case when the post is not found
            $id = null;
        }
    } else {
        $id = $post_identifier;
    }
    

    $deleted = wp_delete_post($id, true); // Set the second parameter to true to force delete

    if (!$deleted) {
        echo 'Handle error: Post deletion failed';
        return false;
    }

    // Post deleted successfully
    return 1;
}