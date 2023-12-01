<?php

/**
 * Removes the featured image of a post.
 *
 * @param mixed $identifier The numeric post ID or string post name.
 * @throws None
 * @return int Returns 1 if the featured image is successfully removed, false otherwise.
 */
function wpt_remove_featured_image($identifier)
{
    // Check if $identifier is numeric (post ID) or a string (post name)
    if (is_numeric($identifier)) {
        $post_id = $identifier;
    } else {
        // Try to get post ID from post name
        $post = get_page_by_path($identifier, OBJECT, 'post');

        if (!$post) {
            echo 'Handle error: Invalid post identifier';
            return false;
        }

        $post_id = $post->ID;
    }

    $removed = delete_post_thumbnail($post_id);

    if (!$removed) {
        echo 'Handle error: Featured image removal failed';
        return false;
    }

    // Featured image removed successfully
    return 1;
}

