<?php


/* * *
 * DISABLE GUTENBURG EVERYWHERE
 * * */

wpt_execute_setting('wpt_disable_gutenburg', 'wpt_disable_gutenburg_everywhere');

if ($current_theme == 'wp-lite') {
}else{
wpt_execute_setting('wpt_add_woocommerce_support', 'wpt_woocommerce_support');
}
wpt_execute_setting('wpt_add_classes_body', 'wpt_add_css_classes_to_body');

wpt_execute_setting('wpt_error_reporting', 'E_ON');
