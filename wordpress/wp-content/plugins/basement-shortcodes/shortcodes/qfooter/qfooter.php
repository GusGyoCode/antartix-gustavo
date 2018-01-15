<?php
defined( 'ABSPATH' ) or die();

class Basement_Shortcode_QFooter_Tag extends Basement_Shortcode {
	protected $enclosing = true;

	public function section_config( $config = array() ) {

		$config = array(
			'description' => __( 'Add a footer for identifying the source.', BASEMENT_SHORTCODES_TEXTDOMAIN )
		);

		return $config;

	}

	public function render( $atts = array(), $content = '' ) {

		$dom        = new DOMDocument( '1.0', 'UTF-8' );
		$footer_tag = $dom->appendChild( $dom->createElement( 'footer', '%cont' ) );

		return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );
	}
}
