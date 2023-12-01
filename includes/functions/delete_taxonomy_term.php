<?php 


/**
 * Deletes a taxonomy term.
 *
 * @param mixed $term The term to delete. If it's numeric, assume it's a term ID. If it's not numeric, assume it's a term name.
 * @param string $taxonomy The taxonomy of the term.
 * @throws None
 * @return int|false Returns 1 if the term was deleted successfully. Returns false if there was an error deleting the term.
 */
function wpt_delete_taxonomy_term($term, $taxonomy)
{
    if (empty($taxonomy)) {
        echo 'Handle error: Invalid taxonomy';
        return false;
    }

    if (is_numeric($term)) {
        // If $term is numeric, assume it's a term ID
        $term_id = absint($term);
        $deleted = wp_delete_term($term_id, $taxonomy);
    } else {
        // If $term is not numeric, assume it's a term name
        $term_obj = get_term_by('name', $term, $taxonomy);

        if ($term_obj && !is_wp_error($term_obj)) {
            $deleted = wp_delete_term($term_obj->term_id, $taxonomy);
        } else {
            echo 'Handle error: Term not found';
            return false;
        }
    }

    if (is_wp_error($deleted)) {
        echo 'Handle error: Term deletion failed';
        return false;
    }

    // Term deleted successfully
    return 1;
}

