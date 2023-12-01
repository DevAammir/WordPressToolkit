<?php 



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
