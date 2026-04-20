<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Appostli_Text_News_List extends \Elementor\Widget_Base {

    public function get_name() { return 'appostli_text_news_list'; }
    public function get_title() { return esc_html__( 'Retro Text News List', 'appostli-blocks' ); }
    public function get_icon() { return 'eicon-bullet-list'; }
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
        $this->add_control('posts_per_page', [ 'label' => 'Number of Posts', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 3 ]);
        $this->add_control('include_categories', [ 'label' => 'Include Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('exclude_categories', [ 'label' => 'Exclude Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('unique_posts', [ 'label' => 'Make Unique', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ]);
        $this->end_controls_section();

        // --- CONTENT LAYOUT ---
        $this->start_controls_section('layout_section', [ 'label' => 'Layout Options', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_control('show_meta', [ 'label' => 'Show Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('title_lines', [
            'label' => 'Max Title Lines', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 3,
            'selectors' => [ '{{WRAPPER}} .appostli-text-list-title' => 'display: -webkit-box; -webkit-line-clamp: {{VALUE}}; -webkit-box-orient: vertical; overflow: hidden;' ]
        ]);
        $this->end_controls_section();

        // --- STYLES ---
        $this->start_controls_section('style_section', [ 'label' => 'Styles', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]);
        
        $this->add_control('title_color', [ 'label' => 'Title Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}} .appostli-text-list-title a' => 'color: {{VALUE}};' ]]);
        $this->add_control('meta_color', [ 'label' => 'Meta Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .appostli-text-list-meta' => 'color: {{VALUE}};' ]]);
        $this->add_control('divider_color', [ 'label' => 'Divider Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#444444', 'selectors' => [ '{{WRAPPER}} .appostli-text-list-item' => 'border-bottom-color: {{VALUE}};' ]]);
        
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'label' => 'Title Typography', 'selector' => '{{WRAPPER}} .appostli-text-list-title' ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'meta_typography', 'label' => 'Meta Typography', 'selector' => '{{WRAPPER}} .appostli-text-list-meta' ]);
        
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        global $appostli_shown_posts;

        $args = [ 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $settings['posts_per_page'] ];
        if ( ! empty( $settings['include_categories'] ) ) { $args['category__in'] = $settings['include_categories']; }
        if ( ! empty( $settings['exclude_categories'] ) ) { $args['category__not_in'] = $settings['exclude_categories']; }
        if ( 'yes' === $settings['unique_posts'] && ! empty( $appostli_shown_posts ) ) { $args['post__not_in'] = $appostli_shown_posts; }

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) :
            echo '<style>
                .appostli-text-list-item { padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #444; }
                .appostli-text-list-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
                .appostli-text-list-title { margin: 0 0 10px 0; }
                .appostli-text-list-title a { text-decoration: none; }
                .appostli-text-list-meta { text-transform: uppercase; display: flex; align-items: center; gap: 8px; }
            </style>';

            echo '<div class="appostli-text-list-wrapper">';
            while ( $query->have_posts() ) : $query->the_post();
                if ( 'yes' === $settings['unique_posts'] ) { $appostli_shown_posts[] = get_the_ID(); }
                ?>
                <div class="appostli-text-list-item">
                    <h3 class="appostli-text-list-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if ( 'yes' === $settings['show_meta'] ) : ?>
                        <div class="appostli-text-list-meta">
                            <span>BY <?php the_author(); ?></span>
                            <span>&bull;</span>
                            <span>
                                <?php 
                                // Calculate time difference for the "2 HOURS AGO" look
                                $time_diff = human_time_diff( get_the_time('U'), current_time('timestamp') );
                                if ( strpos( $time_diff, 'day' ) !== false && intval( $time_diff ) === 1 ) {
                                    echo 'YESTERDAY';
                                } elseif ( intval( $time_diff ) > 7 && strpos( $time_diff, 'day' ) !== false ) {
                                    // Fallback to standard date if it's older than a week
                                    echo get_the_date('d F Y');
                                } else {
                                    echo $time_diff . ' AGO';
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            endwhile; wp_reset_postdata();
            echo '</div>';
        endif;
    }
}