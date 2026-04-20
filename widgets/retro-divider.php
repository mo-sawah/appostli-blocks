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
                'default' => [ 'unit' => 'px', 'size' => 4 ], 
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Setup values
        $color = !empty($settings['color']) ? $settings['color'] : '#FF00FF';
        $size = !empty($settings['pixel_size']['size']) ? (float)$settings['pixel_size']['size'] : 4;
        $unit = !empty($settings['pixel_size']['unit']) ? $settings['pixel_size']['unit'] : 'px';
        
        // This calculates a tiny gap between dots so they don't merge into a line!
        $gap = $size * 0.15; 
        $dot = $size * 0.70; 
        
        // The pattern repeats every 2 columns, which allows the left/right arms to be perfectly shared
        $pattern_width = $size * 2;
        $pattern_height = $size * 3;
        
        // Unique ID so multiple dividers on one page don't break
        $svg_id = 'retro-cross-' . $this->get_id();
        ?>
        <div class="appostli-retro-divider-wrapper" style="width: 100%; height: <?php echo $pattern_height . $unit; ?>; line-height: 0;">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="<?php echo $svg_id; ?>" x="0" y="0" width="<?php echo $pattern_width; ?>" height="<?php echo $pattern_height; ?>" patternUnits="userSpaceOnUse">
                        <rect x="<?php echo 0 + $gap; ?>" y="<?php echo $size + $gap; ?>" width="<?php echo $dot; ?>" height="<?php echo $dot; ?>" fill="<?php echo esc_attr($color); ?>" />
                        
                        <rect x="<?php echo $size + $gap; ?>" y="<?php echo 0 + $gap; ?>" width="<?php echo $dot; ?>" height="<?php echo $dot; ?>" fill="<?php echo esc_attr($color); ?>" />
                        <rect x="<?php echo $size + $gap; ?>" y="<?php echo $size + $gap; ?>" width="<?php echo $dot; ?>" height="<?php echo $dot; ?>" fill="<?php echo esc_attr($color); ?>" />
                        <rect x="<?php echo $size + $gap; ?>" y="<?php echo ($size * 2) + $gap; ?>" width="<?php echo $dot; ?>" height="<?php echo $dot; ?>" fill="<?php echo esc_attr($color); ?>" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#<?php echo $svg_id; ?>)" />
            </svg>
        </div>
        <?php
    }
}