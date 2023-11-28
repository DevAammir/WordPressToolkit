<?php

/***
 * COMMON FUNCTIONS FOR WPT DATABASE QUERY RELATED FUNCTIONS
 * ****/
/**
 * Generates a JSON response for a given query, status, and message.
 *
 * @param mixed $query The query to generate the JSON response for.
 * @param mixed $status The status of the response.
 * @param mixed $message The message of the response.
 * @throws Some_Exception_Class Description of the exception.
 * @return string The JSON-encoded response.
 */
function _wpt_generate_json_response_for_posts($query, $status, $message)
{
    $num = 0;
    $posts = array();

    while ($query->have_posts()) : $query->the_post();
        $num++;
        $post_data = array(
            'position'    => $num,
            'id'           => get_the_ID(),
            'title'        => get_the_title(),
            'permalink'    => get_permalink(),
            'post_date'    => get_the_date('F j, Y'),
            'author'       => get_the_author_meta('display_name'),
            'categories'   => get_the_category_list(', '),
            'tags'         => get_the_tag_list('', ', '),
        );

        if (!empty($params['size']) && $params['size'] == 'full') {
            $post_data['content'] = get_the_content();
        } elseif (!empty($params['size'])) {
            $post_data['excerpt'] = wp_trim_words(get_the_excerpt(), $params['size']);
        }

        // Additional meta information
        $postmeta = get_post_meta(get_the_ID());
        $meta_data = array();
        if (!empty($postmeta)) {
            $meta_data = wpt_get_postmeta_by_id(get_the_ID(), 'json');
        }
        $post_data['meta_data'] = $meta_data;

        $posts[] = $post_data;
    endwhile;

    // Pagination
    $pagination = array();
    if (!empty($params['per_page']) && $params['per_page'] !== -1) {
        $big = 999999999; // need an unlikely integer
        $pagination = array(
            'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'  => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total'   => $query->max_num_pages,
        );
    }

    $content = array(
        'total_posts' => $query->found_posts,
        'post_data'   => $posts,
        'pagination'  => $pagination,
    );

    return json_encode(array(
        'result'  => $content,
        'status'  => $status,
        'message' => $message
    ));
}



/**
 * Generates an array response for the given query, status, and message.
 *
 * @param mixed $query The query to generate the response for.
 * @param mixed $status The status of the response.
 * @param mixed $message The message of the response.
 * @return array The generated array response.
 */
function _wpt_generate_array_response_for_posts($query, $status, $message)
{
    $num = 0;
    $posts = array();

    while ($query->have_posts()) : $query->the_post();
        $num++;
        $post_data = array(
            'position'    => $num,
            'id'           => get_the_ID(),
            'title'        => get_the_title(),
            'permalink'    => get_permalink(),
            'post_date'    => get_the_date('F j, Y'),
            'author'       => get_the_author_meta('display_name'),
            'categories'   => get_the_category_list(', '),
            'tags'         => get_the_tag_list('', ', '),
        );

        if (!empty($params['size']) && $params['size'] == 'full') {
            $post_data['content'] = get_the_content();
        } elseif (!empty($params['size'])) {
            $post_data['excerpt'] = wp_trim_words(get_the_excerpt(), $params['size']);
        }

        // Additional meta information
        $postmeta = get_post_meta(get_the_ID());
        $meta_data = array();
        if (!empty($postmeta)) {
            $meta_data = wpt_get_postmeta_by_id(get_the_ID(), 'array');
        }
        $post_data['meta_data'] = $meta_data;

        $posts[] = $post_data;
    endwhile;


    $content = array(
        'total_posts' => $query->found_posts,
        'post_data'   => $posts
    );

    return array(
        'result'  => $content,
        'status'  => $status,
        'message' => $message
    );
}

/**
 * Generates an HTML response for the given query and parameters.
 *
 * @param mixed $query The query object.
 * @param array $params An array of parameters.
 * @throws Exception Thrown if an error occurs.
 * @return string The generated HTML response.
 */
