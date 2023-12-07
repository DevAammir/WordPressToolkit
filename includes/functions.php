<?php

/****
 * EXECUTE SETTING
 * **/
function wpt_execute_setting($setting, $callback)
{
    if (!empty(WPT_SETTINGS[$setting])) {
        $callback();
    }
}
/* * *
 * DISABLE GUTENBURG EVERYWHERE
 * * */
function wpt_disable_gutenburg_everywhere()
{
    // Disable Gutenberg editor
    add_filter('use_block_editor_for_post', '__return_false', 10);

    // Disable Gutenberg widgets
    add_filter('use_widgets_block_editor', '__return_false');

    // Disable Gutenberg on post type
    add_filter('gutenberg_can_edit_post_type', '__return_false');

    // Disable Gutenberg for post
    add_filter('gutenberg_can_edit_post', '__return_false');
}

/**
 * Adds WooCommerce support to the theme.
 *
 * @throws Some_Exception_Class description of exception
 * @return void
 */
function wpt_woocommerce_support()
{
    function mytheme_add_woocommerce_support() {
        add_theme_support( 'woocommerce', array(
            'thumbnail_image_width' => 150,
            'single_image_width'    => 300,
    
            'product_grid'          => array(
                'default_rows'    => 3,
                'min_rows'        => 2,
                'max_rows'        => 8,
                'default_columns' => 4,
                'min_columns'     => 2,
                'max_columns'     => 5,
            ),
        ) );
    }
    add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );
}

/**
 * Generates the CSS classes to be added to the body element.
 *
 * @param array $classes The array of existing CSS classes for the body element.
 * @return array The modified array of CSS classes with the added post slug.
 */
function wpt_add_css_classes_to_body()
{
    function wpt_body_classes($classes)
    {
        global $post;

        // Add post type and post name
        if (isset($post)) {
            $classes[] = $post->post_type . '-' . $post->post_name;
        }
    
        // Add category IDs
        foreach ((get_the_category($post->ID)) as $category) {
            $classes[] = 'cat-' . $category->cat_ID . '-id';
        }
    
        // Add custom post type
        if (is_singular() && get_post_type() !== 'post') {
            $classes[] = 'post-type-' . get_post_type();
        }
    
        // Add taxonomy
        $taxonomies = get_post_taxonomies($post->ID);
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($post->ID, $taxonomy);
            if ($terms) {
                foreach ($terms as $term) {
                    $classes[] = 'taxonomy-' . $taxonomy . '-' . $term->slug;
                }
            }
        }
    
        // Check if it's a custom page template
        $page_template = get_page_template_slug();
        if ($page_template) {
            $template_name = pathinfo($page_template, PATHINFO_FILENAME);
            $classes[] = 'page-template-' . sanitize_html_class($template_name);
        }
    
        // Add other checks as needed
    
        return $classes;
    }
    add_filter('body_class', 'wpt_body_classes');
}

/**
 * Enable error reporting and display in PHP.
 *
 * @throws Exception If there is an error enabling error reporting.
 */
function E_ON()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


/**
 * Dump and die function for debugging purposes.
 *
 * @param mixed $data The data to be dumped.
 * @param mixed|null $exit Optional. If provided, the script execution will be terminated after dumping the data.
 * @throws None
 * @return None
 */
function dd($data, $exit = null)
{
?>
    <div class="row my-4" style="z-index:99999;">
        <pre>TESTING MODE</pre>
        <pre>
        <?php print_r($data); ?> 
    </pre>
    </div>
    <?php
    if (isset($exit)) {
        exit;
    }
}

// include_once 'functions/custom-pagination-beta.php';
// include_once 'functions/wp_ajax_wpt_get_all_posts-beta.php';

include_once 'functions/_common_functions.php';
include_once 'functions/get_post_by_id.php';
include_once 'functions/get_post_by_name.php';
include_once 'functions/get_all_posts.php';
include_once 'functions/get_posts_by_ids.php';
include_once 'functions/get_posts_by_meta.php';
include_once 'functions/get_posts_by_categories.php';
include_once 'functions/get_posts_by_author.php';
include_once 'functions/get_postmeta_by_id.php';
include_once 'functions/update_usermeta.php';
include_once 'functions/delete_category.php';
include_once 'functions/delete_post.php';
include_once 'functions/delete_tag.php';
include_once 'functions/delete_user.php';
include_once 'functions/remove_featured_image.php';
include_once 'functions/delete_taxonomy_term.php';
include_once 'functions/set_meta.php';
include_once 'functions/create_post_beta.php';
include_once 'functions/get_user_by_id.php';
include_once 'functions/get_users_by_meta.php';
include_once 'functions/get_user_id_by_username.php';
include_once 'functions/get_username_by_id.php';
include_once 'functions/get_user_id_by_email.php';
include_once 'functions/get_users_by_role.php';
include_once 'functions/get_post_thumbnail_by_post_id.php';
include_once 'functions/get_post_parent_by_id.php';
include_once 'functions/get_posts_by_tags.php';
include_once 'functions/create_post.php';
include_once 'functions/create_user.php';
include_once 'functions/update_post.php';
include_once 'functions/update_user_by_id.php';
include_once 'functions/create_custom_role.php';
include_once 'functions/_wpt_upload_user_image.php';
//  include_once 'functions/user_process.php';
// include_once 'functions/xxxx.php';
// include_once 'functions/xxxx.php';
