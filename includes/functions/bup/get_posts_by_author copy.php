<?php

function get_posts_by_author($params = array())
{
    $size = empty($params['size']) ?  0 : $params['size'];

    $args = array(
        'post_type'      => $params['post_type'],
        'posts_per_page' => empty($params['per_page']) ?  -1 : $params['per_page'],
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'excerpt_length' => empty($params['size']) ?  0 : $params['size'],
    );
    if (!empty($params['author'])) {
        $author_data = get_user_by('login', $params['author']);
        if ($author_data) {
            $args['author'] = $author_data->ID;
        } elseif (is_numeric($params['author'])) {
            $args['author'] = $params['author'];
        }
    }
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        if ($params['return_type'] === 'html') {
            $response = '<div class="error">No posts found</div>';
        } else {
            $response = json_encode(array(
                'result'  => 'No posts found',
                'status'  => 404,
                'message' => 'No posts found'
            ));
        }
    } else {
        $status = 200;
        $message = 'success';
        if ($params['return_type'] === 'html') {
            ob_start();
            $num = 0; ?>
            <!-------wpt-posts-wrapper--------->
            <div class='wpt-posts-wrapper all'>
                <div class='wpt-results-total'> <?php echo $query->found_posts; ?> results found</div>
                <?php while ($query->have_posts()) : $query->the_post();
                    $num++;
                    $post_slug = esc_attr(get_post_field('post_name', get_the_ID()));
                    $category = get_the_category();
                    $category_class = $category ? esc_attr($category[0]->slug) : '';
                    if (!empty($params['size']) && $params['size'] != "full") {
                        $excerpt = wp_trim_words(get_the_excerpt(), $params['size']);
                    }
                ?>
                    <div class="post-wrapper <?php echo $category_class; ?> pos-<?php echo $num; ?> post-<?php echo get_the_ID(); ?> <?php echo $post_slug; ?>">
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
                    echo '</div>'; ?>
            </div> <!-- ends .wpt-posts-wrapper--->
<?php endif;
                $content = ob_get_clean();
                $response = $content;
            } elseif ($params['return_type'] === 'json') {
                $response = json_encode(array(
                    'result'  => $query->posts,
                    'status'  => $status,
                    'message' => $message
                ));
            }
        }

        wp_reset_postdata();
        return $response;
    }


    function get_posts_by_author_endpoint()
    {
        echo  get_posts_by_author([
            'per_page'    => -1,
            'post_type'   => $_REQUEST['post_type'],
            'return_type' => 'html',
            'size' =>   $_REQUEST['size'],
            'author' =>   $_REQUEST['author'],
        ]);
        die();
    }

    add_action('wp_ajax_get_posts_by_author_endpoint', 'get_posts_by_author_endpoint');
    add_action('wp_ajax_nopriv_get_posts_by_author_endpoint', 'get_posts_by_author_endpoint');

    add_shortcode('get_posts_by_author', 'get_posts_by_author_shortcode');

    function get_posts_by_author_shortcode($atts)
    {
        return get_posts_by_author([
            'per_page'    => !empty($atts['per_page']) ? $atts['per_page'] : -1,
            'post_type'   => $atts['post_type'],
            'return_type' => 'html',
            'size' =>   $atts['size'],
            'author' =>   $atts['author'],
        ]);
    }