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
    function add_woo_support()
    {
        add_theme_support('woocommerce');
    }
    add_action('after_setup_theme', 'add_woo_support');
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
        $post_slug = $post->post_name;
        $classes[] = $post_slug;

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


/*
// to test
add_action('wp_footer', function () {


    //echo wpt_get_post_by_id(78, 'html');
    // echo wpt_get_post_by_name('lorem','html', 'work'); 

    // Example: Retrieve a custom post type named 'my_custom_post_type' as HTML
    // $content = wpt_get_post_by_name('shrieker', 'html', 'test');
    // echo $content;
    ?><div id="test-321"></div>
    <script>

		
        (function($) {
            $(document).ready(function() {
                
                // Trigger the AJAX request on document click
                // $(document).on('click', function() {
                    // wpt_get_post_by_id(78,  '#test', '<?php //echo WPT_AJAX;?>');
					// wpt_get_post_by_name('home', 'page', '#test', '<?php //echo WPT_AJAX;?>');
					// wpt_get_posts(per_page, post_type, size, target,wpt_ajax_url);
					wpt_get_posts('-1', 'post','0','#test-321', '<?php echo WPT_AJAX;?>');
				
                // });

            });
        })(jQuery);
    </script>
<?php
});
*/