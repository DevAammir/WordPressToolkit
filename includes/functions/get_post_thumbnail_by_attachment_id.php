<?php 



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
