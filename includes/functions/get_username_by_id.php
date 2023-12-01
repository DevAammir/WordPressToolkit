<?php 


/**
 * Retrieves the username associated with the given user ID.
 *
 * @param int $id The ID of the user.
 * @return string The username of the user if found, or 'User not found!' if the user is not found.
 */
function wpt_get_username_by_id($id)
{
    $user = get_user_by('id', $id);

    if ($user) {
        return $user->user_login;
    } else {
        return 'User not found!'; // User not found
    }
}