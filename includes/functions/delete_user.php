<?php 

 /**
     * Deletes a user from the WordPress database.
     *
     * @param mixed $user_identifier The user identifier (either the user ID or the login name)
     * @return int Returns 1 on successful deletion
     */
    function wpt_delete_user($user_identifier)
    {
        require_once ABSPATH . 'wp-admin/includes/user.php';
        require_once ABSPATH . 'wp-includes/pluggable.php';

        if (!is_numeric($user_identifier)) {
            $user = get_user_by('login', $user_identifier);
            $id = $user->ID;
        } else {
            $id = $user_identifier;
        }

        if (!$id) {
            echo 'Handle error: User not found';
            return false;
        }

        $deleted = wp_delete_user($id);

        if (is_wp_error($deleted)) {
            echo 'Handle error: User deletion failed';
            return false;
        }

        // User deleted successfully
        return 1;
    }

