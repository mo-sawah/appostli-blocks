<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Appostli_News_Ticker extends \Elementor\Widget_Base {

    public function get_name() {
        return 'appostli_news_ticker';
    }

    public function get_title() {
        return esc_html__( 'Retro News Ticker', 'appostli-blocks' );
    }

    public function get_icon() {
        return 'eicon-post-slider';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    // Helper function to get categories
    private function get_post_categories() {
        $categories = get_terms( [ 'taxonomy' => 'category', 'hide_empty' => false ] );
        $options = [];
        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            foreach ( $categories as $category ) {
                $options[ $category->term_id ] = $category->name;
            }
        }
        return $options;
    }

    // Helper function to get tags
    private function get_post_tags() {
        $tags = get_terms( [ 'taxonomy' => 'post_tag', 'hide_empty' => false ] );
        $options = [];
        if ( ! is_wp_error( $tags ) && ! empty( $tags ) ) {
            foreach ( $tags as $tag ) {
                $options[ $tag->term_id ] = $tag->name;
            }
        }
        return $options;
    }

    protected function register_controls() {
        
        // --- QUERY SETTINGS ---
        $this->start_controls_section(
            'query_section',
            [
                'label' => esc_html__( 'Query', 'appostli-blocks' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__( 'Number of Posts', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
            ]
        );

        $this->add_control(
            'category_filter',
            [
                'label' => esc_html__( 'Categories', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_post_categories(),
            ]
        );

        $this->add_control(
            'tag_filter',
            [
                'label' => esc_html__( 'Tags', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_post_tags(),
            ]
        );

        $this->add_control(
            'unique_posts',
            [
                'label' => esc_html__( 'Make Posts Unique', 'appostli-blocks' ),
                'description' => esc_html__( 'Turn this on to prevent these posts from loading in other blocks on this page.', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'appostli-blocks' ),
                'label_off' => esc_html__( 'No', 'appostli-blocks' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // --- STYLE SETTINGS ---
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__( 'Ticker Style', 'appostli-blocks' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => esc_html__( 'Scroll Speed (Seconds)', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 5, 'max' => 100, 'step' => 1 ],
                ],
                'default' => [ 'size' => 20 ],
                'selectors' => [
                    '{{WRAPPER}} .appostli-ticker-track' => 'animation-duration: {{SIZE}}s;',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Text Color', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .appostli-ticker-item a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .appostli-ticker-item a',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        global $appostli_shown_posts;

        $args = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => $settings['posts_per_page'],
        ];

        if ( ! empty( $settings['category_filter'] ) ) {
            $args['category__in'] = $settings['category_filter'];
        }

        if ( ! empty( $settings['tag_filter'] ) ) {
            $args['tag__in'] = $settings['tag_filter'];
        }

        // Apply Unique Filter
        if ( 'yes' === $settings['unique_posts'] && ! empty( $appostli_shown_posts ) ) {
            $args['post__not_in'] = $appostli_shown_posts;
        }

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) :
            ?>
            <style>
                .appostli-ticker-wrapper { overflow: hidden; white-space: nowrap; width: 100%; display: flex; }
                .appostli-ticker-track { display: inline-block; white-space: nowrap; padding-left: 100%; animation: appostli-scroll linear infinite; }
                .appostli-ticker-item { display: inline-block; padding-right: 80px; }
                .appostli-ticker-item a { text-decoration: none; text-transform: uppercase; }
                .appostli-ticker-item a:hover { opacity: 0.8; }
                @keyframes appostli-scroll {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(-100%); }
                }
            </style>

            <div class="appostli-ticker-wrapper">
                <div class="appostli-ticker-track">
                    <?php
                    while ( $query->have_posts() ) : $query->the_post();
                        
                        // Add post to global array if Unique is checked
                        if ( 'yes' === $settings['unique_posts'] ) {
                            $appostli_shown_posts[] = get_the_ID();
                        }
                        ?>
                        <span class="appostli-ticker-item">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </span>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <?php
        endif;
    }
}