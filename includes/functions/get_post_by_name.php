<?php


function wpt_get_post_by_name($name, $return_type = 'html', $post_type = 'post')
{
    $args = array(
        'name'        => $name,
        'post_type'   => $post_type,
        'post_status' => 'publish',
        'numberposts' => 1,
    );

    $posts = get_posts($args);

    if (empty($posts)) {
        if ($return_type === 'html') {
            $response = '<div class="error">Post not found</div>';
        } else {
            $response = json_encode(array(
                'result'  => 'Post not found',
                'status'  => 404,
                'message' => 'Post not found',
            ));
        }
    } else {
        $status = 200;
        $message = 'success';

        if ($return_type === 'html') {
            $category = get_the_category($post->ID);
            $category_class = $category ? ($category[0]->slug) : '';
            ob_start();
/*?>
            <div class="post-wrapper <?php echo $category_class; ?>">
                <h3><?php echo $post->post_title; ?></h3>
                <div class="meta-info">
                    <p>Date: <?php echo get_the_date('F j, Y', $post->ID); ?></p>
                    <p>Author: <?php echo get_the_author_meta('display_name', $post->post_author); ?></p>
                    <p>Categories: <?php echo get_the_category_list(', ', '', $post->ID); ?> </p>
                    <p>Tags: <?php echo get_the_tag_list('', ', ', '', $post->ID); ?></p>
                </div>
                <div class="post-content">
                    <?php echo $post->post_content; ?>
                </div>
            </div>
<?php
*/
            $content = ob_get_clean();
            $response = $content;
        } elseif ($return_type === 'json') {
            $response = json_encode(array(
                'result'  => $post,
                'status'  => $status,
                'message' => $message
            ));
        }
    }

    return $response;
}

add_shortcode('get_post_by_name', 'get_post_by_name_shortcode');

function get_post_by_id_shortcode($atts)
{
    $id = $atts['id'];
    $return_type = 'html';  // Default value
    return wpt_get_post_by_id($id, $return_type);
}
