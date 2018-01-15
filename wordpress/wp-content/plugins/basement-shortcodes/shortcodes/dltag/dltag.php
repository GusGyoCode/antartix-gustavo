<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Dl_Tag extends Basement_Shortcode {
    protected $enclosing = true;

    public function section_config( $config = array() ) {

        $config = array(
            'description' => __( 'The dl tag defines a description list.', BASEMENT_SHORTCODES_TEXTDOMAIN )
        );

        return $config;

    }

    public function render( $atts = array(), $content = '' ) {

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $dl_tag = $dom->appendChild( $dom->createElement( 'dl', '%cont' ) );
        $dl_tag->setAttribute('class','dl-horizontal');

        return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );
    }
}
