<?php 


/**
 * Retrieves the user ID associated with a given email address.
 *
 * @param string $email The email address to search for.
 * @throws None
 * @return int|string The user ID if found, or a string indicating that the user was not found.
 */
function wpt_get_user_id_by_email($email)
{
    $user = get_user_by('email', $email);

    if ($user) {
        return $user->ID;
    } else {
        return 'User not found!'; // User not found
    }
}
