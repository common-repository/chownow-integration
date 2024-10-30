<?php
/*
Plugin Name: ChowNow Integration
Description: Add ChowNow ordering widget to website
Version: 1.0.0
Author: ChowNow
Author URI: https://get.chownow.com/
 */

//Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load Class
require_once plugin_dir_path(__FILE__) . '/includes/cn-class.php';

// Register widget
function register_chownow()
{
    register_widget('ChowNow_Widget');
    register_sidebar(
        array(
            'name' => __('ChowNow Sidebar', 'cn_domain'),
            'id' => 'chownow-sidebar',
            'description' => __('Add widgets here for ChowNow', 'cn_domain'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        )
    );
}

// Hook in function
add_action('widgets_init', 'register_chownow');

function chownow_footer_widget()
{
    dynamic_sidebar('chownow-sidebar');
}
add_action('get_footer', 'chownow_footer_widget');


