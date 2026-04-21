<?php
/**
 * Plugin Name: Appostli Blocks
 * Description: Custom 8-bit retro blocks for Elementor (Divider & News Ticker).
 * Plugin URI:  https://sawahsolutions.com
 * Author:      Mohamed Sawah
 * Author URI:  https://sawahsolutions.com
 * Version:     1.0.11
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

/**
 * AJAX Handler for Appostli Load More
 */
add_action('wp_ajax_appostli_load_more', 'appostli_load_more_scripts');
add_action('wp_ajax_nopriv_appostli_load_more', 'appostli_load_more_scripts');

function appostli_load_more_scripts() {
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 4;
    
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    );

    // If we are on a category or author archive, pass those variables
    if (!empty($_POST['cat'])) $args['cat'] = intval($_POST['cat']);
    if (!empty($_POST['author'])) $args['author'] = intval($_POST['author']);

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $author_id = get_the_author_meta('ID');
            $avatar = get_avatar($author_id, 32); 
            ?>
            <div class="appostli-post-card">
                <div class="appostli-card-meta">
                    <?php if ($avatar) echo '<div class="appostli-retro-avatar">' . $avatar . '</div>'; ?>
                    <div class="appostli-card-meta-text">
                        <span class="appostli-author">BY: <?php the_author(); ?></span><br>
                        <span class="appostli-date"><?php echo get_the_date('d M Y'); ?></span>
                    </div>
                </div>
                <h3 class="appostli-card-title">
                    <a href="<?php the_permalink(); ?>">> <?php the_title(); ?></a>
                </h3>
            </div>
            <?php
        endwhile;
    endif;
    wp_reset_postdata();
    die();
