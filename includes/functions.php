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

/* * *
 * WOOCOMMERECE SUPPORT 
 * */
function wpt_woocommerce_support()
{
    function add_woo_support()
    {
        add_theme_support('woocommerce');
    }
    add_action('after_setup_theme', 'add_woo_support');
}

/* * *
 * ADD CSS CLASSES TO BODY 
 * */
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

/* * *
 * ERROR REPORTING ON 
 * */
function E_ON()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


/* * *
 *  DUMP DATA
 * * */
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


/* * *
 *  UNIVERSAL AJAX ENDPOINT BETA VERSION
 * * */

 add_action( 'wp_ajax_wpt_universal_ajax_endpoint', 'wpt_universal_ajax_endpoint' );
 add_action( 'wp_ajax_nopriv_wpt_universal_ajax_endpoint', 'wpt_universal_ajax_endpoint' );
 
 function wpt_universal_ajax_endpoint() {
     // Security check
     check_ajax_referer( 'wpt_ajax_nonce', 'security' );
 
     // Get the AJAX action
     $action = isset( $_POST['action'] ) ? $_POST['action'] : '';
 
     // Perform different actions based on the AJAX action
     switch ( $action ) {
         case 'get_posts':
             // Example: Retrieve posts based on some criteria
             $posts = get_posts( array(
                 'post_type' => 'post',
                 'posts_per_page' => 5,
             ) );
             
             // Return the posts as JSON
             $result = wp_send_json_success( $posts );
             $message =  'successfully retrieved posts' ;
             break;
 
         case 'submit_form':
             // Example: Handle form submission
             $name = sanitize_text_field( $_POST['name'] );
             $email = sanitize_email( $_POST['email'] );
 
             // Perform validation and processing
 
             // Return success or error message
             $message =  'Form submitted successfully' ;
             // or wp_send_json_error( 'Form submission failed' );
             break;
 
         // Add more cases for other AJAX actions as needed
 
         default:
             // Return an error for unknown actions
             $message =  'Invalid AJAX action' ;
             break;
     }
 
     $status = 1; 
     $return = json_encode(array('result' => $result, 'Status' => $status, 'message' => $message, 'request' => $_REQUEST, 'args' => $args));
     echo $return;
     wp_die();
 }
 