function _wpt_generate_html_response_for_posts($query, $params)
{
    ob_start();
    $num = 0;
?>
    <!-------wpt-posts-wrapper--------->
    <div class='wpt-posts-wrapper all'>
        <div class='wpt-results-total'> <?php echo $query->found_posts; ?> results found</div>

        <?php while ($query->have_posts()) : $query->the_post();
            $num++;
            $post_slug = esc_attr(get_post_field('post_name', get_the_ID()));
            $category = get_the_category();
            $category_class = $category ? esc_attr($category[0]->slug) : '';

            $the_post_type = get_post_type();
            $post_type_class = 'post-type-' . esc_attr($the_post_type);

            if (!empty($params['size']) && $params['size'] != "full") {
                $excerpt = wp_trim_words(get_the_excerpt(), $params['size']);
            }
        ?>
            <div class="post-wrapper <?php echo $category_class; ?> pos-<?php echo $num; ?> post-<?php echo get_the_ID(); ?> <?php echo $post_slug; ?>  <?php echo $post_type_class; ?>">
                <h3><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>
                <div class="featuerd-image-wrapper">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('full', array('class' => 'featuerd-image')); ?></a>
                </div>
                <div class="meta-info">
                    <p class="the-date">Date: <?php echo get_the_date('F j, Y'); ?></p>
                    <p class="the-author">Author: <?php echo get_the_author_meta('display_name'); ?></p>
                    <p class="the-categories">Categories: <?php echo get_the_category_list(', '); ?> </p>
                    <p class="the-tags">Tags: <?php echo get_the_tag_list('', ', '); ?></p>
                </div>

                <?php if ($params['size'] == 'full') : ?>
                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>
                <?php elseif (@$params['size'] == 0 || empty(@$params['size'])) : ?>
                <?php else : ?>
                    <div class="post-excerpt">
                        <?php echo $excerpt; ?><a href="<?php the_permalink(); ?>" class="read-more-inline">Read More</a>
                    </div>
                <?php endif; ?>
                <div class="wpt-post-meta">
                    <?php
                    $postmeta = get_post_meta(get_the_ID());
                    if (!empty($postmeta)) :
                        echo wpt_get_postmeta_by_id(get_the_ID(), 'html');
                    endif; ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
            </div>
        <?php
        endwhile;

        if (!empty($params['per_page']) && $params['per_page'] !== -1) :
            // Pagination
            $big = 999999999; // need an unlikely integer
            echo '<div class="pagination">';
            echo paginate_links(array(
                'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'  => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total'   => $query->max_num_pages,
            ));
            echo '</div>';
        endif;
        ?>
    </div> <!-- ends .wpt-posts-wrapper--->
<?php
    $content = ob_get_clean();
    return $content;
}
/**
 * Handles the case when there are no posts to display.
 *
 * @param array $params An associative array of parameters.
 *                      - return_type (string): The type of data to return ('html' or 'json').
 * @return string|array The HTML error message or a JSON response indicating the error.
 */
function _wpt_handle_no_posts($params)
{
    if (isset($params['return_type'])) :
        if ($params['return_type'] === 'html') {
            echo '<div class="error">No posts found</div>';
        } elseif ($params['return_type'] === 'json') {
            echo json_encode(array(
                'result'  => 'No posts found',
                'status'  => 404,
                'message' => 'No posts found'
            ));
        } elseif ($params['return_type'] === 'array') {
            echo  array(
                'result'  => 'No posts found',
                'status'  => 404,
                'message' => 'No posts found'
            );
        } else {
            echo 'No post found';
        }
    else :
        echo 'No post found';
    endif;
}

/**
 * Generates an HTML response for a single post.
 *
 * @param object $post The post object.
 * @throws Some_Exception_Class If an error occurs.
 * @return string The generated HTML response.
 */
function _wpt_generate_html_response_for_posts_for_single_post($post)
{
    $category = get_the_category($post->ID);
    $category_class = $category ? ($category[0]->slug) : '';
    $the_post_type = get_post_type();
    $post_type_class = 'post-type-' . esc_attr($the_post_type);
    ob_start();
?>
    <div class="post-wrapper <?php echo $category_class . ' post-' . $post->ID; ?> <?php echo $post->post_name; ?> <?php echo $post_type_class; ?>">
        <h3><?php echo $post->post_title; ?></h3>
        <div class="featuerd-image-wrapper">
            <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'full'); ?>" class="featuerd-image" />
        </div>
        <div class="meta-info">
            <p class="the-date">Date: <?php echo get_the_date('F j, Y', $post->ID); ?></p>
            <p class="the-author">Author: <?php echo get_the_author_meta('display_name', $post->post_author); ?></p>
            <p class="the-categories">Categories: <?php echo get_the_category_list(', ', '', $post->ID); ?> </p>
            <p class="the-tags">Tags: <?php echo get_the_tag_list('', ', ', '', $post->ID); ?></p>
        </div>
        <div class="post-content">
            <?php echo $post->post_content; ?>
        </div>
        <div class="wpt-post-meta">
            <?php
            echo wpt_get_postmeta_by_id($post->ID, 'html');
            ?>
        </div>
    </div>
<?php
    $content = ob_get_clean();

    return $content;
}

/**
 * Generates a JSON response based on the given post.
 *
 * @param mixed $post The post to include in the response.
 * @return string The JSON-encoded response.
 */
function _wpt_generate_json_response_for_posts_for_single_post($post)
{
    $post_data = array(
        'id'           => get_the_ID(),
        'title'        => get_the_title(),
        'permalink'    => get_permalink(),
        'post_date'    => get_the_date('F j, Y'),
        'author'       => get_the_author_meta('display_name'),
        'categories'   => get_the_category_list(', '),
        'tags'         => get_the_tag_list('', ', '),
        'featured_image' => get_the_post_thumbnail_url(get_the_ID(), 'full')
    );

    $post_data['meta'] = wpt_get_postmeta_by_id(get_the_ID(), 'json');
    $status = 200;
    $message = 'success';

    $response = json_encode([
        'result'  => $post_data,
        'status'  => $status,
        'message' => $message
    ]);

    return $response;
}

