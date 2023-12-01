<?php 

/**
 * Creates a custom role in WordPress.
 *
 * @param string $role_name The name of the custom role.
 * @param string|null $display_name The display name of the custom role. Optional.
 * @param array|null $capabilities The capabilities of the custom role. Optional.
 * @throws WP_Error If an error occurs while creating the custom role.
 * @return mixed Returns the WP_Role object if the role is created successfully,
 *               otherwise returns an error message.
 */
function wpt_create_custom_role($role_name, $display_name = null, $capabilities = null)
{
    if($role_name == 'help'){wpt_create_custom_role_help();die();}
    if(empty($role_name) || empty($display_name) || empty($capabilities)){
        return 'Please provide all the required parameters: role_name, display_name, and capabilities';
    }
    // Check if the role already exists
    if (get_role($role_name)) {
        return 'Role already exists';
    }
    // Attempt to add the custom role
    $result = add_role($role_name, $display_name, $capabilities);

    // Check if an error occurred
    if (is_wp_error($result)) {
        // Handle the error

        error_log('Error creating custom role: ' . $result->get_error_message());

        // wp_mail('admin@example.com', 'Custom Role Creation Error', 'Error: ' . $result->get_error_message());

        // wp_die('Error creating custom role. Please try again or contact support.');

        // Return the WP_Error object to the calling code
        return $result->get_error_message();
    } else {

        // If the role is created successfully, return the WP_Role object
        return 1;
    }
}






function wpt_create_custom_role_help()
{
?>
  <h3>wpt_create_custom_role() help</h3>
  <code>
    $role_name = 'custom_role';<br/>
    $display_name = 'Custom Role'; // Optional<br/>
    $capabilities = [
      'read'         => true,
      'edit_posts'   => true,
      'upload_files' => true
    ]; // Optional<br/><br/>
    wpt_create_custom_role($role_name, $display_name, $capabilities);<br/>
  </code><br/><br/>
  <p>Creates a custom role in WordPress.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>$role_name</code> (string) - The name of the custom role.</li>
    <li><code>$display_name</code> (string|null) - The display name of the custom role. Optional.</li>
    <li><code>$capabilities</code> (array|null) - The capabilities of the custom role. Optional.</li>
  </ul>

  <p><strong>Throws:</strong></p>
  <p>WP_Error If an error occurs while creating the custom role.</p>

  <p><strong>Returns:</strong></p>
  <p>Returns the WP_Role object if the role is created successfully, otherwise returns an error message.</p>
<code>
<pre>
$capabilities = array(
    'read'                   => true,
    'edit_posts'             => true,
    'edit_others_posts'      => false,
    'publish_posts'          => false,
    'edit_pages'             => true,
    'edit_others_pages'      => false,
    'publish_pages'          => false,
    'edit_private_pages'     => true,
    'edit_published_pages'   => false,
    'upload_files'           => true,
    'delete_posts'           => false,
    'delete_pages'           => false,
    'manage_categories'      => false,
    'moderate_comments'      => false,
    'manage_options'         => false,
    'edit_theme_options'     => false,
     'read'                   => true,
    'edit_posts'             => true,
    'edit_others_posts'      => false,
    'publish_posts'          => false,
    'edit_pages'             => true,
    'edit_others_pages'      => false,
    'publish_pages'          => false,
    'edit_private_pages'     => true,
    'edit_published_pages'   => false,
    'upload_files'           => true,
    'delete_posts'           => false,
    'delete_pages'           => false,
    'delete_others_posts'    => false,
    'delete_private_posts'   => false,
    'delete_published_posts' => false,
    'delete_others_pages'    => false,
    'delete_private_pages'   => false,
    'delete_published_pages' => false,
    'manage_categories'      => false,
    'manage_links'           => false,
    'moderate_comments'      => false,
    'manage_comments'        => false,
    'edit_comment'           => false,
    'approve_comment'        => false,
    'unapprove_comment'      => false,
    'delete_comment'         => false,
    'edit_term'              => false,
    'delete_term'            => false,
    'assign_term'            => false,
    'upload_plugins'         => false,
    'install_plugins'        => false,
    'activate_plugins'       => false,
    'update_plugins'         => false,
    'delete_plugins'         => false,
    'edit_plugins'           => false,
    'list_users'             => false,
    'create_users'           => false,
    'delete_users'           => false,
    'edit_users'             => false,
    'promote_users'          => false,
    'edit_theme_options'     => false,
    'customize'              => false,
    'export'                 => false,
    'import'                 => false,
    // Add more capabilities as needed
);
</pre>
</code>

<?php
}
