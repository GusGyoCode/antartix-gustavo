<?php
defined('ABSPATH') or die();

class Basement_Shortcode_Nonbreakablespace extends Basement_Shortcode {

	public function section_config( $config = array() ) {
		$config = array(
			'description' => __( 'Adds non-breakable space.', BASEMENT_SHORTCODES_TEXTDOMAIN )
		);

		return $config;
	}

	public function render( $atts = array(), $content = '' ) {
		return '&nbsp;';
	}
}
