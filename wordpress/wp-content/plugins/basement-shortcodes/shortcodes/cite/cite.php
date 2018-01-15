<?php
defined( 'ABSPATH' ) or die();

class Basement_Shortcode_Cite_Tag extends Basement_Shortcode {
	protected $enclosing = true;

	public function section_config( $config = array() ) {

		$config = array(
			'description' => __( 'The cite tag defines the title of a work (e.g. a book, a song etc.).', BASEMENT_SHORTCODES_TEXTDOMAIN )
		);

		return $config;

	}

	public function render( $atts = array(), $content = '' ) {


		$dom      = new DOMDocument( '1.0', 'UTF-8' );
		$cite_tag = $dom->appendChild( $dom->createElement( 'cite', '%cont' ) );
		$title    = $content;
		$cite_tag->setAttribute( 'title', esc_attr( $title ) );

		return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );

	}
}
