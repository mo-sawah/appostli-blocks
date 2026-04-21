<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Appostli_Latest_News_List extends \Elementor\Widget_Base {

    public function get_name() { return 'appostli_latest_news_list'; }
    public function get_title() { return esc_html__( 'Retro News List', 'appostli-blocks' ); }
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
        // --- QUERY SETTINGS (Your Original) ---
        $this->start_controls_section('query_section', [ 'label' => 'Query', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_control('posts_per_page', [ 'label' => 'Number of Posts', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 4 ]);
        $this->add_control('include_categories', [ 'label' => 'Include Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('exclude_categories', [ 'label' => 'Exclude Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('unique_posts', [ 'label' => 'Make Unique', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ]);
        $this->end_controls_section();

        // --- NEW: GRID & PAGINATION SETTINGS ---
        $this->start_controls_section('grid_section', [ 'label' => 'Grid & Pagination', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'appostli' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [ '1' => '1 Column', '2' => '2 Columns', '3' => '3 Columns' ],
                'selectors' => [ '{{WRAPPER}} .appostli-list-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ],
            ]
        );
        $this->add_control(
            'pagination_type',
            [
                'label' => __( 'Pagination', 'appostli' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [ 'none' => 'None', 'numbers' => 'Numbered Pages', 'load_more' => 'Load More (AJAX)' ],
            ]
        );
        $this->end_controls_section();

        // --- CONTENT LAYOUT (Your Original) ---
        $this->start_controls_section('layout_section', [ 'label' => 'Layout Options', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_group_control(\Elementor\Group_Control_Image_Size::get_type(), [ 'name' => 'image', 'default' => 'medium', 'separator' => 'none' ]);
        $this->add_control('image_width', [
            'label' => 'Image Width (%)', 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => [ 'size' => 40 ],
            'selectors' => [ '{{WRAPPER}} .appostli-list-image' => 'width: {{SIZE}}%;', '{{WRAPPER}} .appostli-list-content' => 'width: calc(100% - {{SIZE}}% - 20px);' ]
        ]);
        $this->add_control('image_ratio', [
            'label' => 'Image Aspect Ratio', 'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'auto' => 'Original', '1 / 1' => '1:1 (Square)', '4 / 3' => '4:3 (Standard)', '16 / 9' => '16:9 (Widescreen)', '21 / 9' => '21:9 (Ultrawide)', '3 / 4' => '3:4 (Portrait)' ],
            'default' => '16 / 9',
            'selectors' => [ '{{WRAPPER}} .appostli-list-image img' => 'aspect-ratio: {{VALUE}}; object-fit: cover; width: 100%;' ]
        ]);
        $this->add_control('show_meta', [ 'label' => 'Show Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_excerpt', [ 'label' => 'Show Excerpt', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'no' ]);
        $this->add_control('title_lines', [
            'label' => 'Max Title Lines', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 4,
            'selectors' => [ '{{WRAPPER}} .appostli-list-title' => 'display: -webkit-box; -webkit-line-clamp: {{VALUE}}; -webkit-box-orient: vertical; overflow: hidden;' ]
        ]);
        $this->end_controls_section();

        // --- STYLES (Your Original) ---
        $this->start_controls_section('style_section', [ 'label' => 'Styles', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_control('title_color', [ 'label' => 'Title Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#5CBA47', 'selectors' => [ '{{WRAPPER}} .appostli-list-title a' => 'color: {{VALUE}};' ]]);
        $this->add_control('meta_color', [ 'label' => 'Meta Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#D32A2A', 'selectors' => [ '{{WRAPPER}} .appostli-list-meta' => 'color: {{VALUE}};' ]]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'label' => 'Title Typography', 'selector' => '{{WRAPPER}} .appostli-list-title' ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'meta_typography', 'label' => 'Meta Typography', 'selector' => '{{WRAPPER}} .appostli-list-meta' ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        global $appostli_shown_posts;
        
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = [ 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $settings['posts_per_page'], 'paged' => $paged ];
        if ( ! empty( $settings['include_categories'] ) ) { $args['category__in'] = $settings['include_categories']; }
        if ( ! empty( $settings['exclude_categories'] ) ) { $args['category__not_in'] = $settings['exclude_categories']; }
        if ( 'yes' === $settings['unique_posts'] && ! empty( $appostli_shown_posts ) ) { $args['post__not_in'] = $appostli_shown_posts; }

        // Dynamic Archive Handling
        $current_obj = get_queried_object();
        $current_cat = ''; $current_author = '';
        if ( is_category() ) { $args['cat'] = $current_obj->term_id; $current_cat = $current_obj->term_id; } 
        elseif ( is_author() ) { $args['author'] = $current_obj->ID; $current_author = $current_obj->ID; }

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) :
            echo '<style>
                /* New Grid CSS */
                .appostli-list-grid { display: grid; gap: 30px; }
                
                /* Your Original CSS */
                .appostli-list-item { display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start; }
                .appostli-list-image img { width: 100%; height: auto; display: block; filter: grayscale(100%) contrast(1.2); transition: 0s; border-bottom: 2px solid #333; }
                .appostli-list-item:hover .appostli-list-image img { filter: grayscale(0%); border-color: #5CBA47; }
                .appostli-list-title { margin: 0 0 10px 0; text-transform: uppercase; }
                .appostli-list-title a { text-decoration: none; }
                .appostli-list-meta { text-transform: uppercase; font-family: "VT323", monospace; }
                .appostli-list-excerpt { margin-top: 10px; text-transform: uppercase; }
                
                /* Pagination CSS */
                .appostli-pagination-wrap { margin-top: 30px; text-align: center; font-family: "VT323", monospace; grid-column: 1 / -1; }
                .appostli-page-numbers .page-numbers { padding: 5px 12px; border: 1px solid #333; color: #888; font-size: 20px; text-decoration: none; margin: 0 5px; }
                .appostli-page-numbers .page-numbers.current, .appostli-page-numbers .page-numbers:hover { color: #FFFF00; border-color: #FFFF00; }
                .appostli-load-more-btn { background: transparent; border: 2px solid #5CBA47; color: #5CBA47; font-family: "VT323", monospace; font-size: 24px; padding: 10px 20px; cursor: pointer; text-transform: uppercase; }
                .appostli-load-more-btn:hover { background: #5CBA47; color: #000; }
                
                @media (max-width: 767px) {
                    .appostli-list-item { flex-direction: column; }
                    .appostli-list-image, .appostli-list-content { width: 100% !important; }
                    .appostli-list-image { margin-bottom: 15px; }
                }
            </style>';

            // We wrap it in your new grid container
            echo '<div class="appostli-list-grid" id="appostli-post-container">';
            
            // Your exact original loop HTML
            while ( $query->have_posts() ) : $query->the_post();
                if ( 'yes' === $settings['unique_posts'] ) { $appostli_shown_posts[] = get_the_ID(); }
                ?>
                <div class="appostli-list-item">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="appostli-list-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php 
                                $settings['image'] = [ 'id' => get_post_thumbnail_id() ];
                                \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); 
                                ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="appostli-list-content">
                        <h3 class="appostli-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ( 'yes' === $settings['show_meta'] ) : ?>
                            <div class="appostli-list-meta">
                                BY <?php the_author(); ?><br>
                                <?php echo get_the_date('d M Y'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
                            <div class="appostli-list-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endwhile; 
            echo '</div>'; // End Grid
            
            // Pagination Logic
            if ( $settings['pagination_type'] === 'numbers' ) {
                $total_pages = $query->max_num_pages;
                if ($total_pages > 1) {
                    echo '<div class="appostli-pagination-wrap appostli-page-numbers">';
                    echo paginate_links([
                        'base' => get_pagenum_link(1) . '%_%',
                        'format' => 'page/%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $total_pages,
                        'prev_text' => '< PREV',
                        'next_text' => 'NEXT >',
                    ]);
                    echo '</div>';
                }
            } elseif ( $settings['pagination_type'] === 'load_more' && $query->max_num_pages > 1 ) {
                ?>
                <div class="appostli-pagination-wrap">
                    <button class="appostli-load-more-btn" id="appostliLoadMore" 
                        data-page="1" data-max="<?php echo $query->max_num_pages; ?>" 
                        data-ppp="<?php echo $settings['posts_per_page']; ?>"
                        data-cat="<?php echo $current_cat; ?>" data-author="<?php echo $current_author; ?>">
                        [ LOAD MORE DATA ]
                    </button>
                </div>
                <script>
                jQuery(document).ready(function($) {
                    $('#appostliLoadMore').off('click').on('click', function() {
                        var btn = $(this);
                        var next_page = btn.data('page') + 1;
                        btn.text('[ FETCHING... ]');
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'appostli_load_more', page: next_page,
                            posts_per_page: btn.data('ppp'), cat: btn.data('cat'), author: btn.data('author')
                        }, function(res) {
                            if(res) {
                                $('#appostli-post-container').append(res);
                                btn.data('page', next_page).text('[ LOAD MORE DATA ]');
                                if (next_page >= btn.data('max')) { btn.text('[ END OF FILE ]').prop('disabled', true); }
                            }
                        });
                    });
                });
                </script>
                <?php
            }
        endif; wp_reset_postdata();
    }
}