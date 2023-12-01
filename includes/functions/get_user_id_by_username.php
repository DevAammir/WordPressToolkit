<?php 

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