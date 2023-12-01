<?php 


/**
 * Removes the featured image of a post.
 *
 * @param mixed $identifier The numeric post ID or string post name.
 * @throws None
 * @return int Returns 1 if the featured image is successfully removed, false otherwise.
 */
function wpt_delete_tag($tag_identifier)
{
    if (is_numeric($tag_identifier)) {
        // Deleting by tag ID
        $deleted = wp_delete_term($tag_identifier, 'post_tag');
    } else {
        // Deleting by tag name
        $tag = get_term_by('name', $tag_identifier, 'post_tag');
        
        if (!$tag) {
            echo 'Handle error: Tag not found';
            return false;
        }

        $deleted = wp_delete_term($tag->term_id, 'post_tag');
    }

    if (is_wp_error($deleted)) {
        echo 'Handle error: Tag deletion failed';
        return false;
    }

    // Tag deleted successfully
    return 1;
}


