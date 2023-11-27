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
function _wpt_generate_json_response($query, $status, $message)
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
            foreach ($postmeta as $key => $val) {
                $meta_data[] = array(
                    'key'   => $key,
                    'value' => $val[0],
                );
            }
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
function _wpt_generate_array_response($query, $status, $message)
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
            foreach ($postmeta as $key => $val) {
                $meta_data[] = array(
                    'key'   => $key,
                    'value' => $val[0],
                );
            }
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
function _wpt_generate_html_response($query, $params)
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
                            if (!empty($postmeta)) : $n = 0;
                                foreach ($postmeta as $key => $val) : $n++;
                            ?>
                                    <p class="the-meta meta-<?php echo $key; ?> meta-<?php echo $n; ?>">
                                        <span class="key" data-key="<?php echo $key; ?>"><?php echo $key; ?></span>
                                        <span class="value" data-value="<?php echo $val[0]; ?>"><?php echo $val[0]; ?></span>
                                    </p>
                            <?php
                                endforeach;
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
    if ($params['return_type'] === 'html') {
        return '<div class="error">No posts found</div>';
    } else {
        return json_encode(array(
            'result'  => 'No posts found',
            'status'  => 404,
            'message' => 'No posts found'
        ));
    }
}