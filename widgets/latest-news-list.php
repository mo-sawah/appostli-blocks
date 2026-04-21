<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Appostli_Latest_News_List extends \Elementor\Widget_Base {

    public function get_name() { return 'appostli_latest_news_list'; }
    public function get_title() { return esc_html__( 'Retro News Grid', 'appostli-blocks' ); }
    public function get_icon() { return 'eicon-post-list'; }
    public function get_categories() { return [ 'general' ]; }

    private function get_terms_list($taxonomy) {
        $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
        $options = [];
        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            foreach ( $terms as $term ) { $options[ $term->term_id ] = $term->name; }
        }
        return $options;
    }

    protected function register_controls() {
        // --- QUERY SETTINGS ---
        $this->start_controls_section('query_section', [ 'label' => 'Query', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_control('posts_per_page', [ 'label' => 'Number of Posts', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 4 ]);
        $this->add_control('include_categories', [ 'label' => 'Include Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('exclude_categories', [ 'label' => 'Exclude Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('unique_posts', [ 'label' => 'Make Unique', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ]);
        $this->end_controls_section();

        // --- LAYOUT & GRID SETTINGS ---
        $this->start_controls_section(
            'layout_section',
            [
                'label' => __( 'Grid & Pagination', 'appostli' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'appostli' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                ],
                'selectors' => [
                    '{{WRAPPER}} .appostli-list-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __( 'Pagination', 'appostli' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => 'None',
                    'numbers' => 'Numbered Pages',
                    'load_more' => 'Load More (AJAX)',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        global $appostli_shown_posts;
        
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = [ 
            'post_type' => 'post', 
            'post_status' => 'publish', 
            'posts_per_page' => $settings['posts_per_page'],
            'paged' => $paged
        ];
        
        // Apply Categories
        if ( ! empty( $settings['include_categories'] ) ) { $args['category__in'] = $settings['include_categories']; }
        if ( ! empty( $settings['exclude_categories'] ) ) { $args['category__not_in'] = $settings['exclude_categories']; }
        
        // Unique posts filter
        if ( 'yes' === $settings['unique_posts'] && ! empty( $appostli_shown_posts ) ) { $args['post__not_in'] = $appostli_shown_posts; }

        // If used on a category/author template directly (Dynamic Archive handling)
        $current_obj = get_queried_object();
        $current_cat = '';
        $current_author = '';

        if ( is_category() ) {
            $args['cat'] = $current_obj->term_id;
            $current_cat = $current_obj->term_id;
        } elseif ( is_author() ) {
            $args['author'] = $current_obj->ID;
            $current_author = $current_obj->ID;
        }

        $query = new \WP_Query( $args );

        ?>
        <style>
            .appostli-list-grid {
                display: grid;
                gap: 20px;
                font-family: 'VT323', monospace;
            }
            .appostli-post-card {
                border: 1px dashed #333333;
                padding: 20px;
                background-color: #050505;
                transition: border-color 0s;
            }
            .appostli-post-card:hover {
                border-color: #5CBA47;
            }
            .appostli-card-meta {
                display: flex;
                align-items: center;
                gap: 15px;
                border-bottom: 1px solid #333333;
                padding-bottom: 10px;
                margin-bottom: 15px;
            }
            .appostli-retro-avatar img {
                border: 2px solid #FFFFFF;
                filter: grayscale(100%) contrast(1.2);
                border-radius: 0;
            }
            .appostli-author { color: #555555; font-size: 18px; text-transform: uppercase; }
            .appostli-date { color: #888888; font-size: 16px; }
            .appostli-card-title a {
                color: #FFFFFF;
                font-size: 24px;
                text-transform: uppercase;
                text-decoration: none;
            }
            .appostli-card-title a:hover { color: #5CBA47; }
            
            /* Pagination & Load More Styling */
            .appostli-pagination-wrap { margin-top: 30px; text-align: center; font-family: 'VT323', monospace; }
            .appostli-page-numbers .page-numbers {
                padding: 5px 12px;
                border: 1px solid #333333;
                color: #888888;
                font-size: 20px;
                text-decoration: none;
                margin: 0 5px;
            }
            .appostli-page-numbers .page-numbers.current,
            .appostli-page-numbers .page-numbers:hover {
                color: #FFFF00;
                border-color: #FFFF00;
            }
            .appostli-load-more-btn {
                background: transparent;
                border: 2px solid #5CBA47;
                color: #5CBA47;
                font-family: 'VT323', monospace;
                font-size: 24px;
                padding: 10px 20px;
                cursor: pointer;
                text-transform: uppercase;
            }
            .appostli-load-more-btn:hover { background: #5CBA47; color: #000; }
            .appostli-load-more-btn:disabled { border-color: #333; color: #333; cursor: not-allowed; }
        </style>

        <div class="appostli-list-grid" id="appostli-post-container">
            <?php
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    if ( 'yes' === $settings['unique_posts'] ) { 
                        $appostli_shown_posts[] = get_the_ID(); 
                    }
                    
                    $author_id = get_the_author_meta('ID');
                    $avatar = get_avatar($author_id, 40);
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
                }
            } else {
                echo '<p>[ ERROR: NO_DATA_FOUND ]</p>';
            }
            ?>
        </div>

        <?php 
        /* --- PAGINATION LOGIC --- */
        if ( $settings['pagination_type'] === 'numbers' ) {
            $total_pages = $query->max_num_pages;
            if ($total_pages > 1) {
                echo '<div class="appostli-pagination-wrap appostli-page-numbers">';
                $current_page = max(1, get_query_var('paged'));
                echo paginate_links([
                    'base' => get_pagenum_link(1) . '%_%',
                    'format' => 'page/%#%',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => '< PREV',
                    'next_text' => 'NEXT >',
                ]);
                echo '</div>';
            }
        } 
        elseif ( $settings['pagination_type'] === 'load_more' && $query->max_num_pages > 1 ) {
            ?>
            <div class="appostli-pagination-wrap">
                <button class="appostli-load-more-btn" id="appostliLoadMore" 
                    data-page="1" 
                    data-max="<?php echo $query->max_num_pages; ?>" 
                    data-ppp="<?php echo $settings['posts_per_page']; ?>"
                    data-cat="<?php echo $current_cat; ?>"
                    data-author="<?php echo $current_author; ?>">
                    [ LOAD MORE DATA ]
                </button>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $('#appostliLoadMore').on('click', function() {
                    var button = $(this);
                    var page = button.data('page');
                    var max_page = button.data('max');
                    var next_page = page + 1;

                    button.text('[ FETCHING... ]');

                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'appostli_load_more',
                            page: next_page,
                            posts_per_page: button.data('ppp'),
                            cat: button.data('cat'),
                            author: button.data('author')
                        },
                        success: function(response) {
                            if(response) {
                                $('#appostli-post-container').append(response);
                                button.data('page', next_page);
                                button.text('[ LOAD MORE DATA ]');

                                if (next_page >= max_page) {
                                    button.text('[ END OF FILE ]').attr('disabled', true);
                                }
                            }
                        }
                    });
                });
            });
            </script>
            <?php
        }
        
        wp_reset_postdata();
    }
}