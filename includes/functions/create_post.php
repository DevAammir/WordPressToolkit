<?php 

/**
 * Creates a new post in WordPress.
 *
 * @param array $params An associative array containing the parameters for the new post.
 *                     - title: The title of the post. Required.
 *                     - content: The content of the post. Required.
 *                     - post_status: The status of the post. Default is 'publish'.
 *                     - post_type: The type of the post. Default is 'post'.
 *                     - post_meta: An associative array of post meta data. Optional.
 * @throws None
 * @return int Returns the ID of the newly created post on success, or 0 on failure.
 */
function wpt_create_post($params)
{
    if($params == 'help'){wpt_create_post_help();die();}
    // Check if required parameters are provided
    if (empty($params['title']) || empty($params['content'])) {
        return 'Error: Title and content are required.';
    }

    // Set default post status if not provided
    $post_status = isset($params['post_status']) ? $params['post_status'] : 'publish';

    // Prepare post data
    $post_data = array(
        'post_title'   => $params['title'],
        'post_content' => $params['content'],
        'post_status'  => $post_status,
        'post_type'    => isset($params['post_type']) ? $params['post_type'] : 'post',
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    // Check for errors during post creation
    if (is_wp_error($post_id)) {
        // return 'Error: ' . $post_id->get_error_message();
        return 0;
    }

    // Check if post meta is provided
    if (isset($params['post_meta']) && is_array($params['post_meta'])) {
        foreach ($params['post_meta'] as $meta_key => $meta_value) {
            // Add post meta for each key-value pair
            add_post_meta($post_id, $meta_key, $meta_value, true);
        }
    }

    // Success message
    // return 'Post created successfully with ID ' . $post_id;
    return  $post_id;
}





function wpt_create_post_help()
{
?>
  <h3>wpt_create_post() help</h3>
  <code>
    $params = [
      'title'       => '',
      'content'     => '',
      'post_status' => 'publish',
      'post_type'   => 'post',
      'post_meta'   => ['meta_key' => 'key', 'meta_value' => 'value'],
    ];<br/><br/>
    wpt_create_post($params);<br/>
  </code><br/><br/>
  <p>Creates a new post in WordPress.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'title'</code> - The title of the post. Required.</li>
    <li><code>'content'</code> - The content of the post. Required.</li>
    <li><code>'post_status'</code> - The status of the post. Default is 'publish'.</li>
    <li><code>'post_type'</code> - The type of the post. Default is 'post'.</li>
    <li><code>'post_meta'</code> - An associative array of post meta data. Optional.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>Returns the ID of the newly created post on success, or 0 on failure.</p>

  <p><strong>Throws:</strong></p>
  <p>None</p>

  <p><strong>Additional:</strong></p>
  <p>Usage:</p>
  <code><pre>
  $post_data = array(
    'post_author'           => 1,                  // ID of the post author (default is the current user) but in this case we have that seperatly
    'post_content'          => 'Post content goes here', // Content of the post
    'post_title'            => 'Post Title',       // Title of the post
    'post_excerpt'          => 'Post excerpt goes here', // Post excerpt
    'post_status'           => 'publish',          // Post status (publish, draft, pending, private, etc.)
    'post_type'             => 'post',             // Post type (post, page, custom post types)
    'post_date'             => '2023-01-01 12:00:00', // Date and time of the post
    'post_date_gmt'         => '2023-01-01 12:00:00', // GMT date and time of the post
    'comment_status'        => 'open',             // Comment status (open, closed)
    'ping_status'           => 'open',             // Pingback/trackback status (open, closed)
    'post_password'         => 'post_password',   // Password for the post
    'post_name'             => 'post-slug',        // Post slug
    'to_ping'               => 'to_ping',         // URLs to be pinged
    'pinged'                => 'pinged',          // URLs already pinged
    'post_content_filtered' => 'filtered_content', // Filtered content of the post
    'post_parent'           => 0,                  // ID of the parent post (0 if none)
    'menu_order'            => 0,                  // Order of the post in menus
    'guid'                  => 'http://example.com/sample-post/', // Global Unique Identifier for the post
    'import_id'             => 0,                  // ID of post if importing
    'context'               => 'normal',           // Where to show the editor box (normal, advanced, side)
    'post_category'         => array(1, 2),        // Array of category IDs for the post
    'tags_input'            => 'tag1, tag2',       // Tags for the post, comma-separated
    'tax_input'             => array(
        'taxonomy_name' => array('term1', 'term2'), // Array of terms IDs for taxonomies
    ),
    'meta_input'            => array(
        'key1' => 'value1',                        // Custom fields as key-value pairs
        'key2' => 'value2',
    ),
);


    </pre>
  </code>
<?php
}
