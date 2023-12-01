<?php


/**
 * Removes the featured image of a post.
 *
 * @param mixed $identifier The numeric post ID or string post name.
 * @throws None
 * @return int Returns 1 if the featured image is successfully removed, false otherwise.
 */
function wpt_delete_category($category_identifier)
{
    if (is_numeric($category_identifier)) {
        // Deleting by category ID
        $deleted = wp_delete_category($category_identifier);
    } else {
        // Deleting by category name
        $category = get_category_by_slug($category_identifier);

        if (!$category) {
            echo 'Handle error: Category not found';
            return false;
        }

        $deleted = wp_delete_category($category->term_id);
    }

    if (is_wp_error($deleted)) {
        echo 'Handle error: Category deletion failed';
        return false;
    }

    // Category deleted successfully
    return 1;
}

