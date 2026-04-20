<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Appostli_Featured_News_Large extends \Elementor\Widget_Base {

    public function get_name() { return 'appostli_featured_news_large'; }
    public function get_title() { return esc_html__( 'Retro Featured News (Large)', 'appostli-blocks' ); }
    public function get_icon() { return 'eicon-image-box'; }
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
        $this->add_control('posts_per_page', [ 'label' => 'Number of Posts', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 1 ]);
        $this->add_control('include_categories', [ 'label' => 'Include Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('exclude_categories', [ 'label' => 'Exclude Categories', 'type' => \Elementor\Controls_Manager::SELECT2, 'multiple' => true, 'options' => $this->get_terms_list('category') ]);
        $this->add_control('unique_posts', [ 'label' => 'Make Unique', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ]);
        $this->end_controls_section();

        // --- CONTENT LAYOUT ---
        $this->start_controls_section('layout_section', [ 'label' => 'Layout Options', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ]);
        $this->add_group_control(\Elementor\Group_Control_Image_Size::get_type(), [ 'name' => 'image', 'default' => 'large', 'separator' => 'none' ]);
        $this->add_control('show_meta', [ 'label' => 'Show Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_excerpt', [ 'label' => 'Show Excerpt', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        
        $this->add_control('title_lines', [
            'label' => 'Max Title Lines', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 3,
            'selectors' => [ '{{WRAPPER}} .appostli-large-title' => 'display: -webkit-box; -webkit-line-clamp: {{VALUE}}; -webkit-box-orient: vertical; overflow: hidden;' ]
        ]);
        $this->add_control('excerpt_lines', [
            'label' => 'Max Excerpt Lines', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 8,
            'selectors' => [ '{{WRAPPER}} .appostli-large-excerpt' => 'display: -webkit-box; -webkit-line-clamp: {{VALUE}}; -webkit-box-orient: vertical; overflow: hidden;' ]
        ]);
        $this->end_controls_section();

        // --- STYLES ---
        $this->start_controls_section('style_section', [ 'label' => 'Styles', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_control('title_color', [ 'label' => 'Title Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#5CBA47', 'selectors' => [ '{{WRAPPER}} .appostli-large-title a' => 'color: {{VALUE}};' ]]);
        $this->add_control('meta_color', [ 'label' => 'Meta Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#D32A2A', 'selectors' => [ '{{WRAPPER}} .appostli-large-meta' => 'color: {{VALUE}};' ]]);
        $this->add_control('excerpt_color', [ 'label' => 'Excerpt Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => [ '{{WRAPPER}} .appostli-large-excerpt' => 'color: {{VALUE}};' ]]);
        
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'label' => 'Title Typography', 'selector' => '{{WRAPPER}} .appostli-large-title' ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'meta_typography', 'label' => 'Meta Typography', 'selector' => '{{WRAPPER}} .appostli-large-meta' ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [ 'name' => 'excerpt_typography', 'label' => 'Excerpt Typography', 'selector' => '{{WRAPPER}} .appostli-large-excerpt' ]);
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
                .appostli-large-item { margin-bottom: 40px; display: flex; flex-direction: column; }
                .appostli-large-image { width: 100%; margin-bottom: 15px; }
                .appostli-large-image img { width: 100%; height: auto; display: block; }
                .appostli-large-title { margin: 0 0 10px 0; text-transform: uppercase; }
                .appostli-large-title a { text-decoration: none; }
                .appostli-large-meta { text-transform: uppercase; margin-bottom: 15px; }
                .appostli-large-excerpt { text-transform: uppercase; }
            </style>';

            echo '<div class="appostli-large-wrapper">';
            while ( $query->have_posts() ) : $query->the_post();
                if ( 'yes' === $settings['unique_posts'] ) { $appostli_shown_posts[] = get_the_ID(); }
                ?>
                <div class="appostli-large-item">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="appostli-large-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', get_post_thumbnail_id() ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="appostli-large-content">
                        <h2 class="appostli-large-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if ( 'yes' === $settings['show_meta'] ) : ?>
                            <div class="appostli-large-meta">
                                BY <?php the_author(); ?><br>
                                <?php echo get_the_date('d F Y'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
                            <div class="appostli-large-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 40 ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endwhile; wp_reset_postdata();
            echo '</div>';
        endif;
    }
}