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
                'selectors' => [
                    // Save the chosen color as a CSS variable on the wrapper
                    '{{WRAPPER}}' => '--retro-divider-color: {{VALUE}};',
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
                    'px' => [ 'min' => 2, 'max' => 20, 'step' => 1 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 4 ], 
                'selectors' => [
                    // Draw the overlapping ++++ pattern using the CSS variable
                    '{{WRAPPER}} .retro-cross-line' => '
                        height: calc({{SIZE}}{{UNIT}} * 3);
                        background-image: 
                            linear-gradient(90deg, transparent 50%, var(--retro-divider-color) 50%),
                            linear-gradient(90deg, var(--retro-divider-color) 100%, transparent 100%),
                            linear-gradient(90deg, transparent 50%, var(--retro-divider-color) 50%);
                        background-size: 
                            calc({{SIZE}}{{UNIT}} * 2) {{SIZE}}{{UNIT}},
                            calc({{SIZE}}{{UNIT}} * 2) {{SIZE}}{{UNIT}},
                            calc({{SIZE}}{{UNIT}} * 2) {{SIZE}}{{UNIT}};
                        background-position: 
                            0 0,
                            0 {{SIZE}}{{UNIT}},
                            0 calc({{SIZE}}{{UNIT}} * 2);
                        background-repeat: repeat-x;
                        image-rendering: pixelated;
                    ',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        ?>
        <div class="appostli-retro-divider-wrapper" style="width: 100%;">
            <div class="retro-cross-line"></div>
        </div>
        <?php
    }
}