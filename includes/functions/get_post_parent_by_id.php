<?php 

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