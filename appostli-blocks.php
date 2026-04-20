<?php
/**
 * Plugin Name: Appostli Blocks
 * Description: Custom 8-bit retro blocks for Elementor (Divider & News Ticker).
 * Plugin URI:  https://sawahsolutions.com
 * Author:      Mohamed Sawah
 * Author URI:  https://sawahsolutions.com
 * Version:     1.0.9
 * Text Domain: appostli-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register Appostli Widgets.
 */
function register_appostli_blocks( $widgets_manager ) {
    // Require the widget files
    require_once( __DIR__ . '/widgets/retro-divider.php' );
    require_once( __DIR__ . '/widgets/news-ticker.php' );
    require_once( __DIR__ . '/widgets/featured-news-large.php' );
    require_once( __DIR__ . '/widgets/latest-news-list.php' );
    require_once( __DIR__ . '/widgets/text-news-list.php' );
    require_once( __DIR__ . '/widgets/numbered-news-list.php' );

    // Register the widgets
    $widgets_manager->register( new \Appostli_Retro_Divider() );
    $widgets_manager->register( new \Appostli_News_Ticker() );
    $widgets_manager->register( new \Appostli_Featured_News_Large() );
    $widgets_manager->register( new \Appostli_Latest_News_List() );
    $widgets_manager->register( new \Appostli_Text_News_List() );
    $widgets_manager->register( new \Appostli_Numbered_News_List() );
}
add_action( 'elementor/widgets/register', 'register_appostli_blocks' );

/**
 * Initialize global variable to keep track of displayed posts for the "Unique" feature.
 */
add_action( 'wp', function() {
    global $appostli_shown_posts;
    $appostli_shown_posts = array();
});