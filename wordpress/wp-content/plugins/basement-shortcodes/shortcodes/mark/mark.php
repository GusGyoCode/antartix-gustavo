<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Mark extends Basement_Shortcode {
    protected $enclosing = true;

    public function section_config( $config = array() ) {

        $config = array(
            'description' => __( 'Use the mark tag if you want to highlight parts of your text.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
            'blocks' => array(
                array(
                    'type' => 'colorpicker',
                    'param' => 'text_color',
                    'title' => __( 'Color', BASEMENT_SHORTCODES_TEXTDOMAIN ),
                    'description' => __( 'Set the color to mark', BASEMENT_SHORTCODES_TEXTDOMAIN )
                ),
                array(
                    'type' => 'colorpicker',
                    'param' => 'bgcolor',
                    'title' => __( 'Background color', BASEMENT_SHORTCODES_TEXTDOMAIN ),
                    'description' => __( 'Set separator background color. Leave empty to keep row background transparent.', BASEMENT_SHORTCODES_TEXTDOMAIN )
                )
            )
        );

        return $config;

    }

    public function render( $atts = array(), $content = '' ) {
        extract( $atts = wp_parse_args( $atts, array(
            'bgcolor' => '',
            'text_color' => ''
        ) ) );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $mark = $dom->appendChild( $dom->createElement( 'mark', $content ) );

        $style = '';

        if($bgcolor) {
            $style .= 'background-color: '.$bgcolor.';';
        }
        if($text_color) {
            $style .= 'color: '.$text_color.';';
        }
        if(!empty($style)) {
	        $mark->setAttribute( 'style', $style );
        }

        return $dom->saveHTML();
    }
}
