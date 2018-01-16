<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Dt_Tag extends Basement_Shortcode {
    protected $enclosing = true;

    public function section_config( $config = array() ) {

        $config = array(
            'description' => __( 'The dt tag defines a term/name in a description list.', BASEMENT_SHORTCODES_TEXTDOMAIN )
        );

        return $config;

    }

    public function render( $atts = array(), $content = '' ) {

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $dt_tag = $dom->appendChild( $dom->createElement( 'dt', '%cont' ) );

        return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );
    }
}
