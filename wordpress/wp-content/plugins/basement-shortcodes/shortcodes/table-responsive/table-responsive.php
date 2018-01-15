<?php
defined( 'ABSPATH' ) or die();

class Basement_Shortcode_Table_Responsive extends Basement_Shortcode {
	protected $enclosing = true;

	public function section_config( $config = array() ) {
		$config = array( 'description' => __( 'Creates responsive tables by wrapping any table.', BASEMENT_SHORTCODES_TEXTDOMAIN ) );

		return $config;
	}

	public function render( $atts = array(), $content = '' ) {

		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$div = $dom->appendChild( $dom->createElement( 'div', '%cont' ) );
		$div->setAttribute( 'class', esc_attr( 'table-responsive' ) );

		return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );
	}
}
