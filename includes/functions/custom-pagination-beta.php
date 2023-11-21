<?php 

add_action('wp_ajax_custom_pagination', 'custom_pagination');
add_action('wp_ajax_nopriv_custom_pagination', 'custom_pagination');


// Custom pagination function in functions.php
function custom_pagination()
{
    $paged = $_POST['page'];
    $per_page = $_POST['per_page'];
    $post_type = $_POST['post_type'];

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $per_page,
        'paged' => $paged
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            // Output your post data here
            echo '<h2>' . get_the_title() . '</h2>';
            echo '<div>' . get_the_content() . '</div>';
        // Add more fields as needed
        endwhile;

        echo '<div id="load-more-btn-container"><button id="load-more-btn">Load More</button></div>';
    endif;

    wp_die();
}


// Inline script in wp_footer
add_action('wp_footer', function () {
    ?>
    <script>
        jQuery(function($) {
            var page = 1;
            var loading = false;
            var perPage = 2; // Set your default value
            var postType = 'post'; // Set your default value

            function load_posts(paged) {
                if (loading) return;
                loading = true;

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: {
                        action: 'custom_pagination',
                        page: paged,
                        per_page: perPage,
                        post_type: postType,
                    },
                    beforeSend: function() {
                        // $('#load-more-btn').hide();
                        $('#ajax-posts').append('<div class="loader">Please wait...</div>');
                    },
                    success: function(response) {
                        $('.loader').hide();
                        $('#ajax-posts').append(response);
                        loading = false;
                    }
                });
            }

            function load_more_posts() {
                page++;
                load_posts(page);
            }

            // Load posts initially
            load_posts(page);

            // Load more button click event
            $(document).on('click', '#load-more-btn', function(e) {
                e.preventDefault();
                $(this).hide();
                load_more_posts();
            });
        });
    </script>
<?php
});