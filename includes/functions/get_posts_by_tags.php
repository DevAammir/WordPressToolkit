<?php 

/**
 * Retrieves posts by tags based on the provided parameters.
 *
 * @param array $params An array of parameters:
 *                     - post_type (string): The post type.
 *                     - size (string): The size.
 *                     - tags (array|string): The tags.
 *                     - return_type (string): The return type.
 * @throws None
 * @return mixed The generated response based on the return type.
 */
function wpt_get_posts_by_tags($params = array())
{
    if($params == 'help'){wpt_get_posts_by_tags_help();die();}
    
    if (
        empty($params) ||
        $params['post_type'] == '' ||
        $params['size'] == '' ||
        empty($params['tags']) ||  // Change from 'category' to 'tags'
        $params['tags'] == '' ||   // Change from 'category' to 'tags'
        empty($params['return_type']) ||
        !isset($params['return_type']) ||
        $params['return_type'] == ''
    ) {
        echo "<div class='error'>Please provide all the required parameters: post_type, size, tags, and return_type</div>";
        die();
    }

    $return_type = empty($params['return_type']) || !isset($params['return_type']) ? 'html' : $params['return_type'];

    $size = empty($params['size']) ? 0 : $params['size'];

    $args = array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => empty($params['per_page']) ? -1 : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'orderby'        => 'post__in', // This may need adjustment based on your requirements
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ? 0 : $params['size']
    );

    if (!empty($params['tags'])) {  // Change from 'category' to 'tags'
        $tags = $params['tags'];    // Change from 'category' to 'tags'

        // Check if it's a comma-separated list
        if (is_array($tags) || (is_string($tags) && strpos($tags, ',') !== false)) {
            $tags_array = array_map('trim', is_array($tags) ? $tags : explode(',', $tags));

            if (count($tags_array) > 1) {
                $args['tax_query'] = array(
                    'relation' => 'OR',
                );

                foreach ($tags_array as $single_tag) {
                    if (is_numeric($single_tag)) {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'post_tag',  // Change from 'category' to 'post_tag'
                            'field' => 'id',
                            'terms' => $single_tag,
                        );
                    } else {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'post_tag',  // Change from 'category' to 'post_tag'
                            'field' => 'slug',
                            'terms' => $single_tag,
                        );
                    }
                }
            } elseif (count($tags_array) === 1) {
                // Use 'tag__in' for a single tag ID
                $args['tag__in'] = array_map('intval', $tags_array);
            }
        } else {
            // Check if it's numeric (ID) or a string (name)
            if (is_numeric($tags)) {
                $args['tag_id'] = $tags;  // Change from 'category' to 'post_tag'
            } else {
                $args['tag'] = $tags;  // Change from 'category' to 'post_tag'
            }
        }
    }

    // Uncomment the following line for debugging purposes
    // dd($args, true);

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        $response = _wpt_handle_no_posts($params);
    } else {
        $status  = 200;
        $message = 'success';

        if ($return_type === 'html') {
            $response = _wpt_generate_html_response_for_posts($query, $params);
        } elseif ($return_type === 'json') {
            $response = _wpt_generate_json_response_for_posts($query,  $params, $status, $message);
        } elseif ($return_type === 'array') {
            $response = _wpt_generate_array_response_for_posts($query,  $params, $status, $message);
        }
    }

    wp_reset_postdata();
    return $response;
}


function wpt_get_posts_by_tags_help()
{
?>
  <h3>get_posts_by_tags() help</h3>
  <code>
    $params = [
      'post_type'   => '',
      'size'        => '',
      'tags'        => '',
      'return_type' => ''
    ];<br/><br/>
    get_posts_by_tags($params);<br/>
  </code><br/><br/>
  <p>Retrieves posts by tags based on the provided parameters.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'post_type'</code> (string) - The post type.</li>
    <li><code>'size'</code> (string) - The size.</li>
    <li><code>'tags'</code> (array|string) - The tags.</li>
    <li><code>'return_type'</code> (string) - The return type.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The generated response based on the return type.</p>

  <p><strong>Throws:</strong></p>
  <p>None</p>
<?php
}