/**
 * Generates an array response for a single post.
 *
 * @param mixed $post The post data.
 * @return array The generated response array.
 */
function _wpt_generate_array_response_for_posts_for_single_post($post)
{
    $post_data = array(
        'id'           => get_the_ID(),
        'title'        => get_the_title(),
        'permalink'    => get_permalink(),
        'post_date'    => get_the_date('F j, Y'),
        'author'       => get_the_author_meta('display_name'),
        'categories'   => get_the_category_list(', '),
        'tags'         => get_the_tag_list('', ', '),
        'featured_image' => get_the_post_thumbnail_url(get_the_ID(), 'full')
    );

    $post_data['meta'] = wpt_get_postmeta_by_id(get_the_ID(), 'json');
    $status = 200;
    $message = 'success';

    $response = [
        'result'  => $post_data,
        'status'  => $status,
        'message' => $message
    ];

    return $response;
}

/**
 * Generates a nested HTML table from the given data array.
 *
 * @param array $data The data array to generate the table from.
 * @throws Exception If an error occurs during the generation process.
 * @return string The generated HTML table.
 */
function _wpt_generate_nested_table($data) {
    // Check if $data is an array
    if (!is_array($data)) {
        // If not an array, treat it as a string and return a single row table
        return '<table class="table meta-table wpt_meta_table"><tr><td>' . $data . '</td></tr></table>';
    }

    $html_output = '<table class="table meta-table wpt_meta_table">';
    $n = 0;

    foreach ($data as $key => $value) {
        $n++;
        $html_output .= '<tr data-key="' . $key . '" data-value="' . $value . '" data-n="' . $n . '"><td>' . $key . '</td><td>';

        // Check if the current $value is a serialized array
        if (is_string($value) && ($unserialized_value = maybe_unserialize($value)) !== false) {
            // If it is a serialized array, recursively call the function with the unserialized value
            $html_output .= _wpt_generate_nested_table($unserialized_value);
        } elseif (is_array($value)) {
            // If it is an array (other than _product_attributes), recursively call the function with the array
            $html_output .= _wpt_generate_nested_table($value);
        } else {
            // If it is not an array or _product_attributes, display the value
            $html_output .= $value;
        }

        $html_output .= '</td></tr>';
    }

    $html_output .= '</table>';

    return $html_output;
}


/**
 * Converts a nested array or serialized array to a multidimensional array.
 *
 * @param mixed $data The array or serialized array to be converted.
 * @throws None
 * @return array The converted multidimensional array.
 */
function _wpt_convert_to_nested_array($data) {
    // Check if $data is an array
    if (!is_array($data)) {
        // If not an array, treat it as a string and return it
        return $data;
    }

    $result = array();

    foreach ($data as $key => $value) {
        // Check if the current $value is a serialized array
        if (is_string($value) && ($unserialized_value = maybe_unserialize($value)) !== false) {
            // If it is a serialized array, recursively call the function with the unserialized value
            $result[$key] = _wpt_convert_to_nested_array($unserialized_value);
        } elseif (is_array($value)) {
            // If it is an array, recursively call the function with the array
            $result[$key] = _wpt_convert_to_nested_array($value);
        } else {
            // If it is not an array, simply assign the value
            $result[$key] = $value;
        }
    }

    return $result;
}

/**
 * Retrieves user data with metadata.
 *
 * @param object $user The user object.
 * @return array The retrieved user data with metadata.
 */
function _wpt_user_data_with_metadata($user_data, $return_type)
{
    if ($user_data) {
        $user_data_array = [];
        $unique_users_count = 0; // Counter for unique users

        foreach ($user_data as $user) {
            // Get user meta for the specific key
            $user_meta_value = get_user_meta($user->ID);

            // Create an array for user data
            $user_data = [
                'ID'                 => $user->ID,
                'user_login'         => $user->user_login,
                'user_pass'          => $user->user_pass,
                'user_nicename'      => $user->user_nicename,
                'user_email'         => $user->user_email,
                'user_url'           => $user->user_url,
                'user_registered'    => $user->user_registered,
                'user_activation_key'=> $user->user_activation_key,
                'user_status'        => $user->user_status,
                'display_name'       => $user->display_name,
            ];

            // Merge user data and user meta data manually
            foreach ($user_meta_value as $meta_key => $meta_values) {
                $user_data[$meta_key] = $meta_values[0];
            }

            // Check if the user ID already exists in the result array
            $existing_user_key = array_search($user->ID, array_column($user_data_array, 'ID'));

            if ($existing_user_key === false) {
                // Convert to the specified return type
                $user_data_array[] = _wpt_convert_to_nested_array($user_data);
                $unique_users_count++;
            }
        }

        return [
            'total'   => $unique_users_count,              
            'result'  =>  ($return_type === 'json') ? json_encode($user_data_array) : $user_data_array,
            'status'  => 'success',
            'message' => 'User data retrieved successfully',
        ];
    }

    // Return an error result if user_data is empty
    return [
        'result'  => 'User not found',
        'status'  => 'error',
        'message' => 'User not found',
        'total'   => 0
    ];
}
