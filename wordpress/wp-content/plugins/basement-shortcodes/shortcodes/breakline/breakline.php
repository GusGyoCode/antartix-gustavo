<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Breakline extends Basement_Shortcode {

	public function section_config( $config = array() ) {
		$config = array(
			'description' => __( 'Adds breakline without creating a paragraph.', BASEMENT_SHORTCODES_TEXTDOMAIN )
		);

		return $config;
	}

	public function render( $atts = array(), $content = '' ) {
		return '<br>';
	}
}
