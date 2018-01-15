<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Reset_List extends Basement_Shortcode {
    protected $enclosing = true;

    public function section_config( $config = array() ) {

        $config = array(
            'description' => __( 'Use to remove the list item marker.', BASEMENT_SHORTCODES_TEXTDOMAIN )
        );

        return $config;

    }

    public function render( $atts = array(), $content = '' ) {

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $classes = array('reset-list');

        // Filter: basement_shortcode_render_link_{ $this->name }_container
        $container = apply_filters( $this->textdomain . '_render_link_' . $this->name . '_container', $dom );
        //$content = wpautop(trim($content));
        $reset_tag = $dom->appendChild( $dom->createElement( 'div', '%cont' ) );

	    $reset_tag->setAttribute( 'class', implode( ' ', apply_filters( 'basement_reset_list', $classes ) ) );

        return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );
    }
}
