<?php 

/**
 * Update a post in WordPress.
 *
 * @param int $post_id The ID of the post to be updated.
 * @param array|null $post_data Optional. An array of post data to be updated. Default is null.
 * @return int|bool 1 on success, or false on failure.
 */
function wpt_update_post($post_id, $post_data=null)
{
    if($post_id == 'help'){wpt_update_post_help();die();}
    // Check if the post ID is valid.
    if (!$post_id || !is_numeric($post_id)) {
        echo 'Invalid ID';
        return false;
    }

    // Check if the post exists.
    if (!get_post($post_id)) {
        echo  'non-existent post';
        return false;
    }

    // Update the post.
    $updated = wp_update_post(array_merge(['ID' => $post_id], $post_data), true);

    // Check if the update was successful.
    if (is_wp_error($updated)) {
        echo ' update error.';
        return false;
    }

   
    return 1;
}



function wpt_update_post_help()
{
?>
  <h3>wpt_update_post() help</h3>
  <code>
    $post_id = 1;<br/>
    $post_data = [
      'post_title'   => 'New Title',
      'post_content' => 'New Content',
      'post_status'  => 'publish'
    ];<br/><br/>
    wpt_update_post($post_id, $post_data);<br/>
  </code><br/><br/>
  <p>Update a post in WordPress.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>$post_id</code> (int) - The ID of the post to be updated.</li>
    <li><code>$post_data</code> (array|null) - Optional. An array of post data to be updated. Default is null.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>1 on success, or false on failure.</p>

  <p><strong>Additional:</strong></p>
  <p>Usage:</p>
  <code><pre>
  $post_data = array(
    'ID'                    => 1,                  // ID of the post to update (required) but in this case we have that seperatly 
    'post_author'           => 1,                  // ID of the post author
    'post_content'          => 'Updated content goes here', // Content of the post
    'post_title'            => 'Updated Post Title', // Title of the post
    'post_excerpt'          => 'Updated post excerpt goes here', // Post excerpt
    'post_status'           => 'publish',          // Post status (publish, draft, pending, private, etc.)
    'post_type'             => 'post',             // Post type (post, page, custom post types)
    'post_date'             => '2023-01-01 12:00:00', // Date and time of the post
    'post_date_gmt'         => '2023-01-01 12:00:00', // GMT date and time of the post
    'comment_status'        => 'open',             // Comment status (open, closed)
    'ping_status'           => 'open',             // Pingback/trackback status (open, closed)
    'post_password'         => 'updated_post_password', // Password for the post
    'post_name'             => 'updated-post-slug', // Post slug
    'to_ping'               => 'updated_to_ping',  // URLs to be pinged
    'pinged'                => 'updated_pinged',   // URLs already pinged
    'post_content_filtered' => 'updated_filtered_content', // Filtered content of the post
    'post_parent'           => 0,                  // ID of the parent post (0 if none)
    'menu_order'            => 0,                  // Order of the post in menus
    'guid'                  => 'http://example.com/updated-sample-post/', // Global Unique Identifier for the post
    'import_id'             => 0,                  // ID of post if importing
    'context'               => 'normal',           // Where to show the editor box (normal, advanced, side)
    'post_category'         => array(1, 2),        // Array of category IDs for the post
    'tags_input'            => 'updated_tag1, updated_tag2', // Tags for the post, comma-separated
    'tax_input'             => array(
        'taxonomy_name' => array('updated_term1', 'updated_term2'), // Array of terms IDs for taxonomies
    ),
    'meta_input'            => array(
        'updated_key1' => 'updated_value1',       // Custom fields as key-value pairs
        'updated_key2' => 'updated_value2',
    ),
);
    </pre>
  </code>
<?php
}
