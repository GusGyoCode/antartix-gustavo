<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Dd_Tag extends Basement_Shortcode {
    protected $enclosing = true;

    public function section_config( $config = array() ) {

        $config = array(
            'description' => __( 'The dd tag is used to describe a term/name in a description list.', BASEMENT_SHORTCODES_TEXTDOMAIN )
        );

        return $config;

    }

    public function render( $atts = array(), $content = '' ) {

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $dd_tag = $dom->appendChild( $dom->createElement( 'dd', '%cont' ) );

        return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );
    }
}
