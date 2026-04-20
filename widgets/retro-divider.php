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
                'default' => '#FF00FF', // The pink from your mockup
                'selectors' => [
                    '{{WRAPPER}} .retro-pixel-line' => 'background-image: linear-gradient(to right, {{VALUE}} 50%, transparent 50%);',
                ],
            ]
        );

        $this->add_control(
            'pixel_size',
            [
                'label' => esc_html__( 'Pixel Size', 'appostli-blocks' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [ 'min' => 2, 'max' => 20, 'step' => 2 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 6 ],
                'selectors' => [
                    '{{WRAPPER}} .retro-pixel-line' => 'height: {{SIZE}}{{UNIT}}; background-size: calc({{SIZE}}{{UNIT}} * 2) 100%;',
                    '{{WRAPPER}} .retro-pixel-spacer' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Creates the double-row checkerboard style from the mockup
        ?>
        <div class="appostli-retro-divider-wrapper" style="width: 100%;">
            <div class="retro-pixel-line"></div>
            <div class="retro-pixel-spacer"></div>
            <div class="retro-pixel-line" style="background-position: 100% 0;"></div>
        </div>
        <?php
    }
}