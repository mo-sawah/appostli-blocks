<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Appostli_Retro_Divider extends \Elementor\Widget_Base {

    public function get_name() {
        return 'appostli_retro_divider';
    }

    public function get_title() {
        return esc_html__( 'Retro Pixel Divider', 'appostli-blocks' );
    }

    public function get_icon() {
        return 'eicon-divider';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Divider Settings', 'appostli-blocks' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'color',
            [
                'label' => esc_html__( 'Pixel Color', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FF00FF',
            ]
        );

        $this->add_control(
            'pixel_size',
            [
                'label' => esc_html__( 'Pixel Size', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [ 'min' => 2, 'max' => 20, 'step' => 1 ],
                ],
                // Defaulting to 4px to perfectly match the chunkiness of your mockup
                'default' => [ 'unit' => 'px', 'size' => 4 ], 
                'selectors' => [
                    // Uses exact pixel repeating gradients so they never stretch horizontally
                    '{{WRAPPER}} .retro-line' => 'height: {{SIZE}}{{UNIT}}; background-image: repeating-linear-gradient(to right, {{color.VALUE}} 0, {{color.VALUE}} {{SIZE}}{{UNIT}}, transparent {{SIZE}}{{UNIT}}, transparent calc({{SIZE}}{{UNIT}} * 2));',
                    '{{WRAPPER}} .retro-spacer' => 'height: {{SIZE}}{{UNIT}};',
                    // Offsets the bottom line by 1 block to create the checkerboard effect
                    '{{WRAPPER}} .retro-line-bottom' => 'background-position: {{SIZE}}{{UNIT}} 0;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        ?>
        <div class="appostli-retro-divider-wrapper" style="width: 100%;">
            <div class="retro-line"></div>
            <div class="retro-spacer"></div>
            <div class="retro-line retro-line-bottom"></div>
        </div>
        <?php
    }
